<?php

namespace App\Http\Controllers;

use App\Http\Requests\CabinetUpdateAccountRequest;
use App\Http\Requests\CabinetUpdateArtistRequest;
use App\Http\Requests\CabinetUpdateCityRequest;
use App\Models\Booking;
use App\Models\Artist;
use App\Models\City;
use App\Models\EventSeat;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class CabinetController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        $user->load(['city', 'bookings.event.venue']);
        $cities = City::orderBy('sort_order')->orderBy('name')->get();

        $bookings = $user->bookings()
            ->with(['event' => fn ($q) => $q->withTrashed(), 'event.venue'])
            ->latest('booked_at')
            ->get();

        return Inertia::render('Cabinet/Index', [
            'user' => $user,
            'bookings' => $bookings,
            'cities' => $cities,
        ]);
    }

    public function favorites(): Response
    {
        $events = auth()->user()
            ->favoriteEvents()
            ->with(['venue'])
            ->orderBy('event_favorites.created_at', 'desc')
            ->get();

        return Inertia::render('Cabinet/Favorites', ['events' => $events]);
    }

    public function updateCity(CabinetUpdateCityRequest $request): RedirectResponse
    {
        auth()->user()->update(['city_id' => $request->validated()['city_id']]);

        return back()->with('status', 'Город обновлён.');
    }

    public function showBooking(Booking $booking): Response
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        $booking->load([
            'event' => fn ($q) => $q->withTrashed(),
            'event.venue',
            'items.section',
            'addons.eventAddon',
        ]);

        $eventUrl = $booking->event
            ? route('events.show', $booking->event)
            : route('cabinet.index');
        return Inertia::render('Cabinet/BookingShow', [
            'booking' => $booking,
            'qrUrl' => 'https://quickchart.io/qr?text=' . urlencode($eventUrl) . '&size=220',
        ]);
    }

    public function refundBooking(Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        if ($booking->status === 'refunded') {
            return back()->with('status', 'Билет уже возвращён.');
        }

        DB::transaction(function () use ($booking) {
            $booking->load(['items.seat', 'event' => fn ($q) => $q->withTrashed(), 'user']);

            $seatIds = $booking->items
                ->pluck('event_seat_id')
                ->filter()
                ->all();

            if ($seatIds) {
                EventSeat::whereIn('id', $seatIds)->update(['status' => 'available']);
            }

            $booking->update([
                'status' => 'refunded',
                'notes' => trim(($booking->notes ? $booking->notes . PHP_EOL : '') . 'Возвращён пользователем ' . now()->format('d.m.Y H:i')),
            ]);

            if ($booking->user && $booking->user->notify_push) {
                $eventTitle = $booking->event?->title ?? 'Мероприятие';
                Notification::create([
                    'user_id' => $booking->user_id,
                    'type' => 'ticket_refunded',
                    'title' => 'Билет возвращён',
                    'body' => 'Билет по мероприятию «' . $eventTitle . '» успешно возвращён.',
                    'url' => route('cabinet.index'),
                    'data' => ['booking_id' => $booking->id, 'event_id' => $booking->event_id],
                ]);
            }
        });

        return redirect()->route('cabinet.bookings.show', $booking)->with('status', 'Билет успешно возвращён.');
    }

    public function account(): Response
    {
        $user = auth()->user();
        $user->makeVisible(['notify_email', 'notify_push']);
        return Inertia::render('Cabinet/Account', ['user' => $user]);
    }

    public function updateAccount(CabinetUpdateAccountRequest $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (isset($validated['notify_email'])) {
            $user->notify_email = (bool) $validated['notify_email'];
        }
        if (isset($validated['notify_push'])) {
            $user->notify_push = (bool) $validated['notify_push'];
        }
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return back()->with('status', 'Данные аккаунта сохранены.');
    }

    public function artistProfile(): Response
    {
        $user = auth()->user();
        if (! $user->isArtist() || ! $user->artist_id) {
            return redirect()->route('cabinet.index')->with('status', 'Профиль артиста доступен только привязанным артистам.');
        }

        $artist = $user->artist;
        $artist->loadCount(['events' => fn ($q) => $q->published()]);
        $upcomingCount = $artist->events()->published()->where('start_at', '>=', now())->count();
        $totalViews = \App\Models\EventView::whereIn('event_id', $artist->events()->pluck('id'))->count();
        $totalBookings = \App\Models\Booking::whereIn('event_id', $artist->events()->pluck('id'))->where('status', '!=', 'refunded')->count();

        return Inertia::render('Cabinet/ArtistProfile', [
            'artist' => $artist,
            'stats' => [
                'events_count' => $artist->events_count ?? 0,
                'upcoming_count' => $upcomingCount,
                'total_views' => $totalViews,
                'total_bookings' => $totalBookings,
            ],
        ]);
    }

    public function updateArtistProfile(CabinetUpdateArtistRequest $request): RedirectResponse
    {
        $artist = auth()->user()->artist;
        if (! $artist) {
            return redirect()->route('cabinet.index');
        }

        $data = $request->validated();
        $links = [];
        if (! blank($data['links_json'] ?? null)) {
            $decoded = json_decode($data['links_json'], true);
            $links = is_array($decoded) ? $decoded : [];
        }
        $data['links'] = $links ?: null;
        unset($data['links_json']);

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']) . '-' . \Illuminate\Support\Str::random(4);
        }

        if ($request->hasFile('photo')) {
            if ($artist->photo) {
                Storage::disk('public')->delete($artist->photo);
            }
            $data['photo'] = $request->file('photo')->store('artists', 'public');
        }

        $artist->update($data);

        return back()->with('status', 'Профиль артиста обновлён.');
    }
}

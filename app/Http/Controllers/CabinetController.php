<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\City;
use App\Models\EventSeat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CabinetController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $user->load(['city', 'bookings.event.venue']);
        $cities = City::orderBy('sort_order')->orderBy('name')->get();

        return view('cabinet.index', [
            'user' => $user,
            'bookings' => $user->bookings()->with('event.venue')->latest('booked_at')->get(),
            'cities' => $cities,
        ]);
    }

    public function favorites(): View
    {
        $events = auth()->user()
            ->favoriteEvents()
            ->with(['venue'])
            ->orderByPivot('created_at', 'desc')
            ->get();

        return view('cabinet.favorites', ['events' => $events]);
    }

    public function updateCity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'city_id' => ['required', 'exists:cities,id'],
        ]);

        auth()->user()->update(['city_id' => $validated['city_id']]);

        return back()->with('status', 'Город обновлён.');
    }

    public function showBooking(Booking $booking): View
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        $booking->load([
            'event.venue',
            'items.section',
            'addons.eventAddon',
        ]);

        return view('cabinet.booking-show', [
            'booking' => $booking,
            'qrUrl' => 'https://quickchart.io/qr?text=' . urlencode(route('cabinet.bookings.show', $booking) . '|' . $booking->reference) . '&size=220',
        ]);
    }

    public function refundBooking(Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        if ($booking->status === 'refunded') {
            return back()->with('status', 'Билет уже возвращён.');
        }

        DB::transaction(function () use ($booking) {
            $booking->load('items.seat');

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
        });

        return redirect()->route('cabinet.bookings.show', $booking)->with('status', 'Билет успешно возвращён.');
    }

    public function account(): View
    {
        return view('cabinet.account', ['user' => auth()->user()]);
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'avatar' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov', 'max:51200'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
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
}

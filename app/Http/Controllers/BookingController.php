<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\BookingItem;
use App\Models\Event;
use App\Models\EventSeat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'tickets_payload' => ['required', 'string'],
            'addons_payload' => ['nullable', 'string'],
            'test_mode' => ['nullable', 'boolean'],
        ]);
        $testMode = $request->boolean('test_mode');

        $ticketsPayload = json_decode($validated['tickets_payload'], true);

        if (! is_array($ticketsPayload) || empty($ticketsPayload)) {
            throw ValidationException::withMessages([
                'tickets_payload' => 'Нужно выбрать хотя бы один билет',
            ]);
        }

        $booking = DB::transaction(function () use ($event, $validated, $ticketsPayload, $testMode) {
            $clampedTickets = collect($ticketsPayload)
                ->map(function ($item) {
                    return [
                        'section_id' => $item['section_id'] ?? null,
                        'seat_id' => $item['seat_id'] ?? null,
                        'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
                    ];
                })
                ->filter(fn ($item) => $item['section_id']);

            if ($clampedTickets->isEmpty()) {
                throw ValidationException::withMessages([
                    'tickets_payload' => 'Не удалось разобрать выбранные места',
                ]);
            }

            $sections = $event->sections()->with(['seats' => function ($query) {
                $query->select(['id', 'event_section_id', 'label', 'status', 'price', 'row_number', 'col_number']);
            }])->get()->keyBy('id');

            $confirmedItems = [];
            $totalAmount = 0;
            $totalTickets = 0;

            foreach ($clampedTickets as $ticket) {
                $section = $sections->get($ticket['section_id']);

                if (! $section) {
                    throw ValidationException::withMessages([
                        'tickets_payload' => 'Неверная зона для бронирования',
                    ]);
                }

                if ($section->seating_mode === 'seated' && $ticket['seat_id']) {
                    $seat = EventSeat::where('event_section_id', $section->id)
                        ->where('id', $ticket['seat_id'])
                        ->lockForUpdate()
                        ->first();

                    if (! $seat || $seat->status !== 'available') {
                        throw ValidationException::withMessages([
                            'tickets_payload' => "Место {$ticket['seat_id']} уже занято",
                        ]);
                    }

                    $price = $seat->price ?: $section->price;
                    $confirmedItems[] = [
                        'event_section_id' => $section->id,
                        'event_seat_id' => $seat->id,
                        'seat_label' => $seat->label,
                        'price' => $price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $totalAmount += $price;
                    $totalTickets++;

                    $seat->update(['status' => 'sold']);
                } else {
                    $quantity = $ticket['quantity'];
                    $existingStanding = BookingItem::where('event_section_id', $section->id)
                        ->whereHas('booking', function ($query) {
                            $query->where('status', '!=', 'refunded');
                        })
                        ->whereNull('event_seat_id')
                        ->lockForUpdate()
                        ->count();

                    if ($section->capacity && ($existingStanding + $quantity) > $section->capacity) {
                        throw ValidationException::withMessages([
                            'tickets_payload' => "В зоне {$section->name} осталось только " . max(0, $section->capacity - $existingStanding) . ' мест',
                        ]);
                    }

                    for ($i = 0; $i < $quantity; $i++) {
                        $confirmedItems[] = [
                            'event_section_id' => $section->id,
                            'event_seat_id' => null,
                            'seat_label' => $section->name . ' #' . ($existingStanding + $i + 1),
                            'price' => $section->price,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    $totalAmount += $section->price * $quantity;
                    $totalTickets += $quantity;
                }
            }

            if (empty($confirmedItems)) {
                throw ValidationException::withMessages([
                    'tickets_payload' => 'Не удалось подобрать свободные места',
                ]);
            }

            $booking = Booking::create([
                'user_id' => auth()->id(),
                'event_id' => $event->id,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'tickets_count' => $totalTickets,
                'total_amount' => $totalAmount,
                'status' => $testMode ? 'confirmed' : 'pending',
                'payment_method' => $testMode ? 'test' : null,
                'booked_at' => now(),
            ]);

            $itemsToInsert = array_map(function ($item) use ($booking) {
                $item['booking_id'] = $booking->id;
                return $item;
            }, $confirmedItems);

            BookingItem::insert($itemsToInsert);

            $addonsPayload = json_decode($validated['addons_payload'] ?? '[]', true);
            if (is_array($addonsPayload)) {
                $eventAddons = $event->addons()->get()->keyBy('id');
                foreach ($addonsPayload as $item) {
                    $addonId = (int) ($item['addon_id'] ?? 0);
                    $qty = max(0, (int) ($item['quantity'] ?? 0));
                    if ($qty <= 0) {
                        continue;
                    }
                    $addon = $eventAddons->get($addonId);
                    if (! $addon) {
                        continue;
                    }
                    $addonTotal = $addon->price * $qty;
                    $totalAmount += $addonTotal;
                    BookingAddon::create([
                        'booking_id' => $booking->id,
                        'event_addon_id' => $addon->id,
                        'quantity' => $qty,
                        'price' => $addon->price,
                    ]);
                }
                $booking->update(['total_amount' => $totalAmount]);
            }

            return $booking;
        });

        if ($testMode) {
            return redirect()->route('cabinet.index')->with('status', "Билет оформлен в тестовом режиме. Номер заказа: {$booking->reference}. Он отображается в «Мои билеты».");
        }
        return redirect()->route('events.show', $event)->with('status', "Бронирование принято. Номер {$booking->reference}");
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Console\Command;

class NotifyPastEvents extends Command
{
    protected $signature = 'notifications:past-events';
    protected $description = 'Notify users with bookings that the event has passed';

    public function handle(): int
    {
        $from = now()->subDay();
        $to = now();

        $bookings = Booking::with(['event', 'user'])
            ->whereHas('event', function ($q) use ($from, $to) {
                $q->where('end_at', '>=', $from)->where('end_at', '<', $to);
            })
            ->where('status', '!=', 'refunded')
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            if (! $booking->user || ! $booking->event) {
                continue;
            }
            if (! $booking->user->notify_push) {
                continue;
            }

            $exists = Notification::where('user_id', $booking->user_id)
                ->where('type', 'event_passed')
                ->where('data->event_id', $booking->event_id)
                ->exists();

            if ($exists) {
                continue;
            }

            Notification::create([
                'user_id' => $booking->user_id,
                'type' => 'event_passed',
                'title' => 'Мероприятие прошло',
                'body' => 'Мероприятие «' . $booking->event->title . '» состоялось. Спасибо, что были с нами!',
                'url' => route('events.show', $booking->event),
                'data' => ['event_id' => $booking->event_id, 'booking_id' => $booking->id],
            ]);
            $count++;
        }

        $this->info("Sent {$count} past event notifications.");

        return 0;
    }
}

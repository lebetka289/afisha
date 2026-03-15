<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    protected $signature = 'notifications:event-reminders';
    protected $description = 'Send notifications to users who have bookings for events happening today';

    public function handle(): int
    {
        $today = now()->startOfDay();
        $tomorrow = $today->copy()->addDay();

        $bookings = Booking::with(['event', 'user'])
            ->whereHas('event', function ($q) use ($today, $tomorrow) {
                $q->where('start_at', '>=', $today)
                    ->where('start_at', '<', $tomorrow);
            })
            ->where('status', '!=', 'cancelled')
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            if (!$booking->user || !$booking->event) continue;
            if (!$booking->user->notify_push) continue;

            $exists = Notification::where('user_id', $booking->user_id)
                ->where('type', 'event_today')
                ->where('data->event_id', $booking->event_id)
                ->whereDate('created_at', $today)
                ->exists();

            if ($exists) continue;

            Notification::create([
                'user_id' => $booking->user_id,
                'type' => 'event_today',
                'title' => 'Сегодня мероприятие!',
                'body' => "Сегодня состоится «{$booking->event->title}». Не забудьте!",
                'url' => route('events.show', $booking->event),
                'data' => ['event_id' => $booking->event_id, 'booking_id' => $booking->id],
            ]);
            $count++;
        }

        $this->info("Sent {$count} event reminder notifications.");
        return 0;
    }
}

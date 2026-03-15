<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Notification;
use Illuminate\Console\Command;

class NotifyNewArtistEvents extends Command
{
    protected $signature = 'notifications:new-artist-events';
    protected $description = 'Notify users who favorited an artist about new events added in the last 24 hours';

    public function handle(): int
    {
        $since = now()->subDay();

        $events = Event::with('artist.favoritedBy')
            ->whereNotNull('artist_id')
            ->where('status', 'published')
            ->where('created_at', '>=', $since)
            ->get();

        $count = 0;
        foreach ($events as $event) {
            if (!$event->artist || !$event->artist->favoritedBy) continue;

            foreach ($event->artist->favoritedBy as $user) {
                if (!$user->notify_push) continue;

                $exists = Notification::where('user_id', $user->id)
                    ->where('type', 'new_artist_event')
                    ->where('data->event_id', $event->id)
                    ->exists();

                if ($exists) continue;

                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'new_artist_event',
                    'title' => "Новый концерт: {$event->artist->name}",
                    'body' => "Добавлен концерт «{$event->title}»",
                    'url' => route('events.show', $event),
                    'data' => ['event_id' => $event->id, 'artist_id' => $event->artist_id],
                ]);
                $count++;
            }
        }

        $this->info("Sent {$count} new artist event notifications.");
        return 0;
    }
}

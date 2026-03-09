<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventFavoriteController extends Controller
{
    public function toggle(Request $request, Event $event): JsonResponse
    {
        if (! auth()->check()) {
            return response()->json(['ok' => false, 'message' => 'Войдите в аккаунт'], 401);
        }

        $user = auth()->user();
        $attached = $user->favoriteEvents()->where('event_id', $event->id)->exists();

        if ($attached) {
            $user->favoriteEvents()->detach($event->id);
            return response()->json(['ok' => true, 'favorited' => false]);
        }

        $user->favoriteEvents()->attach($event->id);
        return response()->json(['ok' => true, 'favorited' => true]);
    }
}

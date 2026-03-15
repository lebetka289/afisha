<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\JsonResponse;

class ArtistFavoriteController extends Controller
{
    public function toggle(Artist $artist): JsonResponse
    {
        if (! auth()->check()) {
            return response()->json(['ok' => false, 'message' => 'Войдите в аккаунт'], 401);
        }

        $user = auth()->user();
        $attached = $user->favoriteArtists()->where('artist_id', $artist->id)->exists();

        if ($attached) {
            $user->favoriteArtists()->detach($artist->id);
            return response()->json(['ok' => true, 'favorited' => false]);
        }

        $user->favoriteArtists()->attach($artist->id);
        return response()->json(['ok' => true, 'favorited' => true]);
    }
}


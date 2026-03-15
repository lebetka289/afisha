<?php

use App\Http\Controllers\Admin\ArtistController as AdminArtistController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\VenueController as AdminVenueController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\ArtistFavoriteController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventFavoriteController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/media/{path}', [MediaController::class, 'show'])->where('path', '.*')->name('media.show');
Route::get('/search/suggest', [EventController::class, 'suggest'])->name('events.suggest');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/recommendations', [RecommendationController::class, 'index'])
    ->middleware('auth')
    ->name('recommendations.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/{event:slug}/book', [BookingController::class, 'store'])->name('events.book')->middleware('auth');
Route::post('/events/{event:slug}/favorite', [EventFavoriteController::class, 'toggle'])->name('events.favorite.toggle');

Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
Route::get('/artists/{artist:slug}', [ArtistController::class, 'show'])->name('artists.show');
Route::post('/artists/{artist:slug}/favorite', [ArtistFavoriteController::class, 'toggle'])
    ->middleware('auth')
    ->name('artists.favorite.toggle');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->prefix('cabinet')->name('cabinet.')->group(function () {
    Route::get('/', [CabinetController::class, 'index'])->name('index');
    Route::get('/favorites', [CabinetController::class, 'favorites'])->name('favorites');
    Route::get('/bookings/{booking}', [CabinetController::class, 'showBooking'])->name('bookings.show');
    Route::post('/bookings/{booking}/refund', [CabinetController::class, 'refundBooking'])->name('bookings.refund');
    Route::post('/city', [CabinetController::class, 'updateCity'])->name('city.update');
    Route::get('/account', [CabinetController::class, 'account'])->name('account');
    Route::put('/account', [CabinetController::class, 'updateAccount'])->name('account.update');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/events');
    Route::resource('artists', AdminArtistController::class);
    Route::resource('events', AdminEventController::class);
    Route::resource('venues', AdminVenueController::class);
});

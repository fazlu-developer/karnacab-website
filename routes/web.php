<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PartnerRegisterController;
use App\Http\Controllers\PlacesProxyController;
use App\Http\Controllers\QuotePreviewController;
use App\Http\Controllers\RoutePreviewController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/route', RoutePreviewController::class)->name('route');
Route::get('/places/suggest', [PlacesProxyController::class, 'suggest'])->name('places.suggest');
Route::get('/places/details', [PlacesProxyController::class, 'details'])->name('places.details');
Route::get('/book', [BookController::class, 'show'])->name('book');
Route::post('/book/quote', [BookController::class, 'quote'])->name('book.quote');

Route::post('/leads', [LeadController::class, 'store'])->name('lead.store');
Route::post('/quotes/preview', [QuotePreviewController::class, 'store'])->name('quote.preview');
Route::post('/partners/register', [PartnerRegisterController::class, 'store'])->name('partners.register');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/faq', fn () => redirect('/support', 301))->name('faq');
Route::get('/drive-with-us', fn () => redirect('/drive', 301));
Route::get('/advertise-with-us', fn () => redirect('/advertise', 301));

foreach (array_keys(config('karnacab_pages')) as $slug) {
    if ($slug === 'home') {
        continue;
    }
    Route::get('/'.$slug, [PageController::class, 'show'])->defaults('slug', $slug)->name($slug);
}

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::post('/book', [BookController::class, 'store'])->name('book.store');
    Route::post('/book/pay', [BookController::class, 'pay'])->name('book.pay');
    Route::get('/book/confirm/{id}', [BookController::class, 'confirm'])->name('book.confirm');
});

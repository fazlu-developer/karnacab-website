<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ContactController;
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
Route::post('/book/continue', [BookController::class, 'continue'])->name('book.continue');

Route::post('/leads', [LeadController::class, 'store'])->name('lead.store');
Route::post('/quotes/preview', [QuotePreviewController::class, 'store'])->name('quote.preview');
Route::post('/partners/register', [PartnerRegisterController::class, 'store'])->name('partners.register');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/faq', fn () => redirect('/support', 301))->name('faq');
Route::get('/drive-with-us', fn () => redirect('/drive', 301));
Route::get('/advertise-with-us', fn () => redirect('/advertise', 301));
Route::get('/login', fn () => redirect('/')->with('status', 'Book rides in the KarnaCab customer app.'))->name('login');
Route::get('/register', fn () => redirect('/')->with('status', 'Create your account in the KarnaCab customer or driver app.'))->name('register');

foreach (array_keys(config('karnacab_pages')) as $slug) {
    if ($slug === 'home') {
        continue;
    }
    Route::get('/'.$slug, [PageController::class, 'show'])->defaults('slug', $slug)->name($slug);
}

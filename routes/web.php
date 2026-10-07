<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DeleteAccountController;
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
Route::get('/privacy', fn () => redirect('/privacy-policy', 301))->name('privacy');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/delete-account', [DeleteAccountController::class, 'show'])->name('delete-account');
Route::post('/delete-account/otp', [DeleteAccountController::class, 'requestOtp'])->name('delete-account.otp');
Route::post('/delete-account', [DeleteAccountController::class, 'destroy'])->name('delete-account.destroy');
Route::get('/terms', fn () => redirect('/terms-conditions', 301))->name('terms');
Route::get('/terms-conditions', [PageController::class, 'terms'])->name('terms-conditions');
Route::get('/about-us', fn () => redirect('/about', 301));
Route::get('/drive-with-us', fn () => redirect('/drive', 301));
Route::get('/advertise-with-us', fn () => redirect('/advertise', 301));
Route::get('/login', fn () => redirect('/')->with('status', 'Book rides in the KarnaRide customer app.'))->name('login');
Route::get('/register', fn () => redirect('/')->with('status', 'Create your account in the KarnaRide customer or driver app.'))->name('register');

foreach (array_keys(config('karnacab_pages')) as $slug) {
    if (in_array($slug, ['home', 'privacy', 'terms'], true)) {
        continue;
    }
    Route::get('/'.$slug, [PageController::class, 'show'])->defaults('slug', $slug)->name($slug);
}

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[a-z0-9][a-z0-9\-]*')
    ->name('cms.page');

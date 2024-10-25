<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::controller(FrontController::class)->group(function () {
    Route::get('/', 'index')->name('front.index');
    Route::get('/browse/{category:slug}', 'category')->name('front.category');
    Route::get('/workshop/{workshop:slug}', 'details')->name('front.details');
});

Route::controller(BookingController::class)->group(function () {
    Route::get('/check-booking', 'checkBooking')->name('front.check_booking');
    Route::post('/check_booking/details', 'checkBookingDetails')->name('front.check_booking_details');

    Route::get('/booking/payment', 'payment')->name('front.payment');
    Route::post('/booking/payment', 'paymentStore')->name('front.paymentStore');

    Route::get('/booking/{workshop:slug}', 'booking')->name('front.booking');
    Route::post('/booking/{workshop:slug}', 'bookingStore')->name('front.bookingStore');

    Route::get('/booking/finished/{bookingTransaction}', 'bookingFinished')->name('front.booking_finished');
});

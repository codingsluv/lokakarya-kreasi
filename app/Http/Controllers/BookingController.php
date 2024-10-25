<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\StoreCheckBookingRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Models\BookingTransaction;
use App\Models\Workshop;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService){
        $this->bookingService = $bookingService;
    }

    public function booking(Workshop $workshop){
        return view('booking.booking', compact('workshop'));
    }

    public function bookingStore(StoreBookingRequest $request, Workshop $workshop){
        $valid = $request->validated();
        $valid['workshop_id'] = $workshop->id;

        try {
            $this->bookingService->storeBooking($valid);
            return redirect()->route('front.payment');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['error' => 'Unable to create booking. Please try again']);
        }
    }
    public function payment(){
        if (!$this->bookingService->isBookingSessionAvailable()) {
            return redirect()->route('front.index');
        }

        $data = $this->bookingService->getBookingDetails();

        if(!$data) {
            return redirect()->route('front.index');
        }

        return view('booking.payment', compact('data'));
    }

    public function paymentStore(StorePaymentRequest $request){
        $valid = $request->validated();

        try {
            $bookingTransactionId = $this->bookingService->finalizeBookingPayment($valid);
            return redirect()->route('front.booking_finished', $bookingTransactionId);
        } catch (\Throwable $th) {
            Log::error('Payment storage failed: '. $th->getMessage());
            return redirect()->back()->withErrors(['error' => 'Unable to store payment. Please try again.' .$th->getMessage()]);
        }
    }

    public function bookingFinshed(BookingTransaction $bookingTransaction) {
        return view('booking.booking_finished', compact('bookingTransaction'));
    }

    public function checkBooking(){
        return view('booking.my_booking');
    }

    public function checkBookingDetails(StoreCheckBookingRequest $request){
        $valid = $request->validated();

        $myBookindDetails = $this->bookingService->getMyBookingDetails($valid);

        if($myBookindDetails){
            return view('booking.my_booking_details', compact('myBookingDetails'));
        }

        return redirect()->route('front.check_booking')->withErrors(['error' => 'No booking found for this phone number and transaction ID']);
    }
}

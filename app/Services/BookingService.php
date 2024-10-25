<?php

namespace App\Services;

use App\Models\BookingTransaction;
use App\Models\WorkshopPartisipan;
use App\Repository\Contracts\BookingRepositoryInterface;
use App\Repository\Contracts\WorkshopRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingService
{
    protected $bookingRepository;
    protected $workshopRepository;

    public function __construct(WorkshopRepositoryInterface $workshopRepository,
        BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->workshopRepository = $workshopRepository;
    }

    public function storeBooking(array $validateData)
    {
        $existingData = $this->bookingRepository->getOrderDataFromSession();
        $updateData = array_merge($existingData, $validateData);

        $this->bookingRepository->saveToSession($updateData);

        return $updateData;
    }

    public function isBookingSessionAvailable(){
        return $this->bookingRepository->getOrderDataFromSession() !== null;
    }

    public function getBookingDetails()
    {
        $orderData = $this->bookingRepository->getOrderDataFromSession();

        if(empty($orderData)) {
            return null;
        }

        $workshop = $this->workshopRepository->find($orderData['workshop_id']);

        $quantity = isset($orderData['quantity']) ? $orderData['quantity'] : 1;
        $subTotalAmount = $workshop->price * $quantity;

        $taxRate = 0.11;
        $totalTax = $subTotalAmount * $taxRate;

        $totalAmount = $subTotalAmount * $totalTax;

        $orderData['sub_total_amount'] = $subTotalAmount;
        $orderData['total_tax'] = $totalTax;
        $orderData['total_amount'] = $totalAmount;

        $this->bookingRepository->saveToSession($orderData);

        return compact('orderData', 'workshop');
    }

    public function finalizeBookingPayment(array $paymentData)
    {
        $orderData = $this->bookingRepository->getOrderDataFromSession();

        if(!$orderData){
            throw new \Exception('Booking data is missing from session');
        }

        Log::info('Order Data:', $orderData);

        if(!isset($orderData['total_amount'])) {
            throw new \Exception('Total amount is missing from order data');
        }

        if(isset($paymentData['proof'])) {
            $proofPath = $paymentData['proof']->store('proofs', 'public');
        }

        DB::beginTransaction();

        try {
            $bookingTransacion = BookingTransaction::create([
                'name' => $orderData['name'],
                'email' => $orderData['email'],
                'phone' => $orderData['phone'],
                'customer_bank_name' => $orderData['customer_bank_name'],
                'customer_bank_number' => $orderData['customer_bank_number'],
                'customer_bank_account' => $orderData['customer_bank_account'],
                'proof' => $proofPath,
                'quantity' => $orderData['quantity'],
                'total_amount' => $orderData['total_amount'],
                'is_paid' => false,
                'workshop_id' => $orderData['workshop_id'],
                'booking_trx_id' => BookingTransaction::generateUnixTrxId(),

            ]);

            foreach ($orderData['participants'] as $participant) {
                WorkshopPartisipan::create([
                    'name' => $participant['name'],
                    'occupation' => $participant['occupation'],
                    'email' => $participant['email'],
                    'workshop_id' => $bookingTransacion->workshop_id,
                    'booking_trx_id' => $bookingTransacion->id,
                ]);
            }
            DB::commit();
            $this->bookingRepository->clearSession();
            return $bookingTransacion->id;
        } catch (\Exception $th) {
            //throw $th;
            Log::error('Payment processing failed: ' .$th->getMessage());

            DB::rollBack();

            throw $th;
        }
    }

    public function getMyBookingDetails(array $valid)
    {
        return $this->bookingRepository->findByTrxIdAndPhoneNumber($valid['booking_trx_id'],
        $valid['phone']);
    }
}

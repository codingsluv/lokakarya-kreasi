<?php

namespace App\Repository\Contracts;

interface BookingRepositoryInterface
{
    public function createBooking(array $data);
    public function findByTrxIdAndPhoneNumber($bookingTrxId, $phoneNumber);


    // ! ini untuk meng-create data dan menyimpanya kedalam sebuah session sementara sebelum di simpan ke database
    public function saveToSession(array $data);
    public function updateToSessionData(array $data);
    public function getOrderDataFromSession();
    public function clearSession();
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopPartisipan extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'occupation',
        'workshop_id',
        'booking_transaction_id',
    ];

    public function workshop(){
        return $this->belongsTo(Workshop::class, 'workshop_id');
    }

    public function bookingTransaction(){
        return $this->belongsTo(BookingTransaction::class, 'booking_transaction_id');
    }
}

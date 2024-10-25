<?php

namespace App\Filament\Resources\BookingTransactionResource\Pages;

use App\Filament\Resources\BookingTransactionResource;
use App\Models\WorkshopPartisipan;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateBookingTransaction extends CreateRecord
{
    protected static string $resource = BookingTransactionResource::class;

    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate() {
        DB::transaction(function () {
          $record = $this->record;
          $participants = $this->form->getState()['participants'];

          foreach ($participants as $participant) {
            WorkshopPartisipan::create([
                'workshop_id' => $record->workshop_id,
                'booking_transaction_id' => $record->id,
                'name' => $participant['name'],
                'email' => $participant['email'],
                'occupation' => $participant['occupation'],
            ]);
          }
        });
    }
}

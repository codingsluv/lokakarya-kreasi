<?php

namespace App\Repository;

use App\Models\Workshop;
use App\Repository\Contracts\WorkshopRepositoryInterface;

class WorkshopRepository implements WorkshopRepositoryInterface
{
    public function getAllWorkshops(){
        // Logic to fetch all workshops from the database
        // and return them as an array
        return Workshop::latest()->get();
    }

    public function find($id){
        // Logic to fetch workshop by id from the database
        // and return it as an instance of Workshop model
        return Workshop::find($id);
    }

    public function getPrice($workshopId){
        // Logic to fetch workshop price by id from the database
        // and return the price as a float
        $workshops = $this->find($workshopId);
        return $workshops ? $workshops->price : 0;
    }
}

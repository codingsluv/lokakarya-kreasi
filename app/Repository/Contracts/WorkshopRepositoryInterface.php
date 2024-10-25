<?php

namespace App\Repository\Contracts;

interface WorkshopRepositoryInterface
{
    public function getAllWorkshops();
    public function find($id);
    public function getPrice($workshopId);
}

<?php

namespace App\Services;

use App\Repository\Contracts\CategoryRepositoryInterface;
use App\Repository\Contracts\WorkshopRepositoryInterface;

class FrontService
{
    protected $categoryRepository;
    protected $workshopRepository;

    public function __construct(WorkshopRepositoryInterface $workshopRepository,
    CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->workshopRepository = $workshopRepository;
    }

    public function getFrontPageData(){
        $categories = $this->categoryRepository->getAllCategories();
        $newWorkshop = $this->workshopRepository->getAllWorkshops();

        return compact('categories', 'newWorkshop');
    }
}

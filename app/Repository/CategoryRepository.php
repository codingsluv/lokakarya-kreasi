<?php

namespace App\Repository;

use App\Models\Category;
use App\Repository\Contracts\CategoryRepositoryInterface;


class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories(){
        // Logic to fetch all categories from the database
        // and return them as an array
        return Category::latest()->get();
    }
}

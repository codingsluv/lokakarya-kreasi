<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopInstructor extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'avatar',
        'occupation',
    ];

    public function workshops(){
        return $this->hasMany(Workshop::class);
    }
}

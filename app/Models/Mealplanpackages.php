<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mealplanpackages extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'recipe', 'price', 'featuredImage', 'user_id', 'mealplan_id','mealplanduration_id'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'main_category_user', 'main_category_id', 'user_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_main_category', 'main_category_id', 'course_id');
    }
}

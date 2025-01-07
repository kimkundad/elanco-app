<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'flag',
        'img',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_country', 'country_id', 'course_id');
    }
}




<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speaker extends Model
{
    use HasFactory;

    public function course()
    {
        return $this->belongsTo(course::class, 'course_id');
    }

    public function countryDetails()
    {
        return $this->belongsTo(Country::class, 'country', 'id'); // 'country' คือ foreign key
    }
}

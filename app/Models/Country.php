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
        return $this->belongsToMany(course::class, 'course_country', 'country_id', 'course_id');
    }

    public function format()
    {
        return [
            'name' => $this->name,
            'flag' => $this->flag,
            'img' => $this->img,
        ];
    }
}




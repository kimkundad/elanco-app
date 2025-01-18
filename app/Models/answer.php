<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'questions_id', 'answers', 'answers_status'
    ];


    public function question()
    {
        return $this->belongsTo(question::class, 'questions_id');
    }
}

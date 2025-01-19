<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_title', 'course_id', 'course_img', 'course_preview', 'duration', 'url_video', 'status'
    ];

    // Relationships
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'course_country');
    }

    public function mainCategories()
    {
        return $this->belongsToMany(MainCategory::class, 'course_main_category');
    }

    public function subCategories()
    {
        return $this->belongsToMany(SubCategory::class, 'course_sub_category');
    }

    public function animalTypes()
    {
        return $this->belongsToMany(AnimalType::class, 'course_animal_type');
    }

    public function itemDes()
    {
        return $this->hasMany(itemDes::class, 'course_id', 'id');
    }

    public function Speaker()
    {
        return $this->hasMany(Speaker::class, 'course_id', 'id');
    }

    public function referances()
    {
        return $this->hasMany(Referance::class, 'course_id', 'id');
    }

    public function courseActions()
    {
        return $this->hasMany(CourseAction::class, 'course_id', 'id');
    }

    public function quiz()
    {
        return $this->belongsTo(quiz::class, 'id_quiz', 'id');
    }

    public function actions()
    {
        return $this->hasMany(CourseAction::class, 'course_id', 'id');
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}

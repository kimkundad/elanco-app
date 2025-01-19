<?php

namespace App\Models\Settings;

use App\Models\Country;
use App\Models\course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedCourse extends Model
{
    use HasFactory;

    protected $table = 'featured_courses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id',
        'status',
        'order',
        'country_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'order' => 'integer',
        'country_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the course associated with the featured course.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the country associated with the featured course.
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Get the user who created the featured course.
     */
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the featured course.
     */
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Format the featured course with additional details.
     *
     * @return array
     */
    public function formatIncludingCourseAndCountry()
    {
        return [
            'id' => $this->id,
            'course' => $this->course ? $this->course->only(['id', 'course_title', 'course_img', 'course_preview']) : null,
            'country' => $this->country ? $this->country->format() : null,
            'status' => $this->status,
            'order' => $this->order,
            'createdBy' => $this->createdByUser ? $this->createdByUser->format() : null,
            'updatedBy' => $this->updatedByUser ? $this->updatedByUser->format() : null,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}

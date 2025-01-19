<?php

namespace App\Http\Repositories\Courses;

use App\Models\course;

class CourseRepositoryImpl implements CourseRepository
{
    public function findById($id)
    {
        return course::find($id);
    }
}

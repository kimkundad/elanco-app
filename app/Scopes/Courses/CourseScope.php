<?php

namespace App\Scopes\Courses;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CourseScope implements Scope
{
    /**
     * Apply the scope to the given builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $countryFlag = request()->input('countryFlag');

        if ($countryFlag) {
            return;
        }

        if (auth()->check() && !auth()->user()->hasRole('superadmin')) {
            $builder->whereHas('countries', function ($query) {
                $query->where('countries.id', auth()->user()->countryDetails->id);
            });
        }
    }
}

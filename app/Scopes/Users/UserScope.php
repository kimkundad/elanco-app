<?php

namespace App\Scopes\Users;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserScope implements Scope
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
        return;

        $countryFlag = request()->input('countryFlag');

        if ($countryFlag) {
            return;
        }

        if (auth()->check() && !auth()->user()->hasRole('superadmin')) {
            $builder->where('country', auth()->user()->countryDetails->id);
        }
    }
}

<?php

namespace App\Scopes\Country;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CountryScope implements Scope
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
            $builder->where('country_id', auth()->user()->countryDetails->id);
        }
    }
}

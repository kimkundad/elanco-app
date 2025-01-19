<?php

namespace App\Models\Settings;

use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'home_banners';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'desktop_image',
        'mobile_image',
        'status',
        'order',
        'created_by',
        'updated_by',
        'country_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'order' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'country_id' => 'integer',
    ];

    /**
     * Get the user who created the banner.
     */
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the banner.
     */
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the country associated with the banner.
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Format the banner with additional details.
     *
     * @return array
     */
    public function formatIncludingCreatedUserAndUpdatedUserAndCountry()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'desktopImage' => $this->desktop_image,
            'mobileImage' => $this->mobile_image,
            'status' => $this->status,
            'order' => $this->order,
            'country' => $this->country ? $this->country->format() : null,
            'createdBy' => $this->createdByUser ? $this->createdByUser->format() : null,
            'updatedBy' => $this->updatedByUser ? $this->updatedByUser->format() : null,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}

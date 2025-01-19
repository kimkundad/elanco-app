<?php

namespace App\Models\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $table = 'user_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_detail',
        'device_type',
        'browser_type',
        'ip_address',
        'activity_timestamp',
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function format()
    {
        return [
            'id' => $this->id,
            'activityType' => $this->activity_type,
            'activityDetail' => $this->activity_detail,
            'deviceType' => $this->device_type,
            'browserType' => $this->browser_type,
            'ipAddress' => $this->ip_address,
            'activityTimestamp' => $this->activity_timestamp,
            'user' => $this->user ? $this->user->formatIncludingCountry() : null,
        ];
    }
}

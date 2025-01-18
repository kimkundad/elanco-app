<?php

namespace App\Models\SystemLog;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'ip_address', 'action', 'status', 'error_reason'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function format()
    {
        return [
            'id' => $this->id,
            'ipAddress' => $this->ip_address,
            'action' => $this->action,
            'status' => $this->status,
            'errorReason' => $this->error_reason,
            'createAt' => $this->created_at,
            'user' => $this->user ? $this->user->formatIncludingCountry() : null,
        ];
    }
}

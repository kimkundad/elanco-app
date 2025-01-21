<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\SystemLogs\SystemLog;
use App\Models\Users\UserLogin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

// เพิ่ม JWTSubject

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'country', 'firstName', 'last_active_at', 'lastName', 'email', 'position', 'userType', 'terms', 'prefix', 'vetId', 'clinic', 'password', 'email_verified_at', 'is_admin', 'provider', 'provider_id', 'access_token', 'avatar', 'phone', 'address', 'birthday', 'zipcode', 'point', 'idcard', 'code_user', 'shop_id',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @param string|array $roles
     */
    public function authorizeRoles($roles)
    {
        if (is_array($roles)) {
            return $this->hasAnyRole($roles) || abort(401, 'This action is unauthorized.');
        }
        return $this->hasRole($roles) || abort(401, 'This action is unauthorized.');
    }

    /**
     * Check multiple roles
     * @param array $roles
     */
    public function hasAnyRole($roles)
    {
        return null !== $this->roles()->whereIn('name', $roles)->first();
    }

    /**
     * Check one role
     * @param string $role
     */
    public function hasRole($role)
    {
        return null !== $this->roles()->where('name', $role)->first();
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey(); // ใช้ ID เป็น JWT Identifier
    }

    // เพิ่มข้อมูล custom ใน JWT Payload
    public function getJWTCustomClaims()
    {
        return [
            'is_admin' => $this->is_admin, // ตัวอย่างข้อมูลเพิ่มเติม
        ];
    }

    public function countryDetails()
    {
        return $this->belongsTo(Country::class, 'country', 'id'); // 'country' คือ foreign key
    }

    public function mainCategories()
    {
        return $this->belongsToMany(MainCategory::class, 'main_category_user', 'user_id', 'main_category_id');
    }

    public function subCategories()
    {
        return $this->belongsToMany(SubCategory::class);
    }

    public function animalTypes()
    {
        return $this->belongsToMany(AnimalType::class);
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class, 'created_by');
    }

    public function systemLogs()
    {
        return $this->hasMany(SystemLog::class);
    }

    /**
     * Get all login records for the user.
     */
    public function logins()
    {
        return $this->hasMany(UserLogin::class, 'user_id', 'id');
    }

    /**
     * Get the latest login record for the user.
     */
    public function latestLogin()
    {
        return $this->hasOne(UserLogin::class, 'user_id', 'id')->latest('login_at');
    }

    public function surveyResponses()
    {
        return $this->hasMany(SurveyResponse::class, 'user_id');
    }

    public function formatIncludingCountry()
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'position' => $this->position,
            'userType' => $this->userType,
            'terms' => $this->terms,
            'prefix' => $this->prefix,
            'vetId' => $this->vetId,
            'clinic' => $this->clinic,
            'avatar' => $this->avatar,
            'phone' => $this->phone,
            'address' => $this->address,
            'birthday' => $this->birthday,
            'zipcode' => $this->zipcode,
            'point' => $this->point,
            'idcard' => $this->idcard,
            'codeUser' => $this->code_user,
            'shopId' => $this->shop_id,
            'country' => $this->countryDetails ? $this->countryDetails->format() : null,
        ];
    }
}

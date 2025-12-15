<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\ApiQuery;
use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use UserNotify, HasApiTokens, ApiQuery;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'ver_code',
        'balance',
        'kyc_data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'kyc_data'          => 'object',
        'ver_code_send_at'  => 'datetime',
    ];

    /**
     * specified column for export with column manipulation
     *
     * @var array
     */
    public function exportColumns(): array
    {
        return [
            'firstname',
            'lastname',
            'username',
            'email',
            'mobile',
            "country_name",
            "created_at" => [
                'name'     => "Joined At",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                },
            ],
            "balance"    => [
                'callback' => function ($item) {
                    return showAmount($item->balance);
                },
            ],
        ];
    }

    protected $guarded = ['id'];

    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }
    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function planPurchases()
    {
        return $this->hasMany(PlanPurchase::class);
    }

    protected function getDefaultGuardName(): string
    {
        return 'admin';
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn() => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function imageSrc(): Attribute
    {
        return new Attribute(
            get: fn() => getImage(getFilePath('userProfile') . '/' . $this->image, getFilePath('userProfile'), isAvatar: true),
        );
    }

    public function fullNameShortForm(): Attribute
    {
        return new Attribute(
            get: fn() => strtoupper(substr($this->firstname, 0, 1)) . strtoupper(substr($this->lastname, 0, 1)),
        );
    }

    public function mobileNumber(): Attribute
    {
        return new Attribute(
            get: fn() => $this->dial_code . $this->mobile,
        );
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED);
    }

    public function scopeSubscribed($query)
    {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED)->where('plan_id', '!=', 0);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified($query)
    {
        return $query->where('sv', Status::UNVERIFIED);
    }

    public function scopeKycUnverified($query)
    {
        return $query->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending($query)
    {
        return $query->where('kv', Status::KYC_PENDING);
    }

    public function scopeEmailVerified($query)
    {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopePremium($query)
    {
        return $query->where('plan_id', '!=', 0);
    }
    public function scopeFree($query)
    {
        return $query->where('plan_id', '=', 0);
    }
    public function scopeMobileVerified($query)
    {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    public function scopeIsParent($query)
    {
        return $query->where('parent_id', '=', 0);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function scopeStaff($query)
    {
        return $query->whereHas('parent')->where('is_staff', Status::YES);
    }

    public function staffPermissions()
    {
        return $this->belongsToMany(StaffPermission::class, 'staff_has_permissions', 'staff_id', 'staff_permission_id');
    }

    public function hasStaffPermission($permission)
    {
        if ($this->is_staff) {
            return $this->staffPermissions()->where('name', $permission)->exists();
        }
        return true;
    }

    public function hasAnyStaffPermission($permissions = [])
    {
        if ($this->is_staff) {
            return $this->staffPermissions()->whereIn('name', $permissions)->exists();
        }
        return true;
    }
}

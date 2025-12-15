<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasApiTokens, GlobalStatus, HasRoles, HasPermissions;

    protected $guarded  = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['image_src'];
    public function imageSrc(): Attribute
    {
        return new Attribute(
            get: fn() => $this->image  ? getImage(getFilePath('adminProfile') . '/' . $this->image, getFileSize('adminProfile'), isAvatar: true) : siteFavicon(),
        );
    }
}

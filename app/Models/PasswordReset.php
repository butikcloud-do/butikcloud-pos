<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    public $timestamps = false;

    protected $guarded  = ['id'];

    protected $hidden = [
        'token'
    ];
}

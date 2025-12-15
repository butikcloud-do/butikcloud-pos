<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use GlobalStatus, SoftDeletes;

    protected $guarded  = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

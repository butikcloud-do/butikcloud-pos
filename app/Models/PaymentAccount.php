<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentAccount extends Model
{
    use GlobalStatus, SoftDeletes;

    protected $guarded  = ['id'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }
}

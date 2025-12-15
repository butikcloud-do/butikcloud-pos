<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $guarded  = ['id'];
    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }
}

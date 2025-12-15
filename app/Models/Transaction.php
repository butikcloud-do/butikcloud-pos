<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $guarded  = ['id'];

    public function exportColumns(): array
    {
        return  [

            'trx',
            'created_at' => [
                'name' =>  "transacted",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                }
            ],
            'amount' => [
                'callback' => function ($item) {
                    return showAmount($item->amount);
                }
            ],
            'post_balance' => [
                'callback' => function ($item) {
                    return showAmount($item->post_balance);
                }
            ],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }
}

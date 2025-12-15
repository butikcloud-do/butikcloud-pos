<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use GlobalStatus, SoftDeletes;

    protected $guarded  = ['id'];
    public function exportColumns(): array
    {
        return  [
            'employee_id' => [
                'name' => "employee",
                'callback' => function ($item) {
                    return @$item->employee->name;
                }
            ],
            'amount' => [
                'callback' => function ($item) {
                    return showAmount($item->amount);
                }
            ],
            'payment_method_id' => [
                'name' => "payment_method",
                'callback' => function ($item) {
                    return @$item->paymentMethod->name;
                }
            ],
            'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentType::class);
    }
}

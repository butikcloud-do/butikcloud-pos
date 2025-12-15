<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use GlobalStatus, SoftDeletes;

    protected $guarded  = ['id'];

    public function exportColumns(): array
    {
        return  [
            'name',
            'company_id' => [
                'name' => "company",
                'callback' => function ($item) {
                    return @$item->company->name;
                }
            ],
        ];
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

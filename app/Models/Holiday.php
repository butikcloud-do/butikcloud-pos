<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use GlobalStatus, SoftDeletes;

    protected $guarded  = ['id'];

    public function exportColumns(): array
    {
        return  [
            'title',
            'company_id' => [
                'name' => "company",
                'callback' => function ($item) {
                    return @$item->company->name;
                }
            ],
            'start_date',
            'end_date',
            'description',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

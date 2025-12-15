<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use GlobalStatus, SoftDeletes;

    protected $guarded = ['id'];

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

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function designations()
    {
        return $this->hasMany(Designation::class, 'department_id');
    }
}

<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use GlobalStatus, SoftDeletes;

    protected $guarded  = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'company_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'company_id');
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class, 'company_id');
    }
}

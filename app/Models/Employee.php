<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Employee extends Model
{
    use GlobalStatus, SoftDeletes;

    protected $guarded  = ['id'];
    public function exportColumns(): array
    {
        return  [
            'name',
            'gender',
            'dob',
            'email',
            'country',
            'phone',
            'joining_date',
            'company_id' => [
                'name' => "company",
                'callback' => function ($item) {
                    return @$item->company->name;
                }
            ],
            'department_id' => [
                'name' => "department",
                'callback' => function ($item) {
                    return @$item->department->name;
                }
            ],
            'designation_id' => [
                'name' => "designation",
                'callback' => function ($item) {
                    return @$item->designation->name;
                }
            ]

        ];
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }


    public function imageSrc(): Attribute
    {
        return new Attribute(
            get: fn() => getImage(getFilePath('employeeImage') . '/' . $this->image, getFilePath('employeeImage'), isAvatar: true),
        );
    }

    // Leave Manage

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function getLeaveStatusAttribute()
    {
        $today = Carbon::now()->format('Y-m-d');

        return  $this->leaveRequests()
            ->where('status', Status::APPROVED)
            ->where(function ($query) use ($today) {
                $query->where(function ($q) use ($today) {
                    $q->where('start_date', $today)
                        ->whereNull('end_date');
                })->orWhere(function ($q) use ($today) {
                    $q->where('start_date', '<=', $today)
                        ->where('end_date', '>=', $today);
                });
            })
            ->exists();
    }


    public function scopeNotOnLeaveToday($query)
    {
        $today = Carbon::today()->format('Y-m-d');
        return $query->whereDoesntHave('leaveRequests', function ($q) use ($today) {
            $q->where('status', Status::APPROVED)
                ->where(function ($sub) use ($today) {
                    $sub->where(function ($q1) use ($today) {
                        $q1->where('start_date', $today)
                            ->whereNull('end_date');
                    })->orWhere(function ($q2) use ($today) {
                        $q2->where('start_date', '<=', $today)
                            ->where('end_date', '>=', $today);
                    });
                });
        });
    }
}

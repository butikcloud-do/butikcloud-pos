<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\RecycleBinManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class LeaveRequest extends Model
{
    use GlobalStatus, RecycleBinManager;

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
            'leave_type_id' => [
                'name' => "leave_type",
                'callback' => function ($item) {
                    return @$item->leaveType->name;
                }
            ],
            'start_date',
            'end_date',
            'days'
        ];
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: function () {
                $html = '';
                if ($this->status == Status::APPROVED) {
                    $html = '<span class="badge badge--success">' . trans('Approved') . '</span>';
                } elseif ($this->status == Status::PENDING) {
                    $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
                } elseif ($this->status == Status::REJECTED) {
                    $html = '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
                }
                return $html;
            },
        );
    }
}

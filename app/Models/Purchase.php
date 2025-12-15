<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Purchase extends Model
{

    protected $guarded  = ['id'];
    public function exportColumns(): array
    {
        return  [
            'invoice_number',
            'reference_number',
            'warehouse_id' => [
                'name' => 'warehouse',
                'callback' => function ($item) {
                    return @$item->warehouse->name;
                }
            ],
            'supplier_id' => [
                'name' => 'supplier',
                'callback' => function ($item) {
                    return @$item->supplier->name;
                }
            ],
            'total' => [
                'callback' => function ($item) {
                    return showAmount($item->total);
                }
            ],
            'status' => [
                'callback' => function ($item) {
                    return strip_tags($item->statusBadge);
                }
            ]
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetails::class, 'purchase_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function purchaseBy()
    {
        return $this->belongsTo(User::class, 'purchase_by');
    }

    public function supplierPayments()
    {
        return $this->hasMany(SupplierPayment::class, 'purchase_id');
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: function () {
                $html = '';
                if ($this->status == Status::PURCHASE_ORDERED) {
                    $html = '<span class="badge badge--info">' . trans('Ordered') . '</span>';
                } elseif ($this->status == Status::PURCHASE_PENDING) {
                    $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
                } else {
                    $html = '<span class="badge badge--success">' . trans('Received') . '</span>';
                }
                return $html;
            },
        );
    }
}

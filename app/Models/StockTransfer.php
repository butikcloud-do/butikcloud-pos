<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $guarded  = ['id'];
    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function stockTransferDetails()
    {
        return $this->hasMany(StockTransferDetail::class);
    }
}

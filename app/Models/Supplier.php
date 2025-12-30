<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Supplier extends Model
{
    use GlobalStatus, SoftDeletes;

    protected $guarded  = ['id'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }

    /**
     * Get the total outstanding balance for this supplier
     * Calculated as sum of due_amount from all purchases
     */
    public function balance(): Attribute
    {
        return new Attribute(
            get: function () {
                return $this->purchases()
                    ->where('user_id', $this->user_id)
                    ->sum('due_amount');
            },
        );
    }
}

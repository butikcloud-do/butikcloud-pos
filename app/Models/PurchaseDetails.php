<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetails extends Model
{
    protected $guarded = ['id'];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function productDetail()
    {
        return $this->belongsTo(ProductDetail::class, 'product_details_id');
    }
}

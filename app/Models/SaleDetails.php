<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleDetails extends Model
{
    protected $guarded  = ['id'];
    public function productDetail()
    {
        return $this->belongsTo(ProductDetail::class, 'product_details_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }
}

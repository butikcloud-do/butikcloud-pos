<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    protected $guarded = ['id'];

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id');
    }
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }
    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function productStock()
    {
        return $this->hasMany(ProductStock::class, 'product_details_id');
    }

    public function salesDetails()
    {
        return $this->hasMany(SaleDetails::class, 'product_details_id');
    }
}

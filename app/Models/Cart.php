<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded  = ['id'];
    
    public function productDetail()
    {
        return $this->belongsTo(ProductDetail::class, 'product_details_id');
    }
}

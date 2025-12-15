<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use GlobalStatus, SoftDeletes;

    protected $appends = [
        'image_src'
    ];

    protected $guarded  = ['id'];
    public function imageSrc(): Attribute
    {
        return new Attribute(
            get: fn() => getImage(getFilePath('product') . '/' . $this->product_code . "/" . $this->image),
        );
    }
    public function name(): Attribute
    {
        return new Attribute(
            get: fn($value) => htmlspecialchars($value, ENT_QUOTES, 'UTF-8'),
        );
    }

    public function exportColumns(): array
    {
        return  [
            'name',
            'product_code',
            'brand_id' => [
                'name' => 'brand',
                'callback' => function ($item) {
                    return @$item->brand->name;
                }
            ],
            'category_id' => [
                'name' => 'category',
                'callback' => function ($item) {
                    return @$item->category->name;
                }
            ],
            'unit_id' => [
                'name' => 'unit',
                'callback' => function ($item) {
                    return @$item->unit->name;
                }
            ]
        ];
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    public function details()
    {
        return $this->hasMany(ProductDetail::class, 'product_id');
    }
}

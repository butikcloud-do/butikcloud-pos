<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use GlobalStatus, SoftDeletes;
    protected $guarded  = ['id'];

    public function exportColumns(): array
    {
        return  [
            'name' => [
                'name' => 'Name'
            ],
            'attribute_id' => [
                'name'     => "Attribute",
                'callback' => function ($item) {
                    return @$item->attribute->name;
                }
            ]
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function attribute()
    {
        return $this->belongsTo(Attribute::class)->withTrashed();
    }
}

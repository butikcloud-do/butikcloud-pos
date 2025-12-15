<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use GlobalStatus, SoftDeletes;

    protected $appends = ['image_src'];

    protected $guarded  = ['id'];

    public function exportColumns(): array
    {
        return  ['name'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function imageSrc(): Attribute
    {
        return new Attribute(
            get: fn() => getImage(getFilePath('brand') . '/' . $this->image, getFileSize('brand')),
        );
    }
}

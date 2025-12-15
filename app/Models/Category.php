<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Category extends Model
{
    use GlobalStatus, SoftDeletes;

    protected $guarded  = ['id'];
    protected $appends = ['image_src'];

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
            get: fn() => getImage(getFilePath('category') . '/' . $this->image, getFileSize('category')),
        );
    }
}

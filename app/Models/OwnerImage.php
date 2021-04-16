<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerImage extends Model
{



    protected $fill = [
        "owner_id",
        "image_id",
    ];



    /**
     * 紐づく画像一覧を取得する
     *
     * @return void
     */
    public function images()
    {
        return $this->hasMany(Image::class, "id", "image_id");
    }
}
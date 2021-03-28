<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{


    protected $fillable = [
        "service_name",
        "service_type",
        "owner_id",
        "capacity",
        "price",
        "price_per_hour",
        "is_displayed",
        "is_deleted",
        // 作成者
        "created_by",
        // 更新者
        "updated_by",
    ];

    public function reserves()
    {
        return $this->hasMany(Reserve::class, "service_id");
    }

    public function owner()
    {
        return $this->hasOne(Owner::class, "id", "owner_id");
    }
}

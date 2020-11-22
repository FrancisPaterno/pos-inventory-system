<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function itemCategory()
    {
        return $this->belongsTo(ItemCategory::class);
    }

    public function itemBrand()
    {
        return $this->belongsTo(ItemBrand::class);
    }

    public function itemUnit()
    {
        return $this->belongsTo(ItemUnit::class);
    }

    public function itemStatus()
    {
        return $this->belongsTo(ItemStatus::class);
    }
}

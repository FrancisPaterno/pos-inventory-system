<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function stockheader()
    {
        return $this->belongsTo(StockHeaders::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

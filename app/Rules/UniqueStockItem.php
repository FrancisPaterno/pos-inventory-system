<?php

namespace App\Rules;

use App\Models\StockItem;
use Illuminate\Contracts\Validation\Rule;

class UniqueStockItem implements Rule
{
    protected $stockno;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($stockno)
    {
        //
        $this->stockno = $stockno;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
        return StockItem::where('stock_header_id', $this->stockno)
            ->where('item_id', $value)
            ->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Item must be unique per stock.';
    }
}

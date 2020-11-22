<?php

namespace App\Http\Controllers;

use App\Models\StockHeaders;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StockHeadersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $rows = $request->rows;
        $search = $request->search;

        if ($search != '' && isset($rows)) {
            return StockHeaders::with('warehouse', 'supplier')->where('delivery_receipt_no', 'like', '%' . $search . '%')->paginate($rows);
        }

        if (isset($rows) && $search == '') {
            return StockHeaders::with('warehouse', 'supplier')->paginate($rows);
        }

        if (!isset($rows) && !isset($search)) {
            return StockHeaders::all();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $stock_header = StockHeaders::create($this->validate($request, [
            'delivery_receipt_no' => ['required', 'unique:stock_headers'],
            'description' => ['max:500'],
            'date_received' => ['required', 'date'],
            'warehouse_id' => ['required'],
            'supplier_id' => ['required'],
            'total' => ['required']
        ]));

        return StockHeaders::with('warehouse', 'supplier')->findOrFail($stock_header->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StockHeaders  $stockHeaders
     * @return \Illuminate\Http\Response
     */
    public function show(StockHeaders $stockHeader)
    {
        //
        return StockHeaders::with('warehouse', 'supplier')->findOrFail($stockHeader->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StockHeaders  $stockHeaders
     * @return \Illuminate\Http\Response
     */
    public function edit(StockHeaders $stockHeaders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StockHeaders  $stockHeaders
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StockHeaders $stockHeader)
    {
        //
        $stockHeader->update($this->validate($request, [
            'delivery_receipt_no' => ['required', Rule::unique('stock_headers')->ignore($stockHeader)],
            'description' => ['max:500'],
            'date_received' => ['required', 'date'],
            'warehouse_id' => ['required'],
            'supplier_id' => ['required'],
            'total' => ['required']
        ]));
        $updsh = StockHeaders::with('warehouse', 'supplier')->findOrFail($stockHeader->id);
        return $updsh;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StockHeaders  $stockHeaders
     * @return \Illuminate\Http\Response
     */
    public function destroy(StockHeaders $stockHeader)
    {
        //
        $stockHeader->delete();
        return $stockHeader;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ItemStatus;
use Illuminate\Http\Request;

class ItemStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (!isset($rows) && !isset($search)) {
            return ItemStatus::all();
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ItemStatus  $itemStatus
     * @return \Illuminate\Http\Response
     */
    public function show(ItemStatus $itemStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ItemStatus  $itemStatus
     * @return \Illuminate\Http\Response
     */
    public function edit(ItemStatus $itemStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ItemStatus  $itemStatus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemStatus $itemStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ItemStatus  $itemStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemStatus $itemStatus)
    {
        //
    }
}

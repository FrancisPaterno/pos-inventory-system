<?php

namespace App\Http\Controllers;

use App\Models\ItemUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $rows = $request['rows'];
        $search = $request['search'];

        if ($search != '' && isset($rows)) {
            return ItemUnit::where('name', 'like', '%' . $search . '%')->paginate($rows);
        }

        if (isset($rows) && $search == '') {
            return ItemUnit::paginate($rows);
        }

        if (!isset($rows) && !isset($search)) {
            return ItemUnit::all();
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
        $itemunit = ItemUnit::create($this->validate(
            $request,
            [
                'name' => ['required', 'max:20', 'unique:item_units'],
                'description' => ['max:100']
            ]
        ));
        return $itemunit;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ItemUnit  $itemUnit
     * @return \Illuminate\Http\Response
     */
    public function show(ItemUnit $itemUnit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ItemUnit  $itemUnit
     * @return \Illuminate\Http\Response
     */
    public function edit(ItemUnit $itemUnit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ItemUnit  $itemUnit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemUnit $itemunit)
    {
        //
        $itemunit->update($this->validate(
            $request,
            [
                'name' => ['required', 'max:20', Rule::unique('item_units')->ignore($itemunit)],
                'description' => ['max:100']
            ]
        ));
        return $itemunit;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ItemUnit  $itemUnit
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemUnit $itemunit)
    {
        //
        $itemunit->delete();
        return $itemunit;
    }
}

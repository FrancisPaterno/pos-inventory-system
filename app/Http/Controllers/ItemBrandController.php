<?php

namespace App\Http\Controllers;

use App\Models\ItemBrand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemBrandController extends Controller
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
            return ItemBrand::where('name', 'like', '%' . $search . '%')->paginate($rows);
        }

        if (isset($rows) && $search == '') {
            return ItemBrand::paginate($rows);
        }

        if (!isset($rows) && !isset($search)) {
            return ItemBrand::all();
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
        $brand = ItemBrand::create($this->validate(
            $request,
            [
                'name' => ['required', 'max:100', 'unique:item_brands'],
                'description' => ['max:500']
            ]
        ));
        return $brand;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ItemBrand  $itemBrand
     * @return \Illuminate\Http\Response
     */
    public function show(ItemBrand $itemBrand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ItemBrand  $itemBrand
     * @return \Illuminate\Http\Response
     */
    public function edit(ItemBrand $itemBrand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ItemBrand  $itemBrand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemBrand $itembrand)
    {
        //
        $itembrand->update($this->validate(
            $request,
            [
                'name' => ['required', 'max:100', Rule::unique('item_brands')->ignore($itembrand)],
                'description' => ['max:500']
            ]
        ));
        return $itembrand;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ItemBrand  $itemBrand
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemBrand $itembrand)
    {
        //
        if ($itembrand->delete()) {
            return $itembrand;
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemCategoryController extends Controller
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
            return ItemCategory::where('name', 'like', '%' . $search . '%')->paginate($rows);
        }

        if (isset($rows) && $search == '') {
            return ItemCategory::paginate($rows);
        }

        if (!isset($rows) && !isset($search)) {
            return ItemCategory::all();
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
        $category = ItemCategory::create($this->validate($request, ['name' => ['required', 'max:100', 'unique:item_categories'], 'description' => ['max:500']]));
        return $category;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ItemCategory  $itemCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ItemCategory $itemCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ItemCategory  $itemCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ItemCategory $itemCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ItemCategory  $itemCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemCategory $itemcategory)
    {
        //
        $itemcategory->update($this->validate($request, ['name' => ['required', 'max:100', Rule::unique('item_categories')->ignore($itemcategory)], 'description' => ['max:500']]));
        return $itemcategory;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ItemCategory  $itemCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemCategory $itemcategory)
    {
        //
        if ($itemcategory->delete()) {
            return $itemcategory;
        }
    }
}

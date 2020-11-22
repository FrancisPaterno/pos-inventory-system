<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class ItemController extends Controller
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
            $items = Item::with('itemCategory', 'itemBrand', 'itemUnit', 'itemStatus')->where('name', 'like', '%' . $search . '%')->orWhere('barcode', 'like', '%' . $search . '%')->paginate($rows);
            foreach ($items as $item) {
                try {
                    $image = Storage::disk('images')->get($item->name);
                    $item->image = $image;
                } catch (Throwable $e) {
                    $item->image = null;
                }
            }
            return $items;
        }

        if (isset($rows) && $search == '') {
            $items = Item::with('itemCategory', 'itemBrand', 'itemUnit', 'itemStatus')->paginate($rows);
            foreach ($items  as $item) {
                try {
                    $image = Storage::disk('images')->get($item->name);
                    $item->image = $image;
                } catch (Throwable $e) {
                    $item->image = null;
                }
            }
            return $items;
        }

        if (!isset($rows) && !isset($search)) {
            return Item::all();
        }
    }

    public function getItemImage(Request $request)
    {
        if (isset($request['item'])) {
            $name = $request['item'];
            $image = Storage::disk('images')->get($name);
            return $image;
        } else return null;
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
        $item = Item::create($this->validate(
            $request,
            [
                'barcode' => ['required', 'max:13', 'unique:items'],
                'name' => ['required', 'max:150', 'unique:items'],
                'sku' => ['max:100', 'unique:items', 'nullable'],
                'description' => ['nullable', 'max:500'],
                'item_category_id' => ['required'],
                'item_brand_id' => ['required'],
                'item_unit_id' => ['required'],
                'item_status_id' => ['required']
            ]
        ));

        $addedItem = Item::where('id', $item->id)->with('itemCategory', 'itemBrand', 'itemUnit', 'itemStatus')->first();

        if (isset($request['image'])) {
            $image = $request['image'];
            $name = $request['name'];
            if (!Storage::disk('images')->exists($name)) {
                Storage::disk('images')->put($name, $image);
                $addedItem->image = $image;
            }
        }

        return $addedItem;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        //
        $oldname = $request['name'];
        $newname = $item->name;
        $item->update($this->validate(
            $request,
            [
                'barcode' => ['required', 'max:13', Rule::unique('items')->ignore($item)],
                'name' => ['required', 'max:150',  Rule::unique('items')->ignore($item)],
                'sku' => ['max:100', 'nullable',  Rule::unique('items')->ignore($item)],
                'description' => ['nullable', 'max:500'],
                'item_category_id' => ['required'],
                'item_brand_id' => ['required'],
                'item_unit_id' => ['required'],
                'item_status_id' => ['required']
            ]
        ));


        if (Storage::disk('images')->exists($oldname)) {
            Storage::disk('images')->delete($oldname);
        }
        if (!Storage::disk('images')->exists($newname)) {
            Storage::disk('images')->put($newname, $request['image']);
        } else {
            Storage::disk('images')->delete($newname);
            Storage::disk('images')->put($newname, $request['image']);
        }

        $updItem = Item::where('id', $request['id'])->with('itemCategory', 'itemBrand', 'itemUnit', 'itemStatus')->first();

        if (Storage::disk('images')->exists($newname)) {
            $updItem->image = Storage::disk('images')->get($newname);
        } else {
            $updItem->image = null;
        }

        return $updItem;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        //
        $item->delete();
        if (Storage::disk('images')->exists($item->name)) {
            Storage::disk('images')->delete($item->name);
        }
        return $item;
    }
}

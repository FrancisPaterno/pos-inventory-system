<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
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
            return Supplier::where('name', 'like', '%' . $search . '%')->paginate($rows);
        }

        if (isset($rows) && $search == '') {
            return Supplier::paginate($rows);
        }

        if (!isset($rows) && !isset($search)) {
            return Supplier::all();
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
        $supplier = Supplier::create($this->validate($request, [
            'name' => ['required', 'max:150'],
            'BRN' => ['required', 'max:50'],
            'address' => ['required', 'max:400'],
            'contact' => ['required', 'max:30'],
            'email' => ['email', 'max:150', 'nullable']
        ]));
        return $supplier;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        //
        $supplier->update($this->validate($request, [
            'name' => ['required', 'max:150'],
            'BRN' => ['required', 'max:50'],
            'address' => ['required', 'max:400'],
            'contact' => ['required', 'max:30'],
            'email' => ['email', 'max:150', 'nullable']
        ]));
        return $supplier;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        //
        $supplier->delete();
        return $supplier;
    }
}

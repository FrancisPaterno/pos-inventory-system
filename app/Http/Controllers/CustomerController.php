<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
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
            return Customer::where('name', 'like', '%' . $search . '%')->paginate($rows);
        }

        if (isset($rows) && $search == '') {
            return Customer::paginate($rows);
        }

        if (!isset($rows) && !isset($search)) {
            return Customer::all();
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
        $customer = Customer::create($this->validate($request, [
            'name' => ['required', 'max:255', 'unique:customers'],
            'address' => ['required', 'max:255'],
            'contact' => ['required', 'max:255'],
            'email' => ['email', 'unique:customers', 'nullable']
        ]));

        return $customer;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        //
        $customer->update(
            $this->validate($request, [
                'name' => ['required', 'max:255', Rule::unique('customers')->ignore($customer)],
                'address' => ['required', 'max:255'],
                'contact' => ['required', 'max:255'],
                'email' => ['email', Rule::unique('customers')->ignore($customer), 'nullable']
            ])
        );

        return $customer;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
        $customer->delete();
        return $customer;
    }
}

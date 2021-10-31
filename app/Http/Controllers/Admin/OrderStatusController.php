<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyOrderStatusRequest;
use App\Http\Requests\StoreOrderStatusRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orderStatuses = OrderStatus::all();

        return view('admin.orderstatus.index', compact('orderStatuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderStatusRequest $request)
    {
        $orderstatus = OrderStatus::create($request->all());

        return redirect()->route('admin.orderstatus.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderStatus  $orderstatus
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderStatusRequest $request, OrderStatus $orderstatus)
    {
        $orderstatus->update($request->all());

        return redirect()->route('admin.orderstatus.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderStatus  $orderstatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderStatus $orderstatus)
    {
        $orderstatus->delete();

        return back();
    }
}

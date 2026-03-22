<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return inertia('admin/order/index', [
            'orders' => Order::with('user')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'user', 'address']);

        return inertia('admin/order/show', [
            'order' => $order,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update($data);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::where('user_id', Auth::id());

        // Filtro AJAX (fetch) o normal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return view('client.orders.partials.table', compact('orders'))->render();
        }

        return view('client.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Policy check manual simple
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items');
        return view('client.orders.show', compact('order'));
    }
}

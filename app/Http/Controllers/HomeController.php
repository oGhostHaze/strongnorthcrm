<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\OrderReturn;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // The main dashboard data is now handled by the Livewire component
        // This controller method simply returns the view that includes the Livewire component
        return view('home');
    }

    /**
     * Get quick stats for AJAX requests.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuickStats()
    {
        // This method could be used for AJAX refreshes of dashboard data
        return response()->json([
            'totalProducts' => Product::count(),
            'lowStockCount' => Product::whereRaw('product_qty < reorder_level')->count(),
            'pendingDeliveries' => Delivery::where('print_count', 0)->count(),
            'totalOrders' => Order::count(),
        ]);
    }
}

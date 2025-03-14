<?php

namespace App\Http\Livewire;

use App\Models\Delivery;
use App\Models\MerchandiseItem;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderReturnInfo;
use App\Models\Product;
use App\Models\Stockin;
use App\Models\SupplyItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Home extends Component
{
    public $dateRange = 'week';
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = Carbon::now()->subWeek()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function setDateRange($range)
    {
        $this->dateRange = $range;

        switch ($range) {
            case 'today':
                $this->startDate = Carbon::today()->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'week':
                $this->startDate = Carbon::now()->subWeek()->format('Y-m-d');
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
            case 'month':
                $this->startDate = Carbon::now()->subMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
            case 'year':
                $this->startDate = Carbon::now()->subYear()->format('Y-m-d');
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
        }
    }

    public function render()
    {
        // Total counts
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalSupplies = SupplyItem::count();
        $totalMerchandise = MerchandiseItem::count();

        // Products below reorder level
        $lowStockProducts = Product::whereRaw('product_qty < reorder_level')
            ->orWhere('product_qty', '<=', 5)
            ->orderBy('product_qty')
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrders = Order::orderByDesc('oa_date')
            ->limit(5)
            ->get();

        // Pending deliveries
        $pendingDeliveries = Delivery::where('print_count', 0)
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        // Pending deliveries count
        $pendingDeliveriesCount = Delivery::where('print_count', 0)->count();

        // Recent stock-ins
        $recentStockins = Stockin::orderByDesc('date')
            ->limit(5)
            ->get();

        // Recent returns
        $recentReturns = OrderReturnInfo::orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Total returns count
        $totalReturns = OrderReturnInfo::count();

        // Total inventory value
        $inventoryValue = Product::sum(DB::raw('product_qty * product_price'));

        // Orders within date range
        $ordersInRange = Order::whereBetween('oa_date', [$this->startDate, $this->endDate])
            ->count();

        // Returns within date range
        $returnsInRange = OrderReturnInfo::whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->count();

        // Stockins within date range
        $stockinsInRange = Stockin::whereBetween('date', [$this->startDate, $this->endDate])
            ->count();

        // Top selling products
        $topSellingProducts = DB::table('delivery_items')
            ->select('delivery_items.product_id', DB::raw('SUM(item_qty) as total_sold'))
            ->join('tblproducts', 'delivery_items.product_id', '=', 'tblproducts.product_id')
            ->groupBy('delivery_items.product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Get product details for top selling products
        foreach ($topSellingProducts as $product) {
            $productDetails = Product::find($product->product_id);
            if ($productDetails) {
                $product->product_description = $productDetails->product_description;
                $product->product_price = $productDetails->product_price;
            } else {
                $product->product_description = 'Unknown Product';
                $product->product_price = 0;
            }
        }

        return view('livewire.home', [
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalSupplies' => $totalSupplies,
            'totalMerchandise' => $totalMerchandise,
            'lowStockProducts' => $lowStockProducts,
            'recentOrders' => $recentOrders,
            'pendingDeliveries' => $pendingDeliveries,
            'pendingDeliveriesCount' => $pendingDeliveriesCount,
            'recentStockins' => $recentStockins,
            'recentReturns' => $recentReturns,
            'totalReturns' => $totalReturns,
            'inventoryValue' => $inventoryValue,
            'ordersInRange' => $ordersInRange,
            'returnsInRange' => $returnsInRange,
            'stockinsInRange' => $stockinsInRange,
            'topSellingProducts' => $topSellingProducts,
        ]);
    }
}

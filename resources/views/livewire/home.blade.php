<div>
    <div class="container-fluid">
        <!-- Date range filter -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Dashboard Overview</h5>
                <div class="btn-group btn-group-sm mt-2" role="group" aria-label="Date range">
                    <button type="button" class="btn btn-outline-primary {{ $dateRange == 'today' ? 'active' : '' }}"
                        wire:click="setDateRange('today')">Today</button>
                    <button type="button" class="btn btn-outline-primary {{ $dateRange == 'week' ? 'active' : '' }}"
                        wire:click="setDateRange('week')">Week</button>
                    <button type="button" class="btn btn-outline-primary {{ $dateRange == 'month' ? 'active' : '' }}"
                        wire:click="setDateRange('month')">Month</button>
                    <button type="button" class="btn btn-outline-primary {{ $dateRange == 'year' ? 'active' : '' }}"
                        wire:click="setDateRange('year')">Year</button>
                </div>
            </div>
            <div class="card-body">
                <div class="text-muted">
                    Showing data from {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to
                    {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                </div>
            </div>
        </div>

        <!-- Summary cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-4">
                <div class="card border-left-primary h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Products</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalProducts) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-box-open fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-left-success h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Inventory Value</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₱{{ number_format($inventoryValue, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-peso-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-left-info h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Orders ({{ ucfirst($dateRange) }})</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($ordersInRange) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-regular fa-copy fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-left-warning h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending Deliveries</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($pendingDeliveriesCount) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-truck fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-4">
                <div class="card border-left-danger h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Returns ({{ ucfirst($dateRange) }})</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($returnsInRange) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-rotate-left fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-left-secondary h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Stock-ins ({{ ucfirst($dateRange) }})</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stockinsInRange) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-dolly fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-left-primary h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Merchandise Items</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totalMerchandise) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-brands fa-shopify fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-left-info h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Supply Items</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalSupplies) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-sitemap fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content area -->
        <div class="row">
            <!-- Low stock products -->
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Low Stock Products</h6>
                        <a href="{{ route('product.list') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if ($lowStockProducts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Category</th>
                                            <th>Current Qty</th>
                                            <th>Reorder Level</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lowStockProducts as $product)
                                            <tr
                                                class="{{ $product->product_qty <= 0 ? 'table-danger' : 'table-warning' }}">
                                                <td>{{ $product->product_description }}</td>
                                                <td>{{ $product->category ? $product->category->category_name : 'N/A' }}
                                                </td>
                                                <td>{{ $product->product_qty }}</td>
                                                <td>{{ $product->reorder_level ?: 5 }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center p-4">
                                <span class="text-success">
                                    <i class="fa-solid fa-check-circle fa-3x mb-3"></i>
                                </span>
                                <p>No products are low in stock!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top selling products -->
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                    </div>
                    <div class="card-body">
                        @if ($topSellingProducts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Units Sold</th>
                                            <th>Total Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topSellingProducts as $product)
                                            <tr>
                                                <td>{{ $product->product_description }}</td>
                                                <td>₱{{ number_format($product->product_price, 2) }}</td>
                                                <td>{{ number_format($product->total_sold) }}</td>
                                                <td>₱{{ number_format($product->total_sold * $product->product_price, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center p-4">
                                <span class="text-muted">
                                    <i class="fa-solid fa-chart-line fa-3x mb-3"></i>
                                </span>
                                <p>No sales data available yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent orders -->
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                        <a href="{{ route('order.agreements') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if ($recentOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Client</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentOrders as $order)
                                            <tr>
                                                <td>{{ $order->oa_number }}</td>
                                                <td>{{ $order->oa_client }}</td>
                                                <td>{{ \Carbon\Carbon::parse($order->oa_date)->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('order.agreements.view', $order) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center p-4">
                                <span class="text-muted">
                                    <i class="fa-regular fa-copy fa-3x mb-3"></i>
                                </span>
                                <p>No recent orders.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pending deliveries -->
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Pending Deliveries</h6>
                        <a href="{{ route('order.delivery.list') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if ($pendingDeliveries->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>DR #</th>
                                            <th>Client</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pendingDeliveries as $delivery)
                                            <tr>
                                                <td>{{ $delivery->transno }}</td>
                                                <td>{{ $delivery->client }}</td>
                                                <td>{{ \Carbon\Carbon::parse($delivery->date)->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('order.delivery.view', $delivery->transno) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center p-4">
                                <span class="text-success">
                                    <i class="fa-solid fa-check-circle fa-3x mb-3"></i>
                                </span>
                                <p>No pending deliveries!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent returns -->
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Returns</h6>
                        <a href="{{ route('order.returns.list') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if ($recentReturns->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>RSN #</th>
                                            <th>OA #</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentReturns as $return)
                                            <tr>
                                                <td>RSN-{{ $return->id }}</td>
                                                <td>{{ $return->oa_no }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $return->status == 'Approved' ? 'success' : 'warning' }}">
                                                        {{ $return->status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('order.returns.view', $return->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center p-4">
                                <span class="text-muted">
                                    <i class="fa-solid fa-rotate-left fa-3x mb-3"></i>
                                </span>
                                <p>No recent returns.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent stockins -->
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Stock-Ins</h6>
                        <a href="{{ route('product.stockin') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if ($recentStockins->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Date</th>
                                            <th>By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentStockins as $stockin)
                                            <tr>
                                                <td>{{ $stockin->product->product_description }}</td>
                                                <td>{{ $stockin->stockin_qty }}</td>
                                                <td>{{ \Carbon\Carbon::parse($stockin->date)->format('M d, Y') }}</td>
                                                <td>{{ $stockin->user ? $stockin->user->emp_name : 'System' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center p-4">
                                <span class="text-muted">
                                    <i class="fa-solid fa-dolly fa-3x mb-3"></i>
                                </span>
                                <p>No recent stock-ins.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

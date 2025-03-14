<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Item ID</th>
            <th>Item Name</th>
            <th>Initial Stock</th>
            <th>Stock In</th>
            <th>Delivered</th>
            <th>Returns</th>
            <th>Current Stock</th>
            <th>Unit Price</th>
            <th>Stock Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportData as $item)
            <tr class="{{ $item['current_stock'] <= 0 ? 'table-danger' : '' }}">
                <td>{{ $item['item_id'] }}</td>
                <td>{{ $item['item_name'] }}</td>
                <td class="text-right">{{ number_format($item['initial_stock']) }}</td>
                <td class="text-right">{{ number_format($item['stock_in']) }}</td>
                <td class="text-right">{{ number_format($item['delivered']) }}</td>
                <td class="text-right">{{ number_format($item['returns']) }}</td>
                <td class="text-right font-weight-bold">{{ number_format($item['current_stock']) }}</td>
                <td class="text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                <td class="text-right">₱{{ number_format($item['stock_value'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="table-secondary">
            <td colspan="6" class="text-right font-weight-bold">Totals:</td>
            <td class="text-right font-weight-bold">
                {{ number_format(array_sum(array_column($reportData, 'current_stock'))) }}</td>
            <td></td>
            <td class="text-right font-weight-bold">
                ₱{{ number_format(array_sum(array_column($reportData, 'stock_value')), 2) }}</td>
        </tr>
    </tfoot>
</table>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Merchandise Stock Movement Summary</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th class="text-right">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Initial Stock Total</td>
                                <td class="text-right">
                                    {{ number_format(array_sum(array_column($reportData, 'initial_stock'))) }}</td>
                            </tr>
                            <tr>
                                <td>Total Stock Added</td>
                                <td class="text-right">
                                    {{ number_format(array_sum(array_column($reportData, 'stock_in'))) }}</td>
                            </tr>
                            <tr>
                                <td>Total Delivered</td>
                                <td class="text-right">
                                    {{ number_format(array_sum(array_column($reportData, 'delivered'))) }}</td>
                            </tr>
                            <tr>
                                <td>Total Returns</td>
                                <td class="text-right">
                                    {{ number_format(array_sum(array_column($reportData, 'returns'))) }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Net Change</td>
                                <td class="text-right font-weight-bold">
                                    @php
                                        $netChange =
                                            array_sum(array_column($reportData, 'stock_in')) -
                                            array_sum(array_column($reportData, 'delivered')) +
                                            array_sum(array_column($reportData, 'returns'));
                                    @endphp
                                    {{ number_format($netChange) }}
                                    <span class="text-{{ $netChange >= 0 ? 'success' : 'danger' }}">
                                        ({{ $netChange >= 0 ? '+' : '' }}{{ number_format($netChange) }})
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Current Stock Total</td>
                                <td class="text-right font-weight-bold">
                                    {{ number_format(array_sum(array_column($reportData, 'current_stock'))) }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Total Stock Value</td>
                                <td class="text-right font-weight-bold">
                                    ₱{{ number_format(array_sum(array_column($reportData, 'stock_value')), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Stock Status</h6>
            </div>
            <div class="card-body">
                @php
                    $inStock = 0;
                    $outOfStock = 0;
                    $lowStock = 0;

                    foreach ($reportData as $item) {
                        if ($item['current_stock'] <= 0) {
                            $outOfStock++;
                        } elseif ($item['current_stock'] < 5) {
                            $lowStock++;
                        } else {
                            $inStock++;
                        }
                    }
                @endphp

                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <td>Items In Stock</td>
                            <td class="text-right">
                                <span class="badge bg-success">{{ $inStock }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Items with Low Stock (< 5)</td>
                            <td class="text-right">
                                <span class="badge bg-warning">{{ $lowStock }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Items Out of Stock</td>
                            <td class="text-right">
                                <span class="badge bg-danger">{{ $outOfStock }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Total Items</td>
                            <td class="text-right">
                                <span class="badge bg-info">{{ count($reportData) }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

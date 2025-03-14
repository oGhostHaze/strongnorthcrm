<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Item ID</th>
            <th>Item Name</th>
            <th>Category</th>
            <th>Location</th>
            <th>Unit</th>
            <th>Initial Qty</th>
            <th>Added</th>
            <th>Disposed</th>
            <th>Current Qty</th>
            <th>Unit Price</th>
            <th>Total Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportData as $item)
            <tr class="{{ $item['current_qty'] <= 0 ? 'table-danger' : '' }}">
                <td>{{ $item['item_id'] }}</td>
                <td>{{ $item['item_name'] }}</td>
                <td>{{ $item['category'] }}</td>
                <td>{{ $item['location'] }}</td>
                <td>{{ $item['unit'] }}</td>
                <td class="text-right">{{ number_format($item['initial_qty']) }}</td>
                <td class="text-right">{{ number_format($item['added']) }}</td>
                <td class="text-right">{{ number_format($item['disposed']) }}</td>
                <td class="text-right font-weight-bold">{{ number_format($item['current_qty']) }}</td>
                <td class="text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                <td class="text-right">₱{{ number_format($item['total_value'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="table-secondary">
            <td colspan="8" class="text-right font-weight-bold">Totals:</td>
            <td class="text-right font-weight-bold">
                {{ number_format(array_sum(array_column($reportData, 'current_qty'))) }}</td>
            <td></td>
            <td class="text-right font-weight-bold">
                ₱{{ number_format(array_sum(array_column($reportData, 'total_value')), 2) }}</td>
        </tr>
    </tfoot>
</table>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Supplies by Category</h6>
            </div>
            <div class="card-body">
                @php
                    $categorySummary = [];
                    foreach ($reportData as $item) {
                        $category = $item['category'];
                        if (!isset($categorySummary[$category])) {
                            $categorySummary[$category] = [
                                'count' => 0,
                                'quantity' => 0,
                                'value' => 0,
                            ];
                        }
                        $categorySummary[$category]['count']++;
                        $categorySummary[$category]['quantity'] += $item['current_qty'];
                        $categorySummary[$category]['value'] += $item['total_value'];
                    }
                @endphp

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="text-right">Items</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categorySummary as $category => $data)
                            <tr>
                                <td>{{ $category }}</td>
                                <td class="text-right">{{ number_format($data['count']) }}</td>
                                <td class="text-right">{{ number_format($data['quantity']) }}</td>
                                <td class="text-right">₱{{ number_format($data['value'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Supplies by Location</h6>
            </div>
            <div class="card-body">
                @php
                    $locationSummary = [];
                    foreach ($reportData as $item) {
                        $location = $item['location'];
                        if (!isset($locationSummary[$location])) {
                            $locationSummary[$location] = [
                                'count' => 0,
                                'quantity' => 0,
                                'value' => 0,
                            ];
                        }
                        $locationSummary[$location]['count']++;
                        $locationSummary[$location]['quantity'] += $item['current_qty'];
                        $locationSummary[$location]['value'] += $item['total_value'];
                    }
                @endphp

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th class="text-right">Items</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($locationSummary as $location => $data)
                            <tr>
                                <td>{{ $location }}</td>
                                <td class="text-right">{{ number_format($data['count']) }}</td>
                                <td class="text-right">{{ number_format($data['quantity']) }}</td>
                                <td class="text-right">₱{{ number_format($data['value'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Supply Movement Summary</h6>
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
                                <td>Initial Quantity Total</td>
                                <td class="text-right">
                                    {{ number_format(array_sum(array_column($reportData, 'initial_qty'))) }}</td>
                            </tr>
                            <tr>
                                <td>Total Added</td>
                                <td class="text-right">
                                    {{ number_format(array_sum(array_column($reportData, 'added'))) }}</td>
                            </tr>
                            <tr>
                                <td>Total Disposed</td>
                                <td class="text-right">
                                    {{ number_format(array_sum(array_column($reportData, 'disposed'))) }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Net Change</td>
                                <td class="text-right font-weight-bold">
                                    @php
                                        $netChange =
                                            array_sum(array_column($reportData, 'added')) -
                                            array_sum(array_column($reportData, 'disposed'));
                                    @endphp
                                    {{ number_format($netChange) }}
                                    <span class="text-{{ $netChange >= 0 ? 'success' : 'danger' }}">
                                        ({{ $netChange >= 0 ? '+' : '' }}{{ number_format($netChange) }})
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Current Quantity Total</td>
                                <td class="text-right font-weight-bold">
                                    {{ number_format(array_sum(array_column($reportData, 'current_qty'))) }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Total Value</td>
                                <td class="text-right font-weight-bold">
                                    ₱{{ number_format(array_sum(array_column($reportData, 'total_value')), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

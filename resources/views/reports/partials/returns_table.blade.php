<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Return Slip #</th>
            <th>Order Number</th>
            <th>Date Returned</th>
            <th>Product Code</th>
            <th>Product Description</th>
            <th>Quantity</th>
            <th>Item Type</th>
            <th>Reason</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportData as $return)
            <tr>
                <td>{{ $return['return_slip_number'] }}</td>
                <td>{{ $return['order_number'] }}</td>
                <td>{{ $return['date_returned'] ? \Carbon\Carbon::parse($return['date_returned'])->format('M d, Y') : '-' }}
                </td>
                <td>{{ $return['product_code'] }}</td>
                <td>{{ $return['product_description'] }}</td>
                <td class="text-right">{{ number_format($return['quantity_returned']) }}</td>
                <td>{{ $return['item_type'] }}</td>
                <td>{{ $return['reason'] ?: '-' }}</td>
                <td>
                    <span class="badge bg-{{ $return['status'] == 'Approved' ? 'success' : 'warning' }}">
                        {{ $return['status'] }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="table-secondary">
            <td colspan="5" class="text-right font-weight-bold">Total Quantity Returned:</td>
            <td class="text-right font-weight-bold">
                {{ number_format(array_sum(array_column($reportData, 'quantity_returned'))) }}</td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Return Status Summary</h6>
            </div>
            <div class="card-body">
                @php
                    $statusSummary = [];
                    foreach ($reportData as $return) {
                        $status = $return['status'];
                        if (!isset($statusSummary[$status])) {
                            $statusSummary[$status] = [
                                'count' => 0,
                                'quantity' => 0,
                            ];
                        }
                        $statusSummary[$status]['count']++;
                        $statusSummary[$status]['quantity'] += $return['quantity_returned'];
                    }
                @endphp

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th class="text-right">Count</th>
                            <th class="text-right">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($statusSummary as $status => $data)
                            <tr>
                                <td>
                                    <span class="badge bg-{{ $status == 'Approved' ? 'success' : 'warning' }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="text-right">{{ number_format($data['count']) }}</td>
                                <td class="text-right">{{ number_format($data['quantity']) }}</td>
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
                <h6 class="mb-0">Return Type Summary</h6>
            </div>
            <div class="card-body">
                @php
                    $typeSummary = [];
                    foreach ($reportData as $return) {
                        $type = $return['item_type'];
                        if (!isset($typeSummary[$type])) {
                            $typeSummary[$type] = [
                                'count' => 0,
                                'quantity' => 0,
                            ];
                        }
                        $typeSummary[$type]['count']++;
                        $typeSummary[$type]['quantity'] += $return['quantity_returned'];
                    }
                @endphp

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Item Type</th>
                            <th class="text-right">Count</th>
                            <th class="text-right">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($typeSummary as $type => $data)
                            <tr>
                                <td>{{ $type }}</td>
                                <td class="text-right">{{ number_format($data['count']) }}</td>
                                <td class="text-right">{{ number_format($data['quantity']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

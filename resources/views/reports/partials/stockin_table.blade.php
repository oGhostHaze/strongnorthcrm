<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Product Code</th>
            <th>Product Description</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Recorded By</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportData as $stockin)
            <tr>
                <td>{{ $stockin['stockin_id'] }}</td>
                <td>{{ \Carbon\Carbon::parse($stockin['date'])->format('M d, Y') }}</td>
                <td>{{ $stockin['product_code'] }}</td>
                <td>{{ $stockin['product_description'] }}</td>
                <td>{{ $stockin['category'] }}</td>
                <td class="text-right">{{ number_format($stockin['quantity']) }}</td>
                <td>{{ $stockin['recorded_by'] }}</td>
                <td>{{ $stockin['remarks'] ?: '-' }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="table-secondary">
            <td colspan="5" class="text-right font-weight-bold">Total Quantity:</td>
            <td class="text-right font-weight-bold">{{ number_format(array_sum(array_column($reportData, 'quantity'))) }}
            </td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Stock-in by Category</h6>
            </div>
            <div class="card-body">
                @php
                    $categorySummary = [];
                    foreach ($reportData as $stockin) {
                        $category = $stockin['category'];
                        if (!isset($categorySummary[$category])) {
                            $categorySummary[$category] = [
                                'count' => 0,
                                'quantity' => 0,
                            ];
                        }
                        $categorySummary[$category]['count']++;
                        $categorySummary[$category]['quantity'] += $stockin['quantity'];
                    }
                @endphp

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="text-right">Count</th>
                            <th class="text-right">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categorySummary as $category => $data)
                            <tr>
                                <td>{{ $category }}</td>
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
                <h6 class="mb-0">Stock-in by Date</h6>
            </div>
            <div class="card-body">
                @php
                    $dateSummary = [];
                    foreach ($reportData as $stockin) {
                        $date = \Carbon\Carbon::parse($stockin['date'])->format('Y-m-d');
                        if (!isset($dateSummary[$date])) {
                            $dateSummary[$date] = [
                                'count' => 0,
                                'quantity' => 0,
                            ];
                        }
                        $dateSummary[$date]['count']++;
                        $dateSummary[$date]['quantity'] += $stockin['quantity'];
                    }

                    // Sort by date
                    ksort($dateSummary);
                @endphp

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-right">Count</th>
                            <th class="text-right">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dateSummary as $date => $data)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</td>
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

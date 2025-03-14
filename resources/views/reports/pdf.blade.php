<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
        }

        .subtitle {
            font-size: 14px;
            margin: 0 0 5px 0;
        }

        .meta {
            margin-bottom: 20px;
        }

        .meta p {
            margin: 0 0 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .table-secondary {
            background-color: #f8f9fa;
        }

        .table-warning {
            background-color: #fff3cd;
        }

        .table-danger {
            background-color: #f8d7da;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 10px;
            border-radius: 3px;
            color: white;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .badge-info {
            background-color: #17a2b8;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            text-align: center;
            color: #6c757d;
        }

        .summary-section {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .summary-table {
            width: 50%;
            margin-bottom: 15px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Saladmaster Inventory Management System</h1>
        <div class="subtitle">{{ $reportTitle }}</div>
    </div>

    <div class="meta">
        <p><strong>Date Range:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to
            {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>

        @if (isset($additionalCriteria) && !empty($additionalCriteria))
            <p><strong>Filters:</strong>
                @foreach ($additionalCriteria as $key => $value)
                    @if (!empty($value))
                        {{ str_replace('_', ' ', ucfirst($key)) }}: {{ $value }},
                    @endif
                @endforeach
            </p>
        @endif
    </div>

    @if (empty($reportData))
        <p>No data found for the selected criteria.</p>
    @else
        @if ($reportType == 'inventory')
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th class="text-right">Initial</th>
                        <th class="text-right">In</th>
                        <th class="text-right">Out</th>
                        <th class="text-right">Returns</th>
                        <th class="text-right">Current</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportData as $item)
                        <tr
                            class="{{ $item['current_stock'] < $item['reorder_level'] ? 'table-warning' : '' }} {{ $item['current_stock'] <= 0 ? 'table-danger' : '' }}">
                            <td>{{ $item['code'] }}</td>
                            <td>{{ $item['description'] }}</td>
                            <td>{{ $item['category'] }}</td>
                            <td class="text-right">{{ number_format($item['initial_stock']) }}</td>
                            <td class="text-right">{{ number_format($item['stock_in']) }}</td>
                            <td class="text-right">{{ number_format($item['stock_out']) }}</td>
                            <td class="text-right">{{ number_format($item['returns']) }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($item['current_stock']) }}</td>
                            <td class="text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                            <td class="text-right">₱{{ number_format($item['stock_value'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <td colspan="7" class="text-right font-weight-bold">Totals:</td>
                        <td class="text-right font-weight-bold">
                            {{ number_format(array_sum(array_column($reportData, 'current_stock'))) }}</td>
                        <td></td>
                        <td class="text-right font-weight-bold">
                            ₱{{ number_format(array_sum(array_column($reportData, 'stock_value')), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @elseif($reportType == 'sales')
            <!-- Custom sales report based on grouping -->
            @if (isset($additionalCriteria['group_by']) && $additionalCriteria['group_by'] == 'product')
                <table>
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th class="text-right">Qty Sold</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportData as $item)
                            <tr>
                                <td>{{ $item['code'] }}</td>
                                <td>{{ $item['description'] }}</td>
                                <td>{{ $item['category'] }}</td>
                                <td class="text-right">{{ number_format($item['quantity_sold']) }}</td>
                                <td class="text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                                <td class="text-right">₱{{ number_format($item['total_amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-secondary">
                            <td colspan="3" class="text-right font-weight-bold">Totals:</td>
                            <td class="text-right font-weight-bold">
                                {{ number_format(array_sum(array_column($reportData, 'quantity_sold'))) }}</td>
                            <td></td>
                            <td class="text-right font-weight-bold">
                                ₱{{ number_format(array_sum(array_column($reportData, 'total_amount')), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            @elseif(isset($additionalCriteria['group_by']) && $additionalCriteria['group_by'] == 'date')
                <!-- Date grouping for sales -->
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-right">Items</th>
                            <th class="text-right">Qty Sold</th>
                            <th class="text-right">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportData as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</td>
                                <td class="text-right">{{ number_format($item['items_count']) }}</td>
                                <td class="text-right">{{ number_format($item['quantity_sold']) }}</td>
                                <td class="text-right">₱{{ number_format($item['total_amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-secondary">
                            <td class="text-right font-weight-bold">Totals:</td>
                            <td class="text-right font-weight-bold">
                                {{ number_format(array_sum(array_column($reportData, 'items_count'))) }}</td>
                            <td class="text-right font-weight-bold">
                                {{ number_format(array_sum(array_column($reportData, 'quantity_sold'))) }}</td>
                            <td class="text-right font-weight-bold">
                                ₱{{ number_format(array_sum(array_column($reportData, 'total_amount')), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <!-- Default sales report -->
                <table>
                    <thead>
                        <tr>
                            <th>DR Number</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Product</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportData as $item)
                            <tr>
                                <td>{{ $item['dr_number'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</td>
                                <td>{{ $item['client'] }}</td>
                                <td>{{ $item['description'] }}</td>
                                <td class="text-right">{{ number_format($item['quantity']) }}</td>
                                <td class="text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                                <td class="text-right">₱{{ number_format($item['total_amount'], 2) }}</td>
                                <td>{{ $item['status'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-secondary">
                            <td colspan="4" class="text-right font-weight-bold">Totals:</td>
                            <td class="text-right font-weight-bold">
                                {{ number_format(array_sum(array_column($reportData, 'quantity'))) }}</td>
                            <td></td>
                            <td class="text-right font-weight-bold">
                                ₱{{ number_format(array_sum(array_column($reportData, 'total_amount')), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        @elseif($reportType == 'payments')
            <table>
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Order #</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Mode</th>
                        <th class="text-right">Amount</th>
                        <th>Status</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportData as $payment)
                        <tr>
                            <td>{{ $payment['payment_id'] }}</td>
                            <td>{{ $payment['order_number'] }}</td>
                            <td>{{ $payment['client'] }}</td>
                            <td>{{ $payment['payment_date'] ? \Carbon\Carbon::parse($payment['payment_date'])->format('M d, Y') : '-' }}
                            </td>
                            <td>{{ $payment['payment_mode'] }}</td>
                            <td class="text-right">₱{{ number_format($payment['amount'], 2) }}</td>
                            <td>{{ $payment['status'] }}</td>
                            <td>{{ $payment['reference_no'] ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <td colspan="5" class="text-right font-weight-bold">Total Amount:</td>
                        <td class="text-right font-weight-bold">
                            ₱{{ number_format(array_sum(array_column($reportData, 'amount')), 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Simple payment summary -->
            <div class="summary-section">
                <div class="summary-title">Payment Summary</div>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th class="text-right">Count</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $statusSummary = [];
                            foreach ($reportData as $payment) {
                                $status = $payment['status'];
                                if (!isset($statusSummary[$status])) {
                                    $statusSummary[$status] = [
                                        'count' => 0,
                                        'amount' => 0,
                                    ];
                                }
                                $statusSummary[$status]['count']++;
                                $statusSummary[$status]['amount'] += $payment['amount'];
                            }
                        @endphp

                        @foreach ($statusSummary as $status => $data)
                            <tr>
                                <td>{{ $status }}</td>
                                <td class="text-right">{{ number_format($data['count']) }}</td>
                                <td class="text-right">₱{{ number_format($data['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Generic table for other report types -->
            <table>
                <thead>
                    <tr>
                        @foreach (array_keys($reportData[0]) as $column)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportData as $row)
                        <tr>
                            @foreach ($row as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="summary-section">
            <p><strong>Total Records:</strong> {{ count($reportData) }}</p>

            @if ($reportType == 'inventory')
                <p><strong>Total Stock Value:</strong>
                    ₱{{ number_format(array_sum(array_column($reportData, 'stock_value')), 2) }}</p>
            @elseif($reportType == 'sales')
                @if (isset($additionalCriteria['group_by']))
                    <p><strong>Total Sales:</strong>
                        ₱{{ number_format(array_sum(array_column($reportData, 'total_amount')), 2) }}</p>
                @else
                    <p><strong>Total Sales:</strong>
                        ₱{{ number_format(array_sum(array_column($reportData, 'total_amount')), 2) }}</p>
                @endif
            @elseif($reportType == 'payments')
                <p><strong>Total Payments:</strong>
                    ₱{{ number_format(array_sum(array_column($reportData, 'amount')), 2) }}</p>
            @endif
        </div>
    @endif

    <div class="footer">
        <p>Report generated on {{ date('F d, Y h:i A') }} | Saladmaster Inventory Management System</p>
    </div>
</body>

</html>

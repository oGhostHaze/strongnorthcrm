<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Records Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #fff;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            margin: 0;
            padding: 0;
            font-size: 14px;
        }

        .filters {
            margin-bottom: 20px;
        }

        .filters h2 {
            font-size: 16px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-top: 0;
        }

        .filter-item {
            margin-bottom: 8px;
            display: flex;
        }

        .filter-label {
            font-weight: bold;
            width: 120px;
        }

        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .summary-card {
            width: 23%;
            padding: 10px;
            border-radius: 4px;
            color: white;
            margin-bottom: 10px;
        }

        .card-posted {
            background-color: #28a745;
        }

        .card-unposted {
            background-color: #ffc107;
            color: #212529;
        }

        .card-onhold {
            background-color: #17a2b8;
        }

        .card-voided {
            background-color: #dc3545;
        }

        .summary-card h3 {
            margin: 0;
            font-size: 14px;
        }

        .summary-card p {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            white-space: nowrap;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-end {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            font-size: 11px;
            color: #666;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }

        .badge-posted {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-unposted {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-onhold {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .badge-voided {
            background-color: #f8d7da;
            color: #721c24;
        }

        .small {
            font-size: 11px;
            color: #666;
        }

        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px;
            background: #4285f4;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        @media print {
            .print-button {
                display: none;
            }

            body {
                padding: 0;
                margin: 0;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .summary-card {
                border: 1px solid #ddd;
            }

            .card-posted {
                background-color: #ffffff;
                color: #000;
                border-left: 5px solid #28a745;
            }

            .card-unposted {
                background-color: #ffffff;
                color: #000;
                border-left: 5px solid #ffc107;
            }

            .card-onhold {
                background-color: #ffffff;
                color: #000;
                border-left: 5px solid #17a2b8;
            }

            .card-voided {
                background-color: #ffffff;
                color: #000;
                border-left: 5px solid #dc3545;
            }
        }
    </style>
</head>

<body>
    <button class="print-button" onclick="window.print()">Print Report</button>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PAYMENT RECORDS REPORT</h1>
            <p>StrongNorth Enterprises OPC</p>
            <p>{{ date('F d, Y') }}</p>
        </div>

        <!-- Filters -->
        <div class="filters">
            <h2>Filters Applied</h2>
            <div class="filter-item">
                <span class="filter-label">Date Range:</span>
                <span>{{ date('F d, Y', strtotime($date_from)) }} to {{ date('F d, Y', strtotime($date_to)) }}</span>
            </div>
            <div class="filter-item">
                <span class="filter-label">Status:</span>
                <span>{{ $status ?: 'All' }}</span>
            </div>
            <div class="filter-item">
                <span class="filter-label">Payment Mode:</span>
                <span>{{ $payment_mode ?: 'All' }}</span>
            </div>
            <div class="filter-item">
                <span class="filter-label">Delivery:</span>
                <span>
                    @if ($delivery_id)
                        @php
                            $delivery = \App\Models\Delivery::find($delivery_id);
                            echo $delivery ? $delivery->transno : 'Unknown';
                        @endphp
                    @else
                        All
                    @endif
                </span>
            </div>
            @if ($search)
                <div class="filter-item">
                    <span class="filter-label">Search Term:</span>
                    <span>{{ $search }}</span>
                </div>
            @endif
        </div>

        <!-- Summary -->
        <div class="summary-cards">
            <div class="summary-card card-posted">
                <h3>Posted Payments</h3>
                <p>₱{{ number_format($total_posted, 2) }}</p>
            </div>
            <div class="summary-card card-unposted">
                <h3>Unposted Payments</h3>
                <p>₱{{ number_format($total_unposted, 2) }}</p>
            </div>
            <div class="summary-card card-onhold">
                <h3>On-hold Payments</h3>
                <p>₱{{ number_format($total_on_hold, 2) }}</p>
            </div>
            <div class="summary-card card-voided">
                <h3>Voided Payments</h3>
                <p>₱{{ number_format($total_voided, 2) }}</p>
            </div>
        </div>

        <!-- Payments Table -->
        <table>
            <thead>
                <tr>
                    <th>Receipt #</th>
                    <th>Date</th>
                    <th>Order #</th>
                    <th>Client</th>
                    <th>Delivery</th>
                    <th>Payment Mode</th>
                    <th>Reference #</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>
                            @if ($payment->batch_receipt_number)
                                {{ $payment->receipt_number }}
                                <div class="small">Batch: {{ $payment->batch_receipt_number }}</div>
                            @else
                                {{ $payment->receipt_number ?? 'PR-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                            @endif
                        </td>
                        <td>{{ date('M d, Y', strtotime($payment->date_of_payment)) }}</td>
                        <td>{{ $payment->details->oa_number }}</td>
                        <td>{{ $payment->details->oa_client }}</td>
                        <td>
                            @if ($payment->delivery)
                                {{ $payment->delivery->transno }}
                                <div class="small">{{ date('M d, Y', strtotime($payment->delivery->date)) }}</div>
                            @else
                                <span class="small">No delivery</span>
                            @endif
                        </td>
                        <td>{{ $payment->mop }}</td>
                        <td>{{ $payment->reference_no }}</td>
                        <td class="text-end">₱{{ number_format($payment->amount, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($payment->status) }}">
                                {{ $payment->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center;">No payment records found</td>
                    </tr>
                @endforelse

                @if (count($payments) > 0)
                    <tr class="total-row">
                        <td colspan="7" class="text-end">TOTAL:</td>
                        <td class="text-end">₱{{ number_format($payments->sum('amount'), 2) }}</td>
                        <td></td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Report generated on {{ date('F d, Y h:i A') }} by {{ Auth::user()->emp_name }}</p>
        </div>
    </div>
</body>

</html>

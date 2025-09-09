<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt {{ $payment->receipt_number ?? 'PR-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
        }

        .receipt-container {
            max-width: 210mm;
            /* A4 width */
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: white;
        }

        .receipt-title {
            background-color: #6c757d;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        .payment-type {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 8px;
        }

        .checkbox-item {
            display: inline-block;
            margin-right: 20px;
        }

        .signature-line {
            border-top: 1px solid black;
            margin-top: 40px;
            padding-top: 5px;
        }

        .copy-section {
            display: flex;
            margin-top: 30px;
        }

        .copy-item {
            flex: 1;
            text-align: center;
            border: 1px solid #ddd;
            padding: 5px 0;
        }

        @media print {
            body {
                padding: 0;
            }

            .receipt-container {
                border: none;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print mb-3 text-center">
        <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
        <a href="{{ route('receipt.show', ['payment_id' => $payment->id]) }}" class="btn btn-secondary">Back to
            Receipt</a>
    </div>

    <div class="receipt-container">
        <div class="receipt-title">PAYMENT RECEIPT</div>

        <div class="receipt-header">
            <h5>STRONGNORTH ENTERPRISES OPC</h5>
            <p>Unit 9 & 10 VYV Bldg., Brgy. 1, San Nicolas, Ilocos Norte</p>
            <p>VAT: 666-167-922-000</p>
            <h5 class="mt-3">PAYMENT RECEIPT</h5>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <p><strong>Sold to:</strong> {{ $payment->details->oa_client }}</p>
                <p><strong>Address:</strong> {{ $payment->details->oa_address }}</p>
                <p><strong>Buss. Style:</strong></p>
            </div>
            <div class="col-6">
                <p><strong>PR NO:</strong>
                    {{ $payment->receipt_number ?? 'PR-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Date:</strong> {{ date('Y-m-d') }}</p>
                <p><strong>TIN:</strong></p>
            </div>
        </div>

        <div class="payment-type">
            <div class="checkbox-item">
                <input type="checkbox" {{ $payment->mop == 'CASH' ? 'checked' : '' }}> cash
            </div>
            <div class="checkbox-item">
                <input type="checkbox" {{ $payment->mop == 'CHECK' ? 'checked' : '' }}> check
            </div>
            <div class="checkbox-item">
                <input type="checkbox" {{ $payment->mop == 'ONLINE' ? 'checked' : '' }}> online
            </div>
            <div class="checkbox-item">
                <input type="checkbox" {{ $payment->mop == 'CC' ? 'checked' : '' }}> cc
            </div>
            <div class="checkbox-item">
                <input type="checkbox" {{ $payment->details->percentage() < 100 ? 'checked' : '' }}> partial
            </div>
            <div class="checkbox-item">
                <input type="checkbox" {{ $payment->details->percentage() >= 100 ? 'checked' : '' }}> full
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>DATE OF PDC</th>
                    <th>CHECK NO.</th>
                    <th>REFERENCE NO.</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $payment->pdc_date ? date('Y-m-d', strtotime($payment->pdc_date)) : '-' }}
                    </td>
                    <td>{{ $payment->mop == 'CHECK' ? $payment->reference_no : '-' }}</td>
                    <td>{{ $payment->details->oa_number }}</td>
                    <td>{{ number_format($payment->amount, 2) }}</td>
                </tr>
                @for ($i = 0; $i < 15; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
                <tr>
                    <td colspan="3" style="text-align:right"><strong>TOTAL AMOUNT</strong></td>
                    <td><strong>{{ number_format($payment->amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="row">
            <div class="col-6">
                <p class="signature-line">Cashier/Authorized Representative</p>
            </div>
        </div>

        <div class="copy-section">
            <div class="copy-item">CLIENT'S COPY</div>
            <div class="copy-item">CASHIER'S COPY</div>
            <div class="copy-item">ACCOUNTING COPY</div>
        </div>

        <div class="text-center mt-2">
            <small>Receipt #:
                {{ $payment->receipt_number ?? 'PR-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</small>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            // Give a small delay to ensure everything loads properly
            setTimeout(function() {
                if (confirm('Print this receipt now?')) {
                    window.print();
                }
            }, 500);
        };
    </script>
</body>

</html>

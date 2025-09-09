<div>
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Payment Receipts for Order #{{ $oa->oa_number }}</h5>
            <div>
                <button wire:click="togglePrintMode" class="btn btn-light">
                    <i class="fa-solid fa-print"></i> {{ $printMode ? 'Exit Print Mode' : 'Enter Print Mode' }}
                </button>
                @if ($printMode)
                    <button wire:click="printAllReceipts" class="btn btn-warning">
                        <i class="fa-solid fa-print"></i> Print All Receipts
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body">
            <div class="mb-4">
                <h6>Order Details</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Client:</strong> {{ $oa->oa_client }}</p>
                        <p class="mb-1"><strong>Address:</strong> {{ $oa->oa_address }}</p>
                        <p class="mb-1"><strong>Contact:</strong> {{ $oa->oa_contact }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Order Date:</strong> {{ date('F d, Y', strtotime($oa->oa_date)) }}</p>
                        <p class="mb-1"><strong>Order Total:</strong>
                            ₱{{ number_format($oa->oa_price_override ?? $oa->items->sum('item_total') + $oa->oa_price_diff, 2) }}
                        </p>
                        <p class="mb-1"><strong>Payment Status:</strong>
                            <span class="badge {{ $oa->percentage() >= 100 ? 'bg-success' : 'bg-warning' }}">
                                {{ $oa->percentage() }}% Paid
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            @if ($printMode)
                <div class="print-container">
                    @foreach ($payments as $payment)
                        <div class="receipt-page mb-4" id="receipt-{{ $payment->id }}">
                            <div class="border p-3">
                                <div class="bg-secondary text-white p-2 text-center mb-3">
                                    <h4>PAYMENT RECEIPT</h4>
                                </div>

                                <div class="text-center mb-3">
                                    <h5>STRONGNORTH ENTERPRISES OPC</h5>
                                    <p class="mb-0">Unit 9 & 10 VYV Bldg., Brgy. 1, San Nicolas, Ilocos Norte</p>
                                    <p>VAT: 666-167-922-000</p>
                                    <h5 class="mt-2">PAYMENT RECEIPT</h5>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Sold to:</strong> {{ $oa->oa_client }}</p>
                                        <p class="mb-1"><strong>Address:</strong> {{ $oa->oa_address }}</p>
                                        <p class="mb-1"><strong>Buss. Style:</strong></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1"><strong>PR NO:</strong>
                                            PR-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
                                        <p class="mb-1"><strong>Date:</strong> {{ date('Y-m-d') }}</p>
                                        <p class="mb-1"><strong>TIN:</strong></p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between border p-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" disabled
                                                    {{ $payment->mop == 'CASH' ? 'checked' : '' }}>
                                                <label class="form-check-label">cash</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" disabled
                                                    {{ $payment->mop == 'CHECK' ? 'checked' : '' }}>
                                                <label class="form-check-label">check</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" disabled
                                                    {{ $payment->mop == 'ONLINE' ? 'checked' : '' }}>
                                                <label class="form-check-label">online</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" disabled
                                                    {{ $payment->mop == 'CC' ? 'checked' : '' }}>
                                                <label class="form-check-label">cc</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" disabled
                                                    {{ $oa->percentage() < 100 ? 'checked' : '' }}>
                                                <label class="form-check-label">partial</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" disabled
                                                    {{ $oa->percentage() >= 100 ? 'checked' : '' }}>
                                                <label class="form-check-label">full</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>DATE ISSUED</th>
                                            <th>DATE OF PDC</th>
                                            <th>CHECK NO.</th>
                                            <th>REFERENCE NO.</th>
                                            <th>AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ date('Y-m-d', strtotime($payment->date_of_payment)) }}</td>
                                            <td>{{ $payment->pdc_date ? date('Y-m-d', strtotime($payment->pdc_date)) : '-' }}
                                            </td>
                                            <td>{{ $payment->mop == 'CHECK' ? $payment->reference_no : '-' }}</td>
                                            <td>{{ $payment->reference_no ?? $oa->oa_number }}</td>
                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                        </tr>
                                        @for ($i = 0; $i < 8; $i++)
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                        @endfor
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>TOTAL AMOUNT</strong></td>
                                            <td><strong>{{ number_format($payment->amount, 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="row mt-4">
                                    <div class="col-6">
                                        <div style="border-top: 1px solid #000; padding-top: 5px;">
                                            <p>Cashier/Authorized Representative</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-4 text-center border">
                                        <p class="mb-0">CLIENT'S COPY</p>
                                    </div>
                                    <div class="col-4 text-center border">
                                        <p class="mb-0">CASHIER'S COPY</p>
                                    </div>
                                    <div class="col-4 text-center border">
                                        <p class="mb-0">ACCOUNTING COPY</p>
                                    </div>
                                </div>

                                <div class="mt-3 d-flex justify-content-center">
                                    <button wire:click="printReceipt({{ $payment->id }})"
                                        class="btn btn-sm btn-primary mx-1">
                                        <i class="fa-solid fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Mode of Payment</th>
                                <th>Reference No</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>PR-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ date('M d, Y', strtotime($payment->date_of_payment)) }}</td>
                                    <td>{{ $payment->mop }}</td>
                                    <td>{{ $payment->reference_no }}</td>
                                    <td>₱{{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $payment->status == 'Posted' ? 'bg-success' : ($payment->status == 'On-hold' ? 'bg-warning' : ($payment->status == 'Voided' ? 'bg-danger' : 'bg-secondary')) }}">
                                            {{ $payment->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <button wire:click="printReceipt({{ $payment->id }})"
                                            class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-print"></i> Print
                                        </button>
                                        @if ($payment->status != 'Voided')
                                            <button wire:click="voidPayment({{ $payment->id }})"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to void this payment receipt?')">
                                                <i class="fa-solid fa-ban"></i> Void
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No payment records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-3">
                <a href="{{ route('order.agreements.view', ['oa' => $oa]) }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back to Order
                </a>
                <a href="{{ route('order.agreements.batch-add-payments', ['oa_id' => $oa->oa_id]) }}"
                    class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Add More Payments
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.addEventListener('print-receipt', event => {
                const paymentId = event.detail.payment_id;
                const receiptElement = document.getElementById('receipt-' + paymentId);

                if (receiptElement) {
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                    <html>
                    <head>
                        <title>Payment Receipt PR-${paymentId.toString().padStart(6, '0')}</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
                        <style>
                            body { padding: 20px; }
                            @media print {
                                .no-print { display: none; }
                                body { padding: 0; }
                                .btn { display: none; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="no-print mb-3 text-center">
                            <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
                            <button onclick="window.close()" class="btn btn-secondary">Close</button>
                        </div>
                        ${receiptElement.innerHTML}
                    </body>
                    </html>
                `);
                    printWindow.document.close();
                    printWindow.focus();
                }
            });

            window.addEventListener('print-all-receipts', () => {
                window.print();
            });
        </script>
    @endpush
</div>

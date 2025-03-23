<div>
    <div class="card">
        <div class="text-white card-header bg-primary d-flex justify-content-between align-items-center">
            <h4 class="mb-0">All Payment Records</h4>
            <div>
                <button wire:click="export" class="btn btn-outline-light">
                    <i class="fa-solid fa-file-export"></i> Export
                </button>
                <button wire:click="printReport" class="btn btn-light">
                    <i class="fa-solid fa-print"></i> Print
                </button>
            </div>
        </div>

        <div class="card-body">
            <!-- Filter Form -->
            <div class="mb-4">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" wire:model.defer="dateFrom">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" wire:model.defer="dateTo">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" wire:model.defer="status">
                            <option value="">All Statuses</option>
                            @foreach ($statuses as $statusOption)
                                <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="payment_mode" class="form-label">Payment Mode</label>
                        <select class="form-select" id="payment_mode" wire:model.defer="paymentMode">
                            <option value="">All Payment Modes</option>
                            @foreach ($paymentModes as $mode)
                                <option value="{{ $mode }}">{{ $mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="delivery_id" class="form-label">Delivery</label>
                        <select class="form-select" id="delivery_id" wire:model.defer="deliveryId">
                            <option value="">All Deliveries</option>
                            @foreach ($deliveries as $delivery)
                                <option value="{{ $delivery->info_id }}">{{ $delivery->transno }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" wire:model.defer="search"
                            placeholder="Receipt #, OA, Client...">
                    </div>
                    <div class="mt-2 col-md-12 d-flex">
                        <button type="button" wire:click="applyFilters" class="btn btn-primary me-2">Filter</button>
                        <button type="button" wire:click="resetFilters" class="btn btn-secondary">Reset</button>
                    </div>
                </div>
            </div>

            <!-- Loading indicator -->
            <div wire:loading class="my-3 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Loading payments...</p>
            </div>

            <!-- Summary Cards -->
            <div class="mb-4 row" wire:loading.class="opacity-50">
                <div class="col-md-3">
                    <div class="text-white card bg-success">
                        <div class="py-2 card-body">
                            <h6 class="mb-0 card-title">Posted Payments</h6>
                            <h3 class="card-text">₱{{ number_format($totalPosted, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="py-2 card-body">
                            <h6 class="mb-0 card-title">Unposted Payments</h6>
                            <h3 class="card-text">₱{{ number_format($totalUnposted, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-white card bg-info">
                        <div class="py-2 card-body">
                            <h6 class="mb-0 card-title">On-hold Payments</h6>
                            <h3 class="card-text">₱{{ number_format($totalOnHold, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-white card bg-danger">
                        <div class="py-2 card-body">
                            <h6 class="mb-0 card-title">Voided Payments</h6>
                            <h3 class="card-text">₱{{ number_format($totalVoided, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments Table -->
            <div class="table-responsive" wire:loading.class="opacity-50">
                <table class="table table-striped table-bordered">
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>
                                    @if ($payment->batch_receipt_number)
                                        <a
                                            href="{{ route('receipt.show.batch', ['batch_number' => $payment->batch_receipt_number]) }}">
                                            {{ $payment->receipt_number }}
                                        </a>
                                        <br><small class="text-muted">Batch:
                                            {{ $payment->batch_receipt_number }}</small>
                                    @else
                                        <a href="{{ route('receipt.show', ['payment_id' => $payment->id]) }}">
                                            {{ $payment->receipt_number ?? 'PR-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                                        </a>
                                    @endif
                                </td>
                                <td>{{ date('M d, Y', strtotime($payment->date_of_payment)) }}</td>
                                <td>
                                    <a href="{{ route('order.agreements.view', ['oa' => $payment->details]) }}">
                                        {{ $payment->details->oa_number }}
                                    </a>
                                </td>
                                <td>{{ $payment->details->oa_client }}</td>
                                <td>
                                    @if ($payment->delivery)
                                        <a
                                            href="{{ route('order.delivery.view', ['transno' => $payment->delivery->transno]) }}">
                                            {{ $payment->delivery->transno }}
                                        </a>
                                        <br><small
                                            class="text-muted">{{ date('M d, Y', strtotime($payment->delivery->date)) }}</small>
                                    @else
                                        <span class="text-muted">No delivery</span>
                                    @endif
                                </td>
                                <td>{{ $payment->mop }}</td>
                                <td>{{ $payment->reference_no }}</td>
                                <td>₱{{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span
                                        class="badge {{ $payment->status == 'Posted'
                                            ? 'bg-success'
                                            : ($payment->status == 'Unposted'
                                                ? 'bg-warning'
                                                : ($payment->status == 'On-hold'
                                                    ? 'bg-info'
                                                    : ($payment->status == 'Voided'
                                                        ? 'bg-danger'
                                                        : 'bg-secondary'))) }}">
                                        {{ $payment->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if ($payment->batch_receipt_number)
                                            <a href="{{ route('receipt.print.batch', ['batch_number' => $payment->batch_receipt_number]) }}"
                                                class="btn btn-primary" target="_blank">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('receipt.print', ['payment_id' => $payment->id]) }}"
                                                class="btn btn-primary" target="_blank">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('receipt.batch', ['oa_id' => $payment->oa_id]) }}"
                                            class="btn btn-info">
                                            <i class="fa-solid fa-receipt"></i>
                                        </a>

                                        @if ($payment->status != 'Voided')
                                            <button type="button"
                                                wire:click="confirmVoidPayment({{ $payment->id }})"
                                                class="btn btn-danger">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No payment records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $payments->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Handle CSV download event
            window.addEventListener('download-csv', event => {
                const {
                    content,
                    fileName
                } = event.detail;

                // Create blob and download link
                const blob = new Blob([content], {
                    type: 'text/csv'
                });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();

                // Cleanup
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            });
        </script>
    @endpush
</div>

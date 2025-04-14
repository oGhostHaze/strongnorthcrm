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
                                        <span class="text-muted">Advance Payment</span>
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
                                                class="btn btn-primary" target="_blank" data-bs-toggle="tooltip"
                                                data-bs-title="Print Batch Receipt">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('receipt.print', ['payment_id' => $payment->id]) }}"
                                                class="btn btn-primary" target="_blank" data-bs-toggle="tooltip"
                                                data-bs-title="Print Receipt">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('receipt.batch', ['oa_id' => $payment->oa_id]) }}"
                                            class="btn btn-info" data-bs-toggle="tooltip"
                                            data-bs-title="View Batch Receipt">
                                            <i class="fa-solid fa-receipt"></i>
                                        </a>

                                        <button type="button" wire:click="editPayment({{ $payment->id }})"
                                            class="btn btn-warning" data-bs-toggle="tooltip"
                                            data-bs-title="Edit Payment">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>

                                        @if ($payment->status != 'Voided')
                                            <button type="button"
                                                wire:click="confirmVoidPayment({{ $payment->id }})"
                                                class="btn btn-danger" data-bs-toggle="tooltip"
                                                data-bs-title="Void Payment">
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

        <!-- Update Payment Modal -->
        <div class="modal fade" id="updatePaymentModal" tabindex="-1" aria-labelledby="updatePaymentModalLabel"
            aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updatePaymentModalLabel">Update Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="edit_status" class="form-label">Status</label>
                                <select class="form-select" id="edit_status" wire:model.defer="editStatus">
                                    <option value="Unposted">Unposted</option>
                                    <option value="Posted">Posted</option>
                                    <option value="Commissioned">Commissioned</option>
                                    <option value="On Hold">On Hold</option>
                                    <option value="Voided">Voided</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="edit_delivery_id" class="form-label">Link to Delivery</label>
                                <select class="form-select" id="edit_delivery_id" wire:model.defer="editDeliveryId">
                                    <option value="">-- Advance Payment --</option>
                                    @foreach ($relatedDeliveries as $delivery)
                                        <option value="{{ $delivery->info_id }}">
                                            {{ $delivery->transno }}
                                            ({{ date('M d, Y', strtotime($delivery->date)) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="edit_mop" class="form-label">Payment Mode</label>
                                <select class="form-select" id="edit_mop" wire:model.defer="editMop">
                                    @foreach ($paymentModes as $mode)
                                        <option value="{{ $mode }}">{{ $mode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="edit_amount" class="form-label">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" id="edit_amount" step="0.01"
                                        wire:model.defer="editAmount">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="edit_date_of_payment" class="form-label">Payment Date</label>
                                <input type="date" class="form-control" id="edit_date_of_payment"
                                    wire:model.defer="editDateOfPayment">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="edit_reference_no" class="form-label">Reference Number</label>
                                <input type="text" class="form-control" id="edit_reference_no"
                                    wire:model.defer="editReferenceNo">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="edit_remarks" rows="3" wire:model.defer="editRemarks"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="updatePayment">Save
                            Changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Update Payment Modal -->
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

            // Show and hide update payment modal
            document.addEventListener('livewire:load', function() {
                // Create modal instance once and store it
                const updatePaymentModal = new bootstrap.Modal(document.getElementById('updatePaymentModal'));

                // Show modal event
                window.addEventListener('show-update-payment-modal', event => {
                    updatePaymentModal.show();
                });

                // Hide modal event
                window.addEventListener('hide-update-payment-modal', event => {
                    updatePaymentModal.hide();
                });

                // Handle modal hidden event to ensure clean state
                document.getElementById('updatePaymentModal').addEventListener('hidden.bs.modal', function() {
                    Livewire.emit('resetEditFields');
                });

                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
            });
        </script>
    @endpush
</div>

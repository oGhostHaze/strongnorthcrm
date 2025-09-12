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
                        <label for="delivery_filter" class="form-label">Delivery</label>
                        <select class="form-select" id="delivery_filter" wire:model.defer="deliveryFilter">
                            <option value="all">All Payments</option>
                            <option value="with_delivery">With Delivery</option>
                            <option value="without_delivery">Without Delivery (Advance Payments)</option>
                            <option value="specific">Specific Delivery</option>
                        </select>
                    </div>
                    <div class="col-md-2" @if ($deliveryFilter != 'specific') style="display: none;" @endif>
                        <label for="delivery_id" class="form-label">Select Delivery</label>
                        <select class="form-select" id="delivery_id" wire:model.defer="deliveryId">
                            <option value="">-- Select Delivery --</option>
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
                            <h6 class="mb-0 card-title">Total Unpaid</h6>
                            <h3 class="card-text">₱{{ number_format($totalUnpaidBalance, 2) }}</h3>
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
                            <th>Order #</th>
                            <th>Delivery</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Lifechanger</th>
                            <th>Presenter</th>
                            <th>Teambuilder</th>
                            <th>Payment Mode</th>
                            <th>Reference #</th>
                            <th>PDC Date</th>
                            <th>Amount</th>
                            <th>Remaining Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>
                                    <a class="text-nowrap"
                                        href="{{ route('receipt.show.batch', ['batch_number' => $payment->batch_receipt_number]) }}">
                                        {{ $payment->getFormattedReceiptNumber() }}
                                    </a>
                                </td>
                                <td>
                                    <a class="text-nowrap"
                                        href="{{ route('order.agreements.view', ['oa' => $payment->details]) }}">
                                        {{ $payment->details->oa_number }}
                                    </a>
                                </td>
                                <td>
                                    @if ($payment->delivery)
                                        <a class="text-nowrap"
                                            href="{{ route('order.delivery.view', ['transno' => $payment->delivery->transno]) }}">
                                            {{ $payment->delivery->transno }}
                                        </a>
                                        <br><small
                                            class="text-muted">{{ date('M d, Y', strtotime($payment->delivery->date)) }}</small>
                                    @else
                                        <span class="text-muted">Advance Payment</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">{{ date('M d, Y', strtotime($payment->date_of_payment)) }}
                                </td>
                                <td class="text-nowrap">{{ $payment->details->oa_client }}</td>
                                <td class="text-nowrap">{{ $payment->details->oa_consultant }}</td>
                                <td class="text-nowrap">{{ $payment->details->oa_presenter }}</td>
                                <td class="text-nowrap">{{ $payment->details->oa_teambuilder }}</td>
                                <td class="text-nowrap">{{ $payment->mop }}</td>
                                <td class="text-nowrap">
                                    <div class="text-nowrap">
                                        {{ $payment->reference_no }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-nowrap">
                                        {{ $payment->pdc_date }}
                                    </div>
                                </td>
                                <td>₱{{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    @php
                                        $subtotal = $payment->details->oa_price_override
                                            ? $payment->details->oa_price_override
                                            : $payment->details->items()->sum('item_total');
                                        $total = (float) $subtotal + (float) $payment->details->oa_price_diff;
                                        $totalPaid = $payment->details
                                            ->payments()
                                            ->where('status', '!=', 'Voided')
                                            ->sum('amount');
                                        $remainingBalance = $total - $totalPaid;
                                    @endphp
                                    <span class="{{ $remainingBalance <= 0 ? 'text-success' : 'text-danger' }}">
                                        ₱{{ number_format($remainingBalance, 2) }}
                                    </span>
                                    @if ($remainingBalance <= 0)
                                        <span class="badge bg-success">Fully Paid</span>
                                    @endif
                                </td>
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
                                            <button type="button" wire:click="initiateTransfer({{ $payment->id }})"
                                                class="btn btn-secondary" data-bs-toggle="tooltip"
                                                data-bs-title="Transfer to Another Order">
                                                <i class="fa-solid fa-exchange-alt"></i>
                                            </button>

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
                                <td colspan="11" class="text-center">No payment records found</td>
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

        <!-- Transfer Payment Modal -->
        <div class="modal fade" id="transferPaymentModal" tabindex="-1" aria-labelledby="transferPaymentModalLabel"
            aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="transferPaymentModalLabel">Transfer Payment to Another Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($selectedPaymentForTransfer)
                            <!-- Payment Information -->
                            <div class="mb-4 alert alert-info">
                                <h6 class="mb-1"><strong>Payment to Transfer:</strong></h6>
                                <p class="mb-1">Amount:
                                    <strong>₱{{ number_format($selectedPaymentForTransfer->amount, 2) }}</strong>
                                </p>
                                <p class="mb-1">From Order:
                                    <strong>{{ $selectedPaymentForTransfer->details->oa_number }}</strong>
                                </p>
                                <p class="mb-0">Client:
                                    <strong>{{ $selectedPaymentForTransfer->details->oa_client }}</strong>
                                </p>
                            </div>

                            <!-- Search for Target Order -->
                            <div class="mb-3">
                                <label for="transfer_order_search" class="form-label">Search for Target Order</label>
                                <input type="text" class="form-control" id="transfer_order_search"
                                    wire:model.debounce.300ms="transferOrderSearch"
                                    placeholder="Search by Order Number, Client Name, Consultant, or Presenter...">
                                <div class="form-text">Type at least 2 characters to search</div>
                            </div>

                            <!-- Search Results -->
                            @if (count($transferSearchResults) > 0)
                                <div class="mb-3">
                                    <label class="form-label">Select Target Order:</label>
                                    <div class="list-group" style="max-height: 200px; overflow-y: auto;">
                                        @foreach ($transferSearchResults as $order)
                                            <button type="button" class="list-group-item list-group-item-action"
                                                wire:click="selectOrderForTransfer({{ $order->oa_id }}, '{{ $order->oa_number }}')">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">{{ $order->oa_number }}</h6>
                                                    <small>{{ date('M d, Y', strtotime($order->oa_date)) }}</small>
                                                </div>
                                                <p class="mb-1">{{ $order->oa_client }}</p>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif(strlen($transferOrderSearch) >= 2)
                                <div class="mb-3 alert alert-warning">
                                    No orders found matching "{{ $transferOrderSearch }}"
                                </div>
                            @endif

                            <!-- Selected Target Order -->
                            @if ($transferToOrderId)
                                <div class="mb-3 alert alert-success">
                                    <h6 class="mb-1"><strong>Selected Target Order:</strong></h6>
                                    <p class="mb-0">{{ $transferToOrderNumber }}</p>
                                </div>
                            @endif

                            <!-- Transfer Reason -->
                            <div class="mb-3">
                                <label for="transfer_remarks" class="form-label">Transfer Reason (Optional)</label>
                                <textarea class="form-control" id="transfer_remarks" rows="3" wire:model.defer="transferRemarks"
                                    placeholder="Enter reason for transferring this payment..."></textarea>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="confirmTransferPayment"
                            @if (!$transferToOrderId) disabled @endif>
                            Transfer Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Transfer Payment Modal -->
    </div>

    @push('scripts')
        <script>
            // Success and error event listeners (from your app.blade.php)
            window.addEventListener('success', event => {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: event.detail,
                    showConfirmButton: false,
                    timer: 2000
                });
            });

            window.addEventListener('error', event => {
                Swal.fire({
                    icon: 'error',
                    title: 'Something went wrong!',
                    text: event.detail,
                    showConfirmButton: false,
                });
            });

            // Select2 focus handler (from your app.blade.php)
            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });

            // Handle CSV download event
            window.addEventListener('download-csv', event => {
                const {
                    content,
                    fileName
                } = event.detail;
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
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            });

            // Handle delivery filter visibility
            function toggleDeliveryIdField() {
                const deliveryFilter = document.getElementById('delivery_filter');
                const deliveryIdContainer = document.getElementById('delivery_id')?.closest('.col-md-2');

                if (deliveryFilter && deliveryIdContainer) {
                    deliveryIdContainer.style.display = deliveryFilter.value === 'specific' ? 'block' : 'none';
                }
            }

            // Initialize tooltips function
            function initializeTooltips() {
                // Dispose existing tooltips more safely
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(element => {
                    try {
                        const existingTooltip = bootstrap.Tooltip.getInstance(element);
                        if (existingTooltip) {
                            existingTooltip.dispose();
                        }
                    } catch (error) {
                        // Silently ignore disposal errors for elements that no longer exist
                        console.debug('Tooltip disposal error (safe to ignore):', error);
                    }
                });

                // Initialize new tooltips
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(element => {
                    try {
                        new bootstrap.Tooltip(element);
                    } catch (error) {
                        console.debug('Tooltip initialization error:', error);
                    }
                });
            }

            // Safely dispose all tooltips
            function disposeAllTooltips() {
                // Get all tooltip instances and dispose them safely
                const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltipElements.forEach(element => {
                    try {
                        const tooltip = bootstrap.Tooltip.getInstance(element);
                        if (tooltip) {
                            tooltip.dispose();
                        }
                    } catch (error) {
                        // Element might have been removed, ignore the error
                    }
                });

                // Also check for any orphaned tooltip elements
                document.querySelectorAll('.tooltip').forEach(tooltipElement => {
                    try {
                        if (tooltipElement.parentNode) {
                            tooltipElement.parentNode.removeChild(tooltipElement);
                        }
                    } catch (error) {
                        // Ignore cleanup errors
                    }
                });
            }

            // DOMContentLoaded event
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize delivery filter
                toggleDeliveryIdField();

                // Add event listener for delivery filter change
                const deliveryFilter = document.getElementById('delivery_filter');
                if (deliveryFilter) {
                    deliveryFilter.addEventListener('change', toggleDeliveryIdField);
                }

                // Initialize tooltips on load
                initializeTooltips();
            });

            // Livewire event handlers
            document.addEventListener('livewire:load', function() {
                // Initialize modal instances
                const updatePaymentModal = new bootstrap.Modal(document.getElementById('updatePaymentModal'));
                const transferPaymentModal = new bootstrap.Modal(document.getElementById('transferPaymentModal'));

                // Modal event handlers
                window.addEventListener('show-update-payment-modal', () => updatePaymentModal.show());
                window.addEventListener('hide-update-payment-modal', () => updatePaymentModal.hide());
                window.addEventListener('show-transfer-payment-modal', () => transferPaymentModal.show());
                window.addEventListener('hide-transfer-payment-modal', () => transferPaymentModal.hide());

                // Modal cleanup on hide
                document.getElementById('updatePaymentModal').addEventListener('hidden.bs.modal', function() {
                    @this.call('resetEditFields');
                });

                document.getElementById('transferPaymentModal').addEventListener('hidden.bs.modal', function() {
                    @this.call('resetTransferFields');
                });

                // Livewire event hooks
                Livewire.hook('element.updating', (el, component) => {
                    // Dispose tooltips before DOM update
                    disposeAllTooltips();
                });

                Livewire.hook('element.updated', (el, component) => {
                    // Reinitialize tooltips after DOM update
                    setTimeout(() => {
                        initializeTooltips();
                        toggleDeliveryIdField();
                    }, 50);
                });

                // Fallback for message.processed (in case element hooks don't work)
                Livewire.hook('message.processed', (message, component) => {
                    setTimeout(() => {
                        // Clean up any orphaned tooltips first
                        disposeAllTooltips();
                        // Then reinitialize
                        initializeTooltips();
                        toggleDeliveryIdField();
                    }, 100);
                });
            });
        </script>
    @endpush
</div>

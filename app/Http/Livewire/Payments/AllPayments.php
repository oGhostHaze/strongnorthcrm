<?php

namespace App\Http\Livewire\Payments;

use Livewire\Component;
use App\Models\OrderPaymentHistory;
use App\Models\Delivery;
use App\Models\Order;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AllPayments extends Component
{
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';

    // Filter properties
    public $dateFrom;
    public $dateTo;
    public $status = '';
    public $paymentMode = '';
    public $search = '';
    public $deliveryId = '';  // For specific delivery
    public $deliveryFilter = 'all'; // all, with_delivery, without_delivery, specific

    // Payment edit properties
    public $editPaymentId = null;
    public $editStatus = '';
    public $editDeliveryId = null;
    public $editMop = '';
    public $editAmount = 0;
    public $editDateOfPayment = '';
    public $editRemarks = '';
    public $editReferenceNo = '';

    // Transfer payment properties
    public $transferPaymentId = null;
    public $transferToOrderId = null;
    public $transferToOrderNumber = '';
    public $transferSearchResults = [];
    public $transferOrderSearch = '';
    public $transferRemarks = '';
    public $selectedPaymentForTransfer = null;

    // Batch transfer properties
    public $batchTransferFromOrderId = null;
    public $batchTransferToOrderId = null;
    public $batchTransferToOrderNumber = '';
    public $batchTransferSearchResults = [];
    public $batchTransferOrderSearch = '';
    public $batchTransferRemarks = '';
    public $selectedOrderForBatchTransfer = null;
    public $batchTransferPayments = [];

    // Related deliveries for edit modal
    public $relatedDeliveries = [];

    // Totals - computed on demand
    public $totalPosted;
    public $totalUnposted;
    public $totalOnHold;
    public $totalVoided;
    public $totalUnpaidBalance;

    // Listeners for events
    protected $listeners = [
        'refreshData',
        'confirmVoid',
        'resetEditFields',
        'confirmTransfer',
        'resetTransferFields',
        'confirmBatchTransfer',
        'resetBatchTransferFields'
    ];

    public function mount()
    {
        // Default to last 30 days
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');

        // Calculate totals on initial load
        $this->calculateTotals();
    }

    public function render()
    {
        $paymentModes = $this->getPaymentModes();
        $statuses = $this->getStatuses();
        $payments = $this->getPayments();
        $deliveries = $this->getDeliveries();

        return view('livewire.payments.all-payments', [
            'payments' => $payments,
            'paymentModes' => $paymentModes,
            'statuses' => $statuses,
            'deliveries' => $deliveries,
        ]);
    }

    /**
     * Get payment modes for dropdown
     */
    private function getPaymentModes()
    {
        return OrderPaymentHistory::select('mop')
            ->distinct()
            ->pluck('mop')
            ->toArray();
    }

    /**
     * Get payment statuses for dropdown
     */
    private function getStatuses()
    {
        return OrderPaymentHistory::select('status')
            ->distinct()
            ->pluck('status')
            ->toArray();
    }

    /**
     * Get all deliveries for dropdown
     */
    private function getDeliveries()
    {
        return Delivery::select('info_id', 'transno', 'date')
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Get payments with applied filters
     */
    private function getPayments()
    {
        $query = OrderPaymentHistory::with(['details', 'delivery'])
            ->whereBetween('date_of_payment', [
                $this->dateFrom,
                Carbon::parse($this->dateTo)->endOfDay()->toDateTimeString()
            ]);

        // Apply status filter
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Apply payment mode filter
        if (!empty($this->paymentMode)) {
            $query->where('mop', $this->paymentMode);
        }

        // Apply delivery filter
        switch ($this->deliveryFilter) {
            case 'with_delivery':
                $query->whereNotNull('delivery_id');
                break;
            case 'without_delivery':
                $query->whereNull('delivery_id');
                break;
            case 'specific':
                if (!empty($this->deliveryId)) {
                    $query->where('delivery_id', $this->deliveryId);
                }
                break;
            default:
                // 'all' - no filter needed
                break;
        }

        // Apply search filter
        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                // Search in payment fields
                $q->where('receipt_number', 'LIKE', "%{$search}%")
                    ->orWhere('batch_receipt_number', 'LIKE', "%{$search}%")
                    ->orWhere('reference_no', 'LIKE', "%{$search}%")
                    // Search in related order fields
                    ->orWhereHas('details', function ($orderQuery) use ($search) {
                        $orderQuery->where('oa_number', 'LIKE', "%{$search}%")
                            ->orWhere('oa_client', 'LIKE', "%{$search}%")
                            ->orWhere('oa_consultant', 'LIKE', "%{$search}%")
                            ->orWhere('oa_presenter', 'LIKE', "%{$search}%");
                    })
                    // Search in related delivery fields
                    ->orWhereHas('delivery', function ($deliveryQuery) use ($search) {
                        $deliveryQuery->where('transno', 'LIKE', "%{$search}%")
                            ->orWhere('client', 'LIKE', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('date_of_payment', 'desc')
            ->paginate(15);
    }

    /**
     * Calculate summary totals
     */
    public function calculateTotals()
    {
        $dateRange = [
            $this->dateFrom,
            Carbon::parse($this->dateTo)->endOfDay()->toDateTimeString()
        ];

        $query = OrderPaymentHistory::whereBetween('date_of_payment', $dateRange);

        // Apply delivery filter to totals as well
        switch ($this->deliveryFilter) {
            case 'with_delivery':
                $query->whereNotNull('delivery_id');
                break;
            case 'without_delivery':
                $query->whereNull('delivery_id');
                break;
            case 'specific':
                if (!empty($this->deliveryId)) {
                    $query->where('delivery_id', $this->deliveryId);
                }
                break;
            default:
                // 'all' - no filter needed
                break;
        }

        $this->totalPosted = (clone $query)->where('status', 'Posted')->sum('amount');
        $this->totalUnposted = (clone $query)->where('status', 'Unposted')->sum('amount');
        $this->totalOnHold = (clone $query)->where('status', 'On-hold')->sum('amount');
        $this->totalVoided = (clone $query)->where('status', 'Voided')->sum('amount');

        // Calculate the total unpaid balance for all orders in the current filter
        $orderIds = (clone $query)->distinct()->pluck('oa_id')->toArray();

        if (!empty($orderIds)) {
            // Get all order totals (subtotal + price_diff)
            $orders = Order::whereIn('oa_id', $orderIds)->get();

            $totalOrderAmount = 0;
            $totalPaidAmount = 0;

            foreach ($orders as $order) {
                $subtotal = $order->oa_price_override ? $order->oa_price_override : $order->items()->sum('item_total');
                $total = (float) $subtotal + (float) $order->oa_price_diff;
                $totalOrderAmount += $total;

                $totalPaidAmount += $order->payments()->where('status', '!=', 'Voided')->sum('amount');
            }

            $this->totalUnpaidBalance = $totalOrderAmount - $totalPaidAmount;
        } else {
            $this->totalUnpaidBalance = 0;
        }
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->status = '';
        $this->paymentMode = '';
        $this->search = '';
        $this->deliveryId = '';
        $this->deliveryFilter = 'all';
        $this->resetPage();
        $this->calculateTotals();
    }

    /**
     * Apply filters and recalculate totals
     */
    public function applyFilters()
    {
        $this->resetPage();
        $this->calculateTotals();
    }

    /**
     * Show void confirmation dialog
     */
    public function confirmVoidPayment($paymentId)
    {
        $this->alert('warning', 'Are you sure you want to void this payment?', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmVoid',
            'showCancelButton' => true,
            'cancelButtonText' => 'Cancel',
            'data' => [
                'payment_id' => $paymentId,
            ],
        ]);
    }

    /**
     * Void a payment
     */
    public function confirmVoid($data)
    {
        $paymentId = $data['data']['payment_id'];
        $payment = OrderPaymentHistory::find($paymentId);

        if (!$payment) {
            $this->alert('error', 'Payment not found.');
            return;
        }

        if ($payment->status === 'Voided') {
            $this->alert('error', 'This payment has already been voided.');
            return;
        }

        // Void the payment
        $payment->status = 'Voided';
        $payment->save();

        // Show success message
        $this->alert('success', 'Payment has been successfully voided.');

        // Refresh the data
        $this->calculateTotals();
    }

    /**
     * Load payment data for editing
     */
    public function editPayment($paymentId)
    {
        $payment = OrderPaymentHistory::find($paymentId);

        if (!$payment) {
            $this->alert('error', 'Payment not found.');
            return;
        }

        $this->editPaymentId = $payment->id;
        $this->editStatus = $payment->status;
        $this->editDeliveryId = $payment->delivery_id;
        $this->editMop = $payment->mop;
        $this->editAmount = $payment->amount;
        $this->editDateOfPayment = $payment->date_of_payment;
        $this->editRemarks = $payment->remarks;
        $this->editReferenceNo = $payment->reference_no;

        // Get only deliveries related to this payment's order agreement
        $this->relatedDeliveries = Delivery::where('oa_no', $payment->oa_id)
            ->select('info_id', 'transno', 'date')
            ->orderBy('date', 'desc')
            ->get();

        $this->dispatchBrowserEvent('show-update-payment-modal');
    }

    /**
     * Update payment with new data
     */
    public function updatePayment()
    {
        $this->validate([
            'editStatus' => 'required',
            'editMop' => 'required',
            'editAmount' => 'required|numeric|min:0',
            'editDateOfPayment' => 'required|date',
        ]);

        $payment = OrderPaymentHistory::find($this->editPaymentId);

        if (!$payment) {
            $this->alert('error', 'Payment not found.');
            return;
        }

        $payment->status = $this->editStatus;
        $payment->delivery_id = $this->editDeliveryId ?: null; // Convert empty string to null
        $payment->mop = $this->editMop;
        $payment->amount = $this->editAmount;
        $payment->date_of_payment = $this->editDateOfPayment;
        $payment->remarks = $this->editRemarks;
        $payment->reference_no = $this->editReferenceNo;

        $payment->save();

        // First dispatch the browser event to hide the modal
        $this->dispatchBrowserEvent('hide-update-payment-modal');

        // Then reset the fields
        $this->resetEditFields();

        // Show success message and recalculate totals
        $this->alert('success', 'Payment updated successfully.');
        $this->calculateTotals();
    }

    /**
     * Reset edit fields (public method for event listener)
     */
    public function resetEditFields()
    {
        $this->editPaymentId = null;
        $this->editStatus = '';
        $this->editDeliveryId = null;
        $this->editMop = '';
        $this->editAmount = 0;
        $this->editDateOfPayment = '';
        $this->editRemarks = '';
        $this->editReferenceNo = '';
        $this->relatedDeliveries = [];
    }

    /**
     * Initiate payment transfer
     */
    public function initiateTransfer($paymentId)
    {
        $payment = OrderPaymentHistory::with('details')->find($paymentId);

        if (!$payment) {
            $this->alert('error', 'Payment not found.');
            return;
        }

        if ($payment->status === 'Voided') {
            $this->alert('error', 'Cannot transfer voided payments.');
            return;
        }

        $this->transferPaymentId = $payment->id;
        $this->selectedPaymentForTransfer = $payment;
        $this->transferOrderSearch = '';
        $this->transferSearchResults = [];
        $this->transferToOrderId = null;
        $this->transferToOrderNumber = '';
        $this->transferRemarks = '';

        $this->dispatchBrowserEvent('show-transfer-payment-modal');
    }

    /**
     * Search for orders to transfer payment to
     */
    public function updatedTransferOrderSearch()
    {
        if (strlen($this->transferOrderSearch) < 2) {
            $this->transferSearchResults = [];
            return;
        }

        $search = $this->transferOrderSearch;

        $this->transferSearchResults = Order::where(function ($query) use ($search) {
            $query->where('oa_number', 'LIKE', "%{$search}%")
                ->orWhere('oa_client', 'LIKE', "%{$search}%")
                ->orWhere('oa_consultant', 'LIKE', "%{$search}%")
                ->orWhere('oa_presenter', 'LIKE', "%{$search}%");
        })
            ->where('oa_id', '!=', $this->selectedPaymentForTransfer->oa_id) // Exclude current order
            ->select('oa_id', 'oa_number', 'oa_client', 'oa_date')
            ->orderBy('oa_number', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Select an order for transfer
     */
    public function selectOrderForTransfer($orderId, $orderNumber)
    {
        $this->transferToOrderId = $orderId;
        $this->transferToOrderNumber = $orderNumber;
        $this->transferSearchResults = [];
        $this->transferOrderSearch = $orderNumber;
        $this->updatedTransferOrderSearch();
    }

    /**
     * Show transfer confirmation
     */
    public function confirmTransferPayment()
    {
        $this->validate([
            'transferToOrderId' => 'required|exists:orders,oa_id',
            'transferRemarks' => 'nullable|string|max:500',
        ]);

        $fromOrder = $this->selectedPaymentForTransfer->details;
        $toOrder = Order::find($this->transferToOrderId);

        $message = "Transfer payment of ₱" . number_format($this->selectedPaymentForTransfer->amount, 2) .
            " from Order #{$fromOrder->oa_number} ({$fromOrder->oa_client}) " .
            "to Order #{$toOrder->oa_number} ({$toOrder->oa_client})?";

        $this->alert('warning', $message, [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'Transfer Payment',
            'onConfirmed' => 'confirmTransfer',
            'showCancelButton' => true,
            'cancelButtonText' => 'Cancel',
            'data' => [
                'payment_id' => $this->transferPaymentId,
                'to_order_id' => $this->transferToOrderId,
                'remarks' => $this->transferRemarks,
            ],
        ]);
    }

    /**
     * Execute the payment transfer
     */
    public function confirmTransfer($data)
    {
        try {
            DB::beginTransaction();

            $paymentId = $data['data']['payment_id'];
            $toOrderId = $data['data']['to_order_id'];
            $remarks = $data['data']['remarks'] ?? '';

            $payment = OrderPaymentHistory::find($paymentId);
            $fromOrder = Order::find($payment->oa_id);
            $toOrder = Order::find($toOrderId);

            if (!$payment || !$fromOrder || !$toOrder) {
                throw new \Exception('Payment or order not found.');
            }

            // Store original values for audit trail
            $originalOrderId = $payment->oa_id;
            $originalOrderNumber = $fromOrder->oa_number;

            // Update payment record
            $payment->oa_id = $toOrderId;
            $payment->delivery_id = null; // Clear delivery association since it's a different order

            // Add transfer information to remarks
            $transferNote = "Transferred from Order #{$originalOrderNumber} on " . now()->format('Y-m-d H:i:s');
            if (!empty($remarks)) {
                $transferNote .= ". Reason: {$remarks}";
            }

            $payment->remarks = $payment->remarks ? $payment->remarks . "; " . $transferNote : $transferNote;
            $payment->save();

            // Create audit log entry (you may want to create a separate audit table)
            \Log::info('Payment Transfer', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'from_order_id' => $originalOrderId,
                'from_order_number' => $originalOrderNumber,
                'to_order_id' => $toOrderId,
                'to_order_number' => $toOrder->oa_number,
                'transfer_reason' => $remarks,
                'transferred_by' => auth()->user()->id ?? null,
                'transferred_at' => now(),
            ]);

            DB::commit();

            // Hide modal and reset fields
            $this->dispatchBrowserEvent('hide-transfer-payment-modal');
            $this->resetTransferFields();

            // Show success message
            $this->alert('success', "Payment successfully transferred from Order #{$originalOrderNumber} to Order #{$toOrder->oa_number}.");

            // Refresh data
            $this->calculateTotals();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', 'Transfer failed: ' . $e->getMessage());
        }
    }

    /**
     * Reset transfer fields
     */
    public function resetTransferFields()
    {
        $this->transferPaymentId = null;
        $this->transferToOrderId = null;
        $this->transferToOrderNumber = '';
        $this->transferSearchResults = [];
        $this->transferOrderSearch = '';
        $this->transferRemarks = '';
        $this->selectedPaymentForTransfer = null;
    }

    /**
     * Initiate batch transfer for all payments from an order
     */
    public function initiateBatchTransfer($fromOrderId)
    {
        $fromOrder = Order::with(['payments' => function ($query) {
            $query->where('status', '!=', 'Voided');
        }])->find($fromOrderId);

        if (!$fromOrder) {
            $this->alert('error', 'Order not found.');
            return;
        }

        if ($fromOrder->payments->isEmpty()) {
            $this->alert('error', 'No active payments found for this order.');
            return;
        }

        $this->batchTransferFromOrderId = $fromOrderId;
        $this->selectedOrderForBatchTransfer = $fromOrder;
        $this->batchTransferPayments = $fromOrder->payments;
        $this->batchTransferOrderSearch = '';
        $this->batchTransferSearchResults = [];
        $this->batchTransferToOrderId = null;
        $this->batchTransferToOrderNumber = '';
        $this->batchTransferRemarks = '';

        $this->dispatchBrowserEvent('show-batch-transfer-modal');
    }

    /**
     * Search for orders for batch transfer
     */
    public function searchOrdersForBatchTransfer()
    {
        if (strlen($this->batchTransferOrderSearch) < 2) {
            $this->batchTransferSearchResults = [];
            return;
        }

        $search = $this->batchTransferOrderSearch;

        $this->batchTransferSearchResults = Order::where(function ($query) use ($search) {
            $query->where('oa_number', 'LIKE', "%{$search}%")
                ->orWhere('oa_client', 'LIKE', "%{$search}%")
                ->orWhere('oa_consultant', 'LIKE', "%{$search}%")
                ->orWhere('oa_presenter', 'LIKE', "%{$search}%");
        })
            ->where('oa_id', '!=', $this->batchTransferFromOrderId) // Exclude source order
            ->select('oa_id', 'oa_number', 'oa_client', 'oa_date')
            ->orderBy('oa_number', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Select an order for batch transfer
     */
    public function selectOrderForBatchTransfer($orderId, $orderNumber)
    {
        $this->batchTransferToOrderId = $orderId;
        $this->batchTransferToOrderNumber = $orderNumber;
        $this->batchTransferSearchResults = [];
        $this->batchTransferOrderSearch = $orderNumber;
    }

    /**
     * Show batch transfer confirmation
     */
    public function confirmBatchTransferPayments()
    {
        $this->validate([
            'batchTransferToOrderId' => 'required|exists:orders,oa_id',
            'batchTransferRemarks' => 'nullable|string|max:500',
        ]);

        $fromOrder = $this->selectedOrderForBatchTransfer;
        $toOrder = Order::find($this->batchTransferToOrderId);
        $totalAmount = $this->batchTransferPayments->sum('amount');
        $paymentCount = $this->batchTransferPayments->count();

        $message = "Transfer {$paymentCount} payments totaling ₱" . number_format($totalAmount, 2) .
            " from Order #{$fromOrder->oa_number} ({$fromOrder->oa_client}) " .
            "to Order #{$toOrder->oa_number} ({$toOrder->oa_client})?";

        $this->alert('warning', $message, [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'Transfer All Payments',
            'onConfirmed' => 'confirmBatchTransfer',
            'showCancelButton' => true,
            'cancelButtonText' => 'Cancel',
            'data' => [
                'from_order_id' => $this->batchTransferFromOrderId,
                'to_order_id' => $this->batchTransferToOrderId,
                'remarks' => $this->batchTransferRemarks,
            ],
        ]);
    }

    /**
     * Execute the batch payment transfer
     */
    public function confirmBatchTransfer($data)
    {
        try {
            DB::beginTransaction();

            $fromOrderId = $data['data']['from_order_id'];
            $toOrderId = $data['data']['to_order_id'];
            $remarks = $data['data']['remarks'] ?? '';

            $fromOrder = Order::find($fromOrderId);
            $toOrder = Order::find($toOrderId);
            $payments = OrderPaymentHistory::where('oa_id', $fromOrderId)
                ->where('status', '!=', 'Voided')
                ->get();

            if (!$fromOrder || !$toOrder || $payments->isEmpty()) {
                throw new \Exception('Invalid order or no payments found.');
            }

            $transferredCount = 0;
            $totalAmount = 0;

            foreach ($payments as $payment) {
                // Store original values for audit trail
                $originalOrderNumber = $fromOrder->oa_number;

                // Update payment record
                $payment->oa_id = $toOrderId;
                $payment->delivery_id = null; // Clear delivery association since it's a different order

                // Add transfer information to remarks
                $transferNote = "Batch transferred from Order #{$originalOrderNumber} on " . now()->format('Y-m-d H:i:s');
                if (!empty($remarks)) {
                    $transferNote .= ". Reason: {$remarks}";
                }

                $payment->remarks = $payment->remarks ? $payment->remarks . "; " . $transferNote : $transferNote;
                $payment->save();

                $transferredCount++;
                $totalAmount += $payment->amount;
            }

            // Create audit log entry
            \Log::info('Batch Payment Transfer', [
                'transferred_count' => $transferredCount,
                'total_amount' => $totalAmount,
                'from_order_id' => $fromOrderId,
                'from_order_number' => $fromOrder->oa_number,
                'to_order_id' => $toOrderId,
                'to_order_number' => $toOrder->oa_number,
                'transfer_reason' => $remarks,
                'transferred_by' => auth()->user()->id ?? null,
                'transferred_at' => now(),
            ]);

            DB::commit();

            // Hide modal and reset fields
            $this->dispatchBrowserEvent('hide-batch-transfer-modal');
            $this->resetBatchTransferFields();

            // Show success message
            $this->alert('success', "Successfully transferred {$transferredCount} payments (₱" . number_format($totalAmount, 2) . ") from Order #{$fromOrder->oa_number} to Order #{$toOrder->oa_number}.");

            // Refresh data
            $this->calculateTotals();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', 'Batch transfer failed: ' . $e->getMessage());
        }
    }

    /**
     * Reset batch transfer fields
     */
    public function resetBatchTransferFields()
    {
        $this->batchTransferFromOrderId = null;
        $this->batchTransferToOrderId = null;
        $this->batchTransferToOrderNumber = '';
        $this->batchTransferSearchResults = [];
        $this->batchTransferOrderSearch = '';
        $this->batchTransferRemarks = '';
        $this->selectedOrderForBatchTransfer = null;
        $this->batchTransferPayments = [];
    }

    /**
     * Export payments to CSV
     */
    public function export()
    {
        $query = OrderPaymentHistory::with(['details', 'delivery'])
            ->whereBetween('date_of_payment', [
                $this->dateFrom,
                Carbon::parse($this->dateTo)->endOfDay()->toDateTimeString()
            ]);

        // Apply status filter
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Apply payment mode filter
        if (!empty($this->paymentMode)) {
            $query->where('mop', $this->paymentMode);
        }

        // Apply delivery filter
        switch ($this->deliveryFilter) {
            case 'with_delivery':
                $query->whereNotNull('delivery_id');
                break;
            case 'without_delivery':
                $query->whereNull('delivery_id');
                break;
            case 'specific':
                if (!empty($this->deliveryId)) {
                    $query->where('delivery_id', $this->deliveryId);
                }
                break;
            default:
                // 'all' - no filter needed
                break;
        }

        // Apply search filter
        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                // Search in payment fields
                $q->where('receipt_number', 'LIKE', "%{$search}%")
                    ->orWhere('batch_receipt_number', 'LIKE', "%{$search}%")
                    ->orWhere('reference_no', 'LIKE', "%{$search}%")
                    // Search in related order fields
                    ->orWhereHas('details', function ($orderQuery) use ($search) {
                        $orderQuery->where('oa_number', 'LIKE', "%{$search}%")
                            ->orWhere('oa_client', 'LIKE', "%{$search}%")
                            ->orWhere('oa_consultant', 'LIKE', "%{$search}%")
                            ->orWhere('oa_presenter', 'LIKE', "%{$search}%");
                    })
                    // Search in related delivery fields
                    ->orWhereHas('delivery', function ($deliveryQuery) use ($search) {
                        $deliveryQuery->where('transno', 'LIKE', "%{$search}%")
                            ->orWhere('client', 'LIKE', "%{$search}%");
                    });
            });
        }

        $payments = $query->orderBy('date_of_payment', 'desc')->get();

        $fileName = 'payments_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://temp', 'w');

        // Add headers
        fputcsv($handle, [
            'Receipt Number',
            'Batch Receipt Number',
            'Date of Payment',
            'Order Number',
            'Client',
            'Delivery Receipt',
            'Payment Mode',
            'Reference Number',
            'Amount',
            'Remaining Balance',
            'Status',
            'Remarks',
            'Due Date',
            'PDC Date',
            'Reconciliation Date'
        ]);

        // Add data rows
        foreach ($payments as $payment) {
            // Calculate remaining balance
            $subtotal = $payment->details->oa_price_override ? $payment->details->oa_price_override : $payment->details->items()->sum('item_total');
            $total = (float) $subtotal + (float) $payment->details->oa_price_diff;
            $totalPaid = $payment->details->payments()->where('status', '!=', 'Voided')->sum('amount');
            $remainingBalance = $total - $totalPaid;

            fputcsv($handle, [
                $payment->receipt_number ?? 'PR-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT),
                $payment->batch_receipt_number ?? 'N/A',
                $payment->date_of_payment,
                $payment->details->oa_number,
                $payment->details->oa_client,
                $payment->delivery ? $payment->delivery->transno : 'N/A',
                $payment->mop,
                $payment->reference_no,
                $payment->amount,
                $remainingBalance,
                $payment->status,
                $payment->remarks,
                $payment->due_date,
                $payment->pdc_date,
                $payment->recon_date
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        $this->dispatchBrowserEvent('download-csv', [
            'content' => $csv,
            'fileName' => $fileName
        ]);

        $this->alert('success', 'Export successful!');
    }

    /**
     * Refresh the data
     */
    public function refreshData()
    {
        $this->calculateTotals();
    }

    /**
     * Trigger print view
     */
    public function printReport()
    {
        return redirect()->route('payments.print', [
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'status' => $this->status,
            'payment_mode' => $this->paymentMode,
            'delivery_filter' => $this->deliveryFilter,
            'delivery_id' => $this->deliveryId,
            'search' => $this->search,
        ]);
    }
}

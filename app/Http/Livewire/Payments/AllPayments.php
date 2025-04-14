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

    // Related deliveries for edit modal
    public $relatedDeliveries = [];

    // Totals - computed on demand
    public $totalPosted;
    public $totalUnposted;
    public $totalOnHold;
    public $totalVoided;
    public $totalUnpaidBalance;

    // Listeners for events
    protected $listeners = ['refreshData', 'confirmVoid', 'resetEditFields'];

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

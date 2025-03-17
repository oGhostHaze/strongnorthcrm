<?php

namespace App\Http\Livewire\Payments;

use Livewire\Component;
use App\Models\OrderPaymentHistory;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
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

    // Totals - computed on demand
    public $totalPosted;
    public $totalUnposted;
    public $totalOnHold;
    public $totalVoided;

    // Listeners for events
    protected $listeners = ['refreshData', 'confirmVoid'];

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

        return view('livewire.payments.all-payments', [
            'payments' => $payments,
            'paymentModes' => $paymentModes,
            'statuses' => $statuses,
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
     * Get payments with applied filters
     */
    private function getPayments()
    {
        $query = OrderPaymentHistory::with('details')
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

        // Apply search filter
        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                // Search in payment fields
                $q->where('receipt_number', 'LIKE', "%{$search}%")
                  ->orWhere('batch_receipt_number', 'LIKE', "%{$search}%")
                  ->orWhere('reference_no', 'LIKE', "%{$search}%")
                  // Search in related order fields
                  ->orWhereHas('details', function($orderQuery) use ($search) {
                      $orderQuery->where('oa_number', 'LIKE', "%{$search}%")
                          ->orWhere('oa_client', 'LIKE', "%{$search}%")
                          ->orWhere('oa_consultant', 'LIKE', "%{$search}%")
                          ->orWhere('oa_presenter', 'LIKE', "%{$search}%");
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

        $this->totalPosted = OrderPaymentHistory::where('status', 'Posted')
            ->whereBetween('date_of_payment', $dateRange)
            ->sum('amount');

        $this->totalUnposted = OrderPaymentHistory::where('status', 'Unposted')
            ->whereBetween('date_of_payment', $dateRange)
            ->sum('amount');

        $this->totalOnHold = OrderPaymentHistory::where('status', 'On-hold')
            ->whereBetween('date_of_payment', $dateRange)
            ->sum('amount');

        $this->totalVoided = OrderPaymentHistory::where('status', 'Voided')
            ->whereBetween('date_of_payment', $dateRange)
            ->sum('amount');
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
     * Export payments to CSV
     */
    public function export()
    {
        $query = OrderPaymentHistory::with('details')
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

        // Apply search filter
        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                // Search in payment fields
                $q->where('receipt_number', 'LIKE', "%{$search}%")
                  ->orWhere('batch_receipt_number', 'LIKE', "%{$search}%")
                  ->orWhere('reference_no', 'LIKE', "%{$search}%")
                  // Search in related order fields
                  ->orWhereHas('details', function($orderQuery) use ($search) {
                      $orderQuery->where('oa_number', 'LIKE', "%{$search}%")
                          ->orWhere('oa_client', 'LIKE', "%{$search}%")
                          ->orWhere('oa_consultant', 'LIKE', "%{$search}%")
                          ->orWhere('oa_presenter', 'LIKE', "%{$search}%");
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
            'Payment Mode',
            'Reference Number',
            'Amount',
            'Status',
            'Remarks',
            'Due Date',
            'PDC Date',
            'Reconciliation Date'
        ]);

        // Add data rows
        foreach ($payments as $payment) {
            fputcsv($handle, [
                $payment->receipt_number ?? 'PR-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT),
                $payment->batch_receipt_number ?? 'N/A',
                $payment->date_of_payment,
                $payment->details->oa_number,
                $payment->details->oa_client,
                $payment->mop,
                $payment->reference_no,
                $payment->amount,
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
            'search' => $this->search,
        ]);
    }
}

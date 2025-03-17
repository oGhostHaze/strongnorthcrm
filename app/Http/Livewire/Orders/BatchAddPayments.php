<?php

namespace App\Http\Livewire\Orders;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\OrderPaymentHistory;
use App\Models\ModeOfPayment;
use App\Models\Order;

class BatchAddPayments extends Component
{
    public $payments = [], $oa = [];
    public $oa_id;
    public $batchReceiptNumber;

    public function mount($oa_id)
    {
        $this->oa_id = $oa_id;
        $this->oa = Order::find($oa_id);
        $this->batchReceiptNumber = $this->generateBatchReceiptNumber();
        $this->addPaymentRow();
    }

    /**
     * Generate a batch receipt number
     *
     * @return string
     */
    private function generateBatchReceiptNumber()
    {
        $lastPayment = OrderPaymentHistory::orderBy('created_at', 'desc')->first();

        if ($lastPayment && !empty($lastPayment->batch_receipt_number)) {
            $parts = explode('-', $lastPayment->batch_receipt_number);

            // If we have the expected format
            if (count($parts) === 3) {
                $prefix = $parts[0];
                $dateCode = date('Ymd'); // Today's date

                // If the date code is the same as today, increment the sequence
                if ($parts[1] === $dateCode) {
                    $sequence = (int)$parts[2] + 1;
                } else {
                    // New day, start from 1
                    $sequence = 1;
                }

                return $prefix . '-' . $dateCode . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }
        }

        // Default format if no previous receipt or incorrect format
        return 'PR-' . date('Ymd') . '-0001';
    }

    public function addPaymentRow()
    {
        $this->payments[] = [
            'oa_id' => '',
            'mop' => '',
            'amount' => '',
            'date_of_payment' => Carbon::now()->format('Y-m-d'),
            'remarks' => '',
            'status' => 'Unposted',
            'due_date' => null,
            'pdc_date' => null,
            'reference_no' => '',
            'recon_date' => null,
            'batch_receipt_number' => $this->batchReceiptNumber,
            'receipt_sequence' => count($this->payments) + 1, // Sequence within the batch
        ];
    }

    public function removePaymentRow($index)
    {
        unset($this->payments[$index]);
        $this->payments = array_values($this->payments);

        // Update sequences
        foreach ($this->payments as $i => $payment) {
            $this->payments[$i]['receipt_sequence'] = $i + 1;
        }
    }

    public function savePayments()
    {
        $this->validate([
            'payments.*.mop' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.date_of_payment' => 'required|date',
            'payments.*.remarks' => 'nullable|string',
            'payments.*.status' => 'required|in:Posted,Unposted,On-hold',
            'payments.*.due_date' => 'nullable|date',
            'payments.*.pdc_date' => 'nullable|date',
            'payments.*.reference_no' => 'nullable|string',
            'payments.*.recon_date' => 'nullable|date',
        ]);

        $createdPayments = [];
        $batchCount = count($this->payments);

        foreach ($this->payments as $index => $payment) {
            $payment['oa_id'] = $this->oa_id;

            // Assign the same batch receipt number to all payments in this batch
            $payment['batch_receipt_number'] = $this->batchReceiptNumber;

            // Create an individual receipt number for display - combines batch number with sequence
            $sequenceNumber = str_pad($payment['receipt_sequence'], 2, '0', STR_PAD_LEFT);
            $payment['receipt_number'] = $payment['batch_receipt_number'] . '-' . $sequenceNumber . '/' . str_pad($batchCount, 2, '0', STR_PAD_LEFT);

            // Create the payment record
            $createdPayment = OrderPaymentHistory::create($payment);
            $createdPayments[] = $createdPayment->id;
        }

        // Redirect to the receipt batch view
        return redirect()->route('receipt.batch', ['oa_id' => $this->oa_id])
            ->with('success', 'Batch payment with Receipt #' . $this->batchReceiptNumber . ' has been created successfully.');
    }

    public function render()
    {
        return view('livewire.orders.batch-add-payments', [
            'modes_of_payment' => ModeOfPayment::all(),
            'batchReceiptNumber' => $this->batchReceiptNumber,
        ]);
    }
}

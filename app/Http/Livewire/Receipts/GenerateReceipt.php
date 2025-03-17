<?php

namespace App\Http\Livewire\Receipts;

use App\Models\Order;
use App\Models\OrderPaymentHistory;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class GenerateReceipt extends Component
{
    use LivewireAlert;

    public $payments = [];
    public $oa;
    public $oa_id;
    public $printMode = false;

    public function mount($oa_id)
    {
        $this->oa_id = $oa_id;
        $this->oa = Order::find($oa_id);
        $this->loadPayments();
    }

    public function loadPayments()
    {
        $this->payments = OrderPaymentHistory::where('oa_id', $this->oa_id)
            ->orderBy('date_of_payment', 'desc')
            ->get();
    }

    public function togglePrintMode()
    {
        $this->printMode = !$this->printMode;
    }

    public function printReceipt($payment_id)
    {
        $this->dispatchBrowserEvent('print-receipt', ['payment_id' => $payment_id]);
    }

    public function printAllReceipts()
    {
        $this->togglePrintMode();
        $this->dispatchBrowserEvent('print-all-receipts');
    }

    public function voidPayment($payment_id)
    {
        $payment = OrderPaymentHistory::find($payment_id);

        if ($payment) {
            $payment->status = 'Voided';
            $payment->save();

            $this->alert('success', 'Payment receipt voided successfully');
            $this->loadPayments();
        } else {
            $this->alert('error', 'Unable to find the payment record');
        }
    }

    public function render()
    {
        return view('livewire.receipts.generate-receipt', [
            'payments' => $this->payments
        ]);
    }
}
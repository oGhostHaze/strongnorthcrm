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

    public function mount($oa_id)
    {
        $this->oa_id = $oa_id;
        $this->oa = Order::find($oa_id);
        $this->addPaymentRow();
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
        ];
    }

    public function removePaymentRow($index)
    {
        unset($this->payments[$index]);
        $this->payments = array_values($this->payments);
    }

    public function savePayments()
    {
        $this->validate([
            'payments.*.mop' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.date_of_payment' => 'required|date',
            'payments.*.remarks' => 'nullable|string',
            'payments.*.status' => 'required|in:Posted,Unposted,On-hold',
        ]);

        foreach ($this->payments as $payment) {
            $payment['oa_id'] = $this->oa_id;
            OrderPaymentHistory::create($payment);
        }

        session()->flash('success', 'Payments added successfully.');
        $this->payments = [];
        $this->addPaymentRow();
    }

    public function render()
    {
        return view('livewire.orders.batch-add-payments', [
            'modes_of_payment' => ModeOfPayment::all(),
        ]);
    }
}
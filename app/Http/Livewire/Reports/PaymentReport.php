<?php

namespace App\Http\Livewire\Reports;

use Illuminate\Database\Eloquent\Builder;
use App\Models\OrderPaymentHistory;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class PaymentReport extends Component
{
    use LivewireAlert;

    public $start_date, $end_date;
    public $payment_id, $status, $remarks, $search, $search_column = 'oa_client';

    public function render()
    {
        $start_date = Carbon::parse($this->start_date)->startOfMonth()->toDateString();
        $end_date = Carbon::parse($this->end_date)->endOfMonth()->toDateString();

        $reports = OrderPaymentHistory::whereBetween('date_of_payment', [$this->start_date, $this->end_date])
            ->whereHas('details', function (Builder $query) {
                if ($this->search) {
                    $query->where($this->search_column, 'LIKE', "%{$this->search}%");
                }
            })
            ->whereBetween('date_of_payment', [$start_date, $end_date])
            ->with('details')
            ->get();
        return view('livewire.reports.payment-report', compact('reports'));
    }

    public function mount()
    {
        $this->start_date = Carbon::now()->startOfMonth()->toDateString();
        $this->end_date = Carbon::now()->endOfMonth()->toDateString();
    }

    public function update_payment()
    {
        $this->validate([
            'payment_id' => 'required',
            'status' => 'required',
            'remarks' => 'nullable',
        ]);

        $payment = OrderPaymentHistory::find($this->payment_id);
        $payment->status = $this->status;
        $payment->remarks = $this->remarks;
        $payment->save();

        $this->reset('status', 'payment_id');

        $this->alert('success', 'Payment Updated!');
    }


}
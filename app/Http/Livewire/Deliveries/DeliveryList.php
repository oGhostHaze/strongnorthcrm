<?php

namespace App\Http\Livewire\Deliveries;

use App\Models\Order;
use Livewire\Component;
use App\Models\Delivery;
use Livewire\WithPagination;

class DeliveryList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = Delivery::orderByDesc('dr_count')
                        ->where('date', 'LIKE', '%'.$this->search.'%')
                        ->orWhere('transno', 'LIKE', '%'.$this->search.'%')
                        ->orWhere('client', 'LIKE', '%'.$this->search.'%')
                        ->orWhere('contact', 'LIKE', '%'.$this->search.'%')
                        ->orWhere('consultant', 'LIKE', '%'.$this->search.'%')
                        ->orWhere('associate', 'LIKE', '%'.$this->search.'%')
                        ->orWhere('presenter', 'LIKE', '%'.$this->search.'%')
                        ->orWhere('code', 'LIKE', '%'.$this->search.'%')
                        ->paginate(15);
        return view('livewire.deliveries.delivery-list', [
            'data' => $data
        ]);
    }

    public function view_dr($transno = null)
    {
        return redirect()->route('order.delivery.view', ['transno' => $transno]);
    }
}

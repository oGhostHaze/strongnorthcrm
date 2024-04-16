<?php

namespace App\Http\Livewire\Orders\Returns;

use App\Models\OrderReturnInfo;
use Livewire\Component;
use Livewire\WithPagination;

class Listreturn extends Component
{
    use WithPagination;
    public $paginationTheme = 'bootstrap';
    public $search;

    public function render()
    {
        $data = OrderReturnInfo::where('id', 'LIKE', '%'.$this->search.'%')->orWhere('oa_no', 'LIKE', '%'.$this->search.'%')->paginate(20);

        return view('livewire.orders.returns.listreturn', compact(
            'data'
        ));
    }

    public function view_rsn($rsn)
    {
        return redirect()->route('order.returns.view', ['rsn' => $rsn]);
    }
}

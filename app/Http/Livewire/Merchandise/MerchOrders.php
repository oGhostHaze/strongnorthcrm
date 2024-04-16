<?php

namespace App\Http\Livewire\Merchandise;

use App\Models\MerchandiseOrderHeader;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class MerchOrders extends Component
{
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['view_order'];

    public $search;
    public $transno, $print_count, $date, $client, $address, $contact, $consultant, $associate, $presenter, $team_builder, $distributor;

    public function render()
    {
        $data = MerchandiseOrderHeader::where('transno','like','%'.$this->search.'%')
                        ->orWhere('client','like','%'.$this->search.'%')
                        ->orWhere('presenter','like','%'.$this->search.'%')
                        ->orWhere('associate','like','%'.$this->search.'%')
                        ->orderByDesc('id')
                        ->paginate(20);

        return view('livewire.merchandise.merch-orders', compact('data'));
    }

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function save()
    {

        $date = date('Ymj');
        $this->print_count = MerchandiseOrderHeader::max('print_count') + 1;
        
        $this->transno = "MO" . $date . "-" . $this->print_count;

        $validated_data = $this->validate([
			'transno' => 'required',
			'print_count' => 'nullable',
            'date' => 'required',
            'client' => 'required',
            'address' => 'nullable',
            'contact' => 'nullable',
            'consultant' => 'nullable',
            'associate' => 'nullable',
            'presenter' => 'nullable',
            'team_builder' => 'nullable',
            'distributor' => 'nullable',
        ]);

        $new = MerchandiseOrderHeader::create($validated_data);
        $this->alert('success', 'Created new Merchandise Order #'.$validated_data['transno']);
        $this->view_order($new);
    }

    public function view_order(MerchandiseOrderHeader $order)
    {
        return redirect()->route('merch.order.view', ['merch' => $order]);
    }
}

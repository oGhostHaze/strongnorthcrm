<?php

namespace App\Http\Livewire\Servicing;

use App\Models\ServicingInfo;
use Livewire\Component;
use Livewire\WithPagination;

class SrList extends Component
{
    use WithPagination;
    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $sr_no, $client, $contact_no, $received_from, $date_received, $inspected_by, $inc;
    protected $listeners = ['view_servicing'];

    public function render()
    {
        $data = ServicingInfo::orderByDesc('date_received')
                            ->paginate(20);
        return view('livewire.servicing.sr-list', [
            'data' => $data
        ]);
    }

    public function mount()
    {
        $this->date_received = date('Y-m-d');
    }

    public function save()
    {
	    $date = date('Ymj');
        $this->inc = ServicingInfo::max('inc') + 1;
        $this->sr_no = "SR" . $date . "-" . $this->inc;

        $validatedData = $this->validate([
                'sr_no' => 'required',
                'client' => 'required',
                'contact_no' => 'required',
                'received_from' => 'required',
                'date_received' => 'required',
                'inspected_by' => 'required',
                'inc' => 'required',
            ]);

        ServicingInfo::create($validatedData);
        $this->dispatchBrowserEvent('success', 'New servicing details created.');
    }

    public function view_servicing(ServicingInfo $servicing)
    {
        return redirect()->route('servicing.view', ['servicing' => $servicing]);
    }


}

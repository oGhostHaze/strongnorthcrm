<?php

namespace App\Http\Livewire\Servicing;

use App\Models\ServicingInfo;
use Livewire\Component;

class SrView extends Component
{
    public $servicing;
    public $print_val;

    public function render()
    {
        return view('livewire.servicing.sr-view');
    }

    public function mount(ServicingInfo $servicing)
    {
        $this->servicing = $servicing;
    }

    public function print_this()
    {
        $this->print_val = true;
        $this->dispatchBrowserEvent('print_div');
    }
}

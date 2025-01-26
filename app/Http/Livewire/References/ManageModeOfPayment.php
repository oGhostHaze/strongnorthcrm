<?php

namespace App\Http\Livewire\References;

use Livewire\Component;
use App\Models\ModeOfPayment;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ManageModeOfPayment extends Component
{
    use LivewireAlert;

    public $search;
    public $legend, $name;

    public function render()
    {
        $mops = ModeOfPayment::where('legend', 'LIKE', '%'.$this->search.'%')->get();
        return view('livewire.references.manage-mode-of-payment', compact(
            'mops'
        ));
    }

    public function save()
    {
        $validated = $this->validate(['legend' => ['required', 'string', 'unique:mode_of_payments,legend'], 'name' => ['nullable', 'string']]);
        $validated['name'] = ucwords($validated['name']);
        ModeOfPayment::create($validated);
        $this->reset();
        $this->alert('success', 'Mode of Payment '.$validated['name'].' has been added successfully!');
    }
}

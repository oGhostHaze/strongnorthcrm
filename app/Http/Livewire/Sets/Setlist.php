<?php

namespace App\Http\Livewire\Sets;

use App\Models\Set;
use Livewire\Component;

class Setlist extends Component
{
    protected $listeners = ['view_set'];

    public function render()
    {
        $sets = Set::all();

        return view('livewire.sets.setlist', compact(
            'sets'
        ));
    }

    public function view_set(Set $set)
    {
        return redirect()->route('set.view', ['set' => $set]);
    }
}

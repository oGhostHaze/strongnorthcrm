<?php

namespace App\Http\Livewire\Supplies;

use App\Models\SupplyLocation as Location;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SupplyLocation extends Component
{
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';
    // protected $listeners = ['update_price'];
    public $search, $name;

    public function render()
    {
        $locations = Location::where('name', 'LIKE', '%'.$this->search.'%')->paginate(20);

        return view('livewire.supplies.supply-location', compact(
            'locations'
        ));
    }

    public function save()
    {
        $validated = $this->validate(['name' => ['required', 'string', 'unique:supply_locations,name']]);
        Location::create($validated);
        $this->reset();
        $this->alert('success', 'Location '.$validated['name'].' has been added successfully!');
    }
}

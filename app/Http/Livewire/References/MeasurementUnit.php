<?php

namespace App\Http\Livewire\References;

use App\Models\UnitOfMeasurement;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class MeasurementUnit extends Component
{
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';
    // protected $listeners = ['update_price'];
    public $search, $unit, $description;

    public function render()
    {
        $measurements = UnitOfMeasurement::where('unit', 'LIKE', '%'.$this->search.'%')->paginate(20);

        return view('livewire.references.measurement-unit', compact(
            'measurements'
        ));
    }

    public function save()
    {
        $validated = $this->validate(['unit' => ['required', 'string', 'unique:unit_of_measurements,unit'], 'description' => ['nullable', 'string']]);
        UnitOfMeasurement::create($validated);
        $this->reset();
        $this->alert('success', 'Unit of Measurement '.$validated['unit'].' has been added successfully!');
    }
}

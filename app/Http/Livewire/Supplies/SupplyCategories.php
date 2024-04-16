<?php

namespace App\Http\Livewire\Supplies;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SupplyCategory;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SupplyCategories extends Component
{
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';
    // protected $listeners = ['update_price'];
    public $search, $name;

    public function render()
    {
        $categories = SupplyCategory::where('name', 'LIKE', '%'.$this->search.'%')->paginate(20);

        return view('livewire.supplies.supply-categories', compact(
            'categories'
        ));
    }

    public function save()
    {
        $validated = $this->validate(['name' => ['required', 'string', 'unique:supply_categories,name']]);
        SupplyCategory::create($validated);
        $this->reset();
        $this->alert('success', 'Category '.$validated['name'].' has been added successfully!');
    }
}

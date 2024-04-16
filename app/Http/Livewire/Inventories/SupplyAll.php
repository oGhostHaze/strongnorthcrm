<?php

namespace App\Http\Livewire\Inventories;

use App\Models\SupplyInventoryItem;
use Livewire\Component;
use Livewire\WithPagination;

class SupplyAll extends Component
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
        $data = SupplyInventoryItem::whereRelation('item', 'item_name', 'like', '%'.$this->search.'%')
                            ->orderByDesc('date')
                            ->paginate(20);

        return view('livewire.inventories.supply-all', compact(
            'data'
        ));
    }
}

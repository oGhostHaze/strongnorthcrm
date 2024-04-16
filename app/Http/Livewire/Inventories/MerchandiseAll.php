<?php

namespace App\Http\Livewire\Inventories;

use App\Models\MerchandiseInventoryItem;
use Livewire\Component;
use Livewire\WithPagination;

class MerchandiseAll extends Component
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
        $data = MerchandiseInventoryItem::whereRelation('product', 'item', 'like', '%'.$this->search.'%')
                            ->orderByDesc('date')
                            ->paginate(20);

        return view('livewire.inventories.merchandise-all', [
            'data' => $data,
        ]);
    }
}

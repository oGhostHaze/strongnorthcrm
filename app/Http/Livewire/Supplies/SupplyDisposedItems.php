<?php

namespace App\Http\Livewire\Supplies;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SupplyItemDisposed;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SupplyDisposedItems extends Component
{
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';
    // protected $listeners = ['update_price'];
    public $search;

    public function render()
    {
        $supplies = SupplyItemDisposed::whereRelation('item', 'item_name', 'LIKE', '%'.$this->search.'%')->paginate(20);

        return view('livewire.supplies.supply-disposed-items', compact(
            'supplies',
        ));
    }
}

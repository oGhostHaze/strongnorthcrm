<?php

namespace App\Http\Livewire\Inventories;

use App\Models\InventoryItem;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class All extends Component
{
    public $search, $start_date, $end_date;

    public function render()
    {
        $end_date = Carbon::parse($this->end_date)->endOfDay()->toDateString();
        $data = InventoryItem::whereRelation('product', 'product_description', 'like', '%'.$this->search.'%')
                            ->whereBetween('created_at', [$this->start_date, $end_date])
                            ->orderByDesc('inventory_date_id')
                            ->get();

        return view('livewire.inventories.all', [
            'data' => $data,
        ]);
    }

    public function mount()
    {
        $this->start_date = Carbon::now()->subWeek()->toDateString();
        $this->end_date = Carbon::now()->toDateString();
    }
}

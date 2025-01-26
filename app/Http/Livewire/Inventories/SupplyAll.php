<?php

namespace App\Http\Livewire\Inventories;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SupplyInventoryItem;

class SupplyAll extends Component
{

    public $search;
    public $start_date, $end_date;

    public function render()
    {
        $end_date = Carbon::parse($this->end_date)->endOfDay()->toDateString();
        $data = SupplyInventoryItem::with('item', 'inv')->whereRelation('item', 'item_name', 'like', '%'.$this->search.'%')
                            ->whereHas('inv', function($query) use ($end_date) {
                                $query->whereBetween('date', [$this->start_date, $end_date]);
                            })
                            ->orderByDesc('date')
                            ->get();

        return view('livewire.inventories.supply-all', compact(
            'data'
        ));
    }

    public function mount()
    {
        $this->start_date = Carbon::now()->subWeek()->toDateString();
        $this->end_date = Carbon::now()->toDateString();
    }
}

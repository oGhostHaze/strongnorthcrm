<?php

namespace App\Http\Livewire\Inventories;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MerchandiseInventoryItem;

class MerchandiseAll extends Component
{

    public $search, $start_date, $end_date;

    public function render()
    {
        $end_date = Carbon::parse($this->end_date)->endOfDay()->toDateString();
        $data = MerchandiseInventoryItem::whereRelation('product', 'item', 'like', '%'.$this->search.'%')
                            ->whereBetween('date', [$this->start_date, $end_date])
                            ->orderByDesc('date')
                            ->get();

        return view('livewire.inventories.merchandise-all', [
            'data' => $data,
        ]);
    }

    public function mount()
    {
        $this->start_date = Carbon::now()->subWeek()->toDateString();
        $this->end_date = Carbon::now()->toDateString();
    }
}

<?php

namespace App\Http\Livewire\Inventories;

use App\Models\InventoryItem;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class All extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search, $start_date, $end_date, $page_number = 20;


    public function updatedPageNumber()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatedStartDate()
    {
        $this->resetPage();
    }
    
    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $end_date = Carbon::parse($this->end_date)->addDay();
        $data = InventoryItem::whereRelation('product', 'product_description', 'like', '%'.$this->search.'%')
                            ->whereBetween('created_at', [$this->start_date, $end_date])
                            ->orderByDesc('inventory_date_id')
                            ->paginate($this->page_number);

        return view('livewire.inventories.all', [
            'data' => $data,
        ]);
    }
    
    public function mount()
    {
        $this->start_date = date('Y-m-d');
        $this->end_date = date('Y-m-d');
    }
}

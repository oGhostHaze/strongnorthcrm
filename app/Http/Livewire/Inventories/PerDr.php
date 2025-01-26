<?php

namespace App\Http\Livewire\Inventories;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class PerDr extends Component
{

    public $search = '';
    public $start_date, $end_date;

    public function render()
    {

        $end_date = Carbon::parse($this->end_date)->endOfDay()->toDateString();

        $data = DB::table('delivery_items')
                    ->selectRaw('delivery_info.date, delivery_items.transno, delivery_info.client AS client, tblproducts.product_description, delivery_items.item_price, delivery_items.item_qty, delivery_items.item_total, delivery_items.status, delivery_info.code AS code, delivery_info.dr_reference AS dr_reference')
                    ->leftJoin('delivery_info', 'delivery_items.transno', 'delivery_info.transno')
                    ->join('tblproducts', 'delivery_items.product_id', 'tblproducts.product_id')
                    ->whereBetween('delivery_info.date', [$this->start_date, $end_date])
                    ->where(function($query) {
                        return $query->where('tblproducts.product_description', 'like', '%'.$this->search.'%')
                                    ->orWhere('delivery_items.transno', 'like', '%'.$this->search.'%')
                                    ->orWhere('delivery_info.client' , 'like', '%'.$this->search.'%');
                      })
                    ->orderByDesc('delivery_info.dr_count')
                    ->get();

        return view('livewire.inventories.per-dr', [
            'data' => $data,
        ]);
    }

    public function mount()
    {
        $this->start_date = Carbon::now()->subWeek()->toDateString();
        $this->end_date = Carbon::now()->toDateString();
    }
}

<?php

namespace App\Http\Livewire\Inventories;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Carbon\Carbon;

class PerDrFilter extends Component
{
    use WithPagination;
    public $paginationTheme = 'bootstrap';

    public $search = '', $from_date, $to_date;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // $data = DB::table('all_out')
        //             ->selectRaw('all_out.date, all_out.transno, delivery_info.client AS client, all_out.product_description, all_out.item_price, all_out.item_qty, all_out.item_total, all_out.type, all_out.status, delivery_info.code AS code, delivery_info.dr_reference AS dr_reference')
        //             ->leftJoin('delivery_info', 'all_out.transno', 'delivery_info.transno')
        //             ->orWhere(function($query) {
        //                 return $query->where('all_out.product_description', 'like', '%'.$this->search.'%')
        //                             ->orWhere('all_out.transno', 'like', '%'.$this->search.'%')
        //                             ->orWhere('delivery_info.client' , 'like', '%'.$this->search.'%');
        //               })
        //             ->orderByDesc('all_out.dr_count')
        //             ->paginate(20);
        $from_date = Carbon::parse($this->from_date)->startOfDay()->format('Y-m-d H:i:s');
        $to_date = Carbon::parse($this->to_date)->endOfDay()->format('Y-m-d H:i:s');
        
        
        $data = DB::table('delivery_items')
                    ->selectRaw('delivery_info.date, delivery_items.transno, delivery_info.client AS client, tblproducts.product_description, delivery_items.item_price, delivery_items.item_qty, delivery_items.item_total, delivery_items.status, delivery_info.code AS code, delivery_info.dr_reference AS dr_reference')
                    ->leftJoin('delivery_info', 'delivery_items.transno', 'delivery_info.transno')
                    ->join('tblproducts', 'delivery_items.product_id', 'tblproducts.product_id')
                    ->where(function($query) {
                        return $query->where('tblproducts.product_description', 'like', '%'.$this->search.'%')
                                    ->orWhere('delivery_items.transno', 'like', '%'.$this->search.'%')
                                    ->orWhere('delivery_info.client' , 'like', '%'.$this->search.'%');
                      })
                    ->whereBetween('delivery_info.date', [$from_date, $to_date])
                    ->orderByDesc('delivery_info.dr_count')
                    ->get();
                    
        return view('livewire.inventories.per-dr-filter', [
            'data' => $data,
        ]);
    }
    
    public function mount()
    {
        $this->from_date = $this->to_date = Carbon::parse(now())->format('Y-m-d');
    }
}

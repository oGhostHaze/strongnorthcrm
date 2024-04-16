<?php

namespace App\Http\Livewire\Inventories;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class PerDr extends Component
{
    use WithPagination;
    public $paginationTheme = 'bootstrap';

    public $search = '';

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
        
        
        $data = DB::table('delivery_items')
                    ->selectRaw('delivery_info.date, delivery_items.transno, delivery_info.client AS client, tblproducts.product_description, delivery_items.item_price, delivery_items.item_qty, delivery_items.item_total, delivery_items.status, delivery_info.code AS code, delivery_info.dr_reference AS dr_reference')
                    ->leftJoin('delivery_info', 'delivery_items.transno', 'delivery_info.transno')
                    ->join('tblproducts', 'delivery_items.product_id', 'tblproducts.product_id')
                    ->orWhere(function($query) {
                        return $query->where('tblproducts.product_description', 'like', '%'.$this->search.'%')
                                    ->orWhere('delivery_items.transno', 'like', '%'.$this->search.'%')
                                    ->orWhere('delivery_info.client' , 'like', '%'.$this->search.'%');
                      })
                    ->orderByDesc('delivery_info.dr_count')
                    ->paginate(20);
                    
        return view('livewire.inventories.per-dr', [
            'data' => $data,
        ]);
    }
}

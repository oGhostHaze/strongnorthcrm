<?php

namespace App\Http\Livewire\Merchandise;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MerchandiseItem;
use App\Models\MerchandiseStockIn;
use Illuminate\Support\Facades\Artisan;
use App\Models\MerchandiseInventoryDate;
use App\Models\MerchandiseInventoryItem;
use App\Http\Controllers\InventoryController;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class MerchStockIn extends Component
{
    use WithPagination;
    use LivewireAlert
    ;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete_stk'];

    public $product_id, $search, $stockin_qty, $remarks;

    public function render()
    {
        $data = MerchandiseStockIn::orderByDesc('id')->paginate(15);
        return view('livewire.merchandise.merch-stock-in',[
            'data' => $data,
            'merchs' => MerchandiseItem::all()
        ]);
    }

    public function submit_stk()
    {
        $this->validate([
            'product_id' => 'required|numeric',
            'stockin_qty' => 'required|min:1',
        ]);
        // Artisan::call('inventory:begin');

        MerchandiseStockIn::create([
            'product_id' => $this->product_id,
            'stockin_qty' => $this->stockin_qty,
            'remarks' => $this->remarks,
            'date' => date('Y-m-d'),
            'stockin_by' => auth()->user()->id,
        ]);

        $update = MerchandiseItem::find($this->product_id);
        $update->qty += $this->stockin_qty;
        $update->save();

        InventoryController::record_merchandise_inventory('stockin', $this->stockin_qty, $this->product_id);

        // $inventory_date = MerchandiseInventoryDate::firstOrCreate(['date' => date('Y-m-d')]);
        // $inventory_item = MerchandiseInventoryItem::firstOrCreate([
        //     'date' => $inventory_date->id,
        //     'product_id' => $this->product_id
        // ]);
        // $inventory_item->total_delivered += $this->stockin_qty;
        // $inventory_item->save();

        $this->alert('success', $this->stockin_qty.' '.$update->item.' added to merchandise stocks.');
        $this->reset();
    }

    public function delete_stk($stk_id)
    {
        // Artisan::call('inventory:begin');
        $stk = MerchandiseStockIn::find($stk_id);
        $product_id = $stk->product_id;
        $qty = $stk->stockin_qty;

        $update = MerchandiseItem::find($product_id);
        if($update->qty >= $qty){
            $update->qty -= $qty;
            $update->save();

            $inventory_date = MerchandiseInventoryDate::firstOrCreate(['date' => date('Y-m-d')]);
            $inventory_item = MerchandiseInventoryItem::firstOrCreate([
                'date' => $inventory_date->id,
                'product_id' => $product_id
            ]);
            $inventory_item->total_delivered -= $qty;
            $inventory_item->save();

            MerchandiseStockIn::destroy($stk_id);
            $this->alert('success', $this->stockin_qty.' '.$update->product_description.' removed from merchandise stocks.');
            $this->reset();
        }else{
            $this->alert('error', 'Current stock qty is low.');
        }
    }
}

<?php

namespace App\Http\Livewire\Products;

use App\Models\Product;
use App\Models\Stockin;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InventoryDate;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Artisan;

class StockinList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete_stk'];

    public $product_id, $search, $stockin_qty, $remarks;

    public function render()
    {
        $data = Stockin::whereRelation('product', 'product_description', 'LIKE','%'.$this->search.'%')->orderByDesc('stockIn_id')->paginate(15);
        return view('livewire.products.stockin-list',[
            'data' => $data,
            'products' => Product::all()
        ]);
    }

    public function submit_stk()
    {
        $this->validate([
            'product_id' => 'required|numeric',
            'stockin_qty' => 'required|min:1',
        ]);
        Artisan::call('inventory:begin');

        Stockin::create([
            'product_id' => $this->product_id,
            'stockin_qty' => $this->stockin_qty,
            'remarks' => $this->remarks,
            'date' => now(),
            'stockin_by' => auth()->user()->id,
        ]);

        $update = Product::find($this->product_id);
        $update->product_qty += $this->stockin_qty;
        $update->save();

        $inventory_date = InventoryDate::firstOrCreate(['inv_date' => date('Y-m-d')]);
        $inventory_item = InventoryItem::firstOrCreate([
            'inventory_date_id' => $inventory_date->id,
            'product_id' => $this->product_id
        ]);
        $inventory_item->total_delivered += $this->stockin_qty;
        $inventory_item->save();
        $this->dispatchBrowserEvent('success', $this->stockin_qty.' '.$update->product_description.' added to stocks.');
        $this->reset();
    }

    public function delete_stk($stk_id)
    {
        Artisan::call('inventory:begin');
        $stk = Stockin::find($stk_id);
        $product_id = $stk->product_id;
        $qty = $stk->stockin_qty;

        $update = Product::find($product_id);
        if($update->product_qty >= $qty){
            $update->product_qty -= $qty;
            $update->save();

            $inventory_date = InventoryDate::firstOrCreate(['inv_date' => date('Y-m-d')]);
            $inventory_item = InventoryItem::firstOrCreate([
                'inventory_date_id' => $inventory_date->id,
                'product_id' => $product_id
            ]);
            $inventory_item->total_delivered -= $qty;
            $inventory_item->save();

            Stockin::destroy($stk_id);
            $this->dispatchBrowserEvent('success', $this->stockin_qty.' '.$update->product_description.' removed from stocks.');
        }else{
            $this->dispatchBrowserEvent('error', 'Current stock qty is low.');
        }
    }
}

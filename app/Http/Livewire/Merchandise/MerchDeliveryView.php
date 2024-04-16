<?php

namespace App\Http\Livewire\Merchandise;

use App\Http\Controllers\InventoryController;
use App\Models\MerchandiseDelivery;
use App\Models\MerchandiseDeliveryItem;
use App\Models\MerchandiseInventoryDate;
use App\Models\MerchandiseInventoryItem;
use App\Models\MerchandiseItem;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class MerchDeliveryView extends Component
{
    use LivewireAlert;

    public $delivery, $price_override, $price_difference;
    public $print_val = false;
    public $back;
    protected $listeners = ['print_this', 'release_item', 'to_follow_item'];

    public function render()
    {
        return view('livewire.merchandise.merch-delivery-view');
    }

    public function mount($transno)
    {
        $this->delivery = MerchandiseDelivery::where('transno', $transno)->latest()->first();
        $this->back = url()->previous();
    }

    public function release_item($item_id)
    {
        $item = MerchandiseDeliveryItem::find($item_id);
        $product = MerchandiseItem::find($item->product_id);
        if($product->qty >= $item->item_qty){
            $item->status = 'For Releasing';
            $item->save();
            $this->alert('success', 'Item Released');
        }else{
            $this->alert('error', 'Item is unable to be released due to insufficient stocks.');
        }
    }

    public function to_follow_item($item_id)
    {
        $item = MerchandiseDeliveryItem::find($item_id);
        $item->status = 'To Follow';
        $item->save();
        $this->alert('success', 'Item marked as to follow');
    }

    public function override_price()
    {
        $this->validate(['price_override' => 'required']);
        $this->delivery->price_override = $this->price_override;
        $this->delivery->save();
        $items = $this->delivery->items()->get();
        foreach($items as $item){
            $item->item_total = 0;
            $item->save();
        }
        $this->alert('success', 'Total price successfully overriden for DR #'.$this->delivery->transno);
    }

    public function add_pricediff()
    {
        $this->validate(['price_difference'=>'required']);
        $this->delivery->price_diff = $this->price_difference;
        $this->delivery->save();
        $this->alert('success', 'Updated price difference for DR #'.$this->delivery->transno);
    }

    public function cancel_override()
    {
        $this->delivery->price_override = null;
        $this->delivery->save();
        $items = $this->delivery->items()->get();
        foreach($items as $item){
            $item->item_total = (float)$item->item_qty * (float)$item->item_price;
            $item->save();
        }
        $this->alert('success', 'Total price successfully overriden for DR #'.$this->delivery->transno);
    }

    public function print_this()
    {
        $this->print_val = true;
        $this->dispatchBrowserEvent('print_div');
    }

    public function finalize_dr()
    {

        // Artisan::call('inventory:begin');

        $items = $this->delivery->items()->where('status', 'For Releasing')->get();
        foreach($items as $item){
            $product = MerchandiseItem::find($item->product_id);
            if($product->qty >= $item->item_qty){

                $order = $this->delivery->mo->items()->where('product_id', $item->product_id)->where('item_qty_ordered', $item->item_qty)->first();
                $order->item_qty_released = $item->item_qty;
                $order->save();

                $product->qty -= $item->item_qty;
                $product->save();

                InventoryController::record_merchandise_inventory('delivery', $item->item_qty, $item->product_id);

                $item->status = 'Released';
            }else{
                $item->status = 'To Follow';
            }
            $item->save();
        }

        $this->delivery->print_count = 1;
        $this->delivery->save();
        $this->alert('success', 'DR created successfully');
    }

    public function cancel_dr()
    {
        $merch = MerchandiseDelivery::where('transno', $this->delivery->transno)->where('print_count', '0')->first();
        $merch->delete();

        $items = MerchandiseDeliveryItem::where('transno', $this->delivery->transno)->get();
        foreach($items as $item){
            $item->delete();
        }
        $this->alert('success', 'DR Cancelled Successfuly');
        return redirect($this->back);
    }
}

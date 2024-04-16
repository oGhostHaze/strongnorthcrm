<?php

namespace App\Http\Livewire\Deliveries;

use App\Models\Order;
use App\Models\Product;
use Livewire\Component;
use App\Models\Delivery;
use App\Models\OrderItem;
use App\Models\DeliveryGift;
use App\Models\DeliveryItem;
use App\Models\InventoryDate;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Artisan;

class DeliveryView extends Component
{
    public $delivery, $price_override, $price_difference;
    public $print_val = false;
    protected $listeners = ['print_this', 'release_item', 'to_follow_item', 'release_gift', 'to_follow_gift'];

    public function render()
    {
        return view('livewire.deliveries.delivery-view');
    }

    public function mount($transno)
    {
        $this->delivery = Delivery::where('transno', $transno)->first();
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
        $this->dispatchBrowserEvent('success', 'Total price successfully overriden for DR #'.$this->delivery->transno);
    }

    public function add_pricediff()
    {
        $this->validate(['price_difference'=>'required']);
        $this->delivery->price_diff = $this->price_difference;
        $this->delivery->save();
        $this->dispatchBrowserEvent('success', 'Updated price difference for DR #'.$this->delivery->transno);
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
        $this->dispatchBrowserEvent('success', 'Total price successfully overriden for DR #'.$this->delivery->transno);
    }

    public function print_this()
    {
        $this->print_val = true;
        $this->dispatchBrowserEvent('print_div');
    }

    public function release_item($item_id)
    {
        $item = DeliveryItem::find($item_id);
        $product = Product::find($item->product_id);
        if($product->product_qty >= $item->item_qty){
            $item->status = 'For Releasing';
            $item->save();
            $this->dispatchBrowserEvent('success', 'Item Released');
        }else{
            $this->dispatchBrowserEvent('error', 'Item is unable to be released due to insufficient stocks.');
        }
    }

    public function to_follow_item($item_id)
    {
        $item = DeliveryItem::find($item_id);
        $item->status = 'To Follow';
        $item->save();
        $this->dispatchBrowserEvent('success', 'Item marked as to follow');
    }

    public function release_gift($gift_id)
    {
        $gift = DeliveryGift::find($gift_id);
        $product = Product::find($gift->product_id);
        if($product->product_qty >= $gift->item_qty){
            $gift->status = 'For Releasing';
            $gift->save();
            $this->dispatchBrowserEvent('success', 'Gift Released');
        }else{
            $this->dispatchBrowserEvent('error', 'Gift is unable to be released due to insufficient stocks.');
        }
    }

    public function to_follow_gift($gift_id)
    {
        $gift = DeliveryGift::find($gift_id);
        $gift->status = 'To Follow';
        $gift->save();
        $this->dispatchBrowserEvent('success', 'Gift marked as to follow');
    }

    public function finalize_dr()
    {

        Artisan::call('inventory:begin');

        $items = $this->delivery->items()->where('status', 'For Releasing')->get();
        foreach($items as $item){
            $product = Product::find($item->product_id);
            if($product->product_qty >= $item->item_qty){
                $product->product_qty -= $item->item_qty;
                $product->save();

                $order = $this->delivery->oa->items()->where('product_id', $item->product_id)->first();
                $order->item_qty -= $item->item_qty;
                $order->released += $item->item_qty;
                $order->save();

                $inventory_date = InventoryDate::firstOrCreate(['inv_date' => date('Y-m-d')]);
                $inventory_item = InventoryItem::firstOrCreate([
                    'inventory_date_id' => $inventory_date->id,
                    'product_id' => $item->product_id
                ]);
                $inventory_item->total_released += $item->item_qty;
                $inventory_item->save();

                $item->status = 'Released';
            }else{
                $item->status = 'To Follow';
            }
            $item->save();
        }


        $gifts = $this->delivery->gifts()->where('status', 'For Releasing')->get();
        foreach($gifts as $gift){
            $product = Product::find($gift->product_id);
            if($product->product_qty >= $gift->item_qty){
                $product->product_qty -= $gift->item_qty;
                $product->save();

                $order = $this->delivery->oa->gifts()->where('product_id', $gift->product_id)->first();
                $order->item_qty -= $gift->item_qty;
                $order->released += $gift->item_qty;
                $order->save();

                $inventory_date = InventoryDate::firstOrCreate(['inv_date' => date('Y-m-d')]);
                $inventory_item = InventoryItem::firstOrCreate([
                    'inventory_date_id' => $inventory_date->id,
                    'product_id' => $gift->product_id
                ]);
                $inventory_item->total_released += $gift->item_qty;
                $inventory_item->save();

                $gift->status = 'Released';
            }else{
                $gift->status = 'To Follow';
            }
            $gift->save();
        }
        $this->delivery->print_count = 1;
        $this->delivery->save();
        $this->dispatchBrowserEvent('success', 'DR created successfully');
    }
}

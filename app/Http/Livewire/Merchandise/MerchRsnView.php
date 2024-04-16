<?php

namespace App\Http\Livewire\Merchandise;

use App\Http\Controllers\InventoryController;
use App\Models\MerchandiseItem;
use App\Models\MerchandiseOrderItem;
use App\Models\MerchandiseOrderReturn;
use App\Models\MerchandiseOrderReturnInfo;
use App\Models\OrderGift;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Artisan;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class MerchRsnView extends Component
{
    use LivewireAlert;

    public $rsn;
    public $item_id, $item_qty, $item_remarks, $print_val = false;

    protected $listeners = ['approve_returns'];

    public function render()
    {
        $items = MerchandiseOrderItem::where('transno', $this->rsn->oa_no)->get();

        return view('livewire.merchandise.merch-rsn-view', [
            'items' => $items,
        ]);
    }

    public function mount($rsn)
    {
        $this->rsn = MerchandiseOrderReturnInfo::find($rsn);
    }

    public function return_item()
    {
        $this->validate(['item_id' => 'required']);
        $item = MerchandiseOrderItem::find($this->item_id);

        $qty = $item->item_qty_released;
        $current_returns = MerchandiseOrderReturn::where('item_id', $item->id)->where('return_no', $this->rsn->id)->first();
        $qty -= $current_returns->qty ?? 0;


        $this->validate(['item_qty' => ['required', 'integer', 'max:' . $qty]]);
        MerchandiseOrderReturn::create([
            'product_id' => $item->product_id,
            'item_id' => $item->id,
            'qty' => $this->item_qty,
            'date_returned' => now(),
            'return_no' => $this->rsn->id,
            'reason' => $this->item_remarks,
        ]);

        $this->resetExcept('rsn');
    }

    public function confirm_approval()
    {
        $this->alert('warning', 'Approve Return Slip? Note that after confirmation, return quantity will reflect on product inventory and actions shall be disabled in this return slip.', [
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Confirm',
            'onConfirmed' => 'approve_returns',
            'allowOutsideClick' => false,
            'timer' => null,
            'toast' => false
        ]);
    }

    public function approve_returns()
    {
        Artisan::call('inventory:merchandise');

        $items = $this->rsn->return_items()->get();
        foreach ($items as $return) {
            $update_item = [];
            $update_item = MerchandiseOrderItem::find($return->item_id);
            $product = MerchandiseItem::find($return->product_id);
            $product->qty += $return->qty;

            $update_item->item_qty_returned += $return->qty;
            $update_item->item_qty_released -= $return->qty;
            $update_item->item_total = $update_item->item_qty_released * $update_item->price;

            $product->save();
            $update_item->save();
            InventoryController::record_merchandise_inventory('return', $return->qty, $return->product_id);
        }

        $this->rsn->status = 'Approved';
        $this->rsn->save();

        $this->alert('success', 'RSN-' . $this->rsn->id . ' approved! Product inventory updated.');
    }
}

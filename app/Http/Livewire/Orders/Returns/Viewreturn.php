<?php

namespace App\Http\Livewire\Orders\Returns;

use App\Http\Controllers\InventoryController;
use App\Models\Product;
use Livewire\Component;
use App\Models\OrderGift;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\OrderReturnInfo;
use Illuminate\Support\Facades\Artisan;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Viewreturn extends Component
{
    use LivewireAlert;

    public $rsn;
    public $item_id, $gift_id, $item_qty, $item_remarks, $gift_qty, $gift_remarks, $print_val = false;
    public $status;

    protected $listeners = ['approve_returns', 'reject_returns'];

    public function render()
    {
        $items = OrderItem::where('oa_id', $this->rsn->oa_id)->where('released', '<>', '0')->get();
        $gifts = OrderGift::where('oa_id', $this->rsn->oa_id)->where('released', '<>', '0')->get();

        return view('livewire.orders.returns.viewreturn', compact(
            'items',
            'gifts'
        ));
    }

    public function mount($rsn)
    {
        $this->rsn = OrderReturnInfo::find($rsn);
    }

    public function return($type)
    {
        if ($type == 'item') {
            $this->validate(['item_id' => 'required']);
            $item = OrderItem::find($this->item_id);
        } else {
            $this->validate(['gift_id' => 'required']);
            $item = OrderGift::find($this->gift_id);
        }

        $qty = $item->released;
        $current_returns = OrderReturn::where('item_id', $item->item_id)->where('item_type', $type)->where('return_no', $this->rsn->id)->first();
        $qty -= $current_returns->qty ?? 0;


        // $this->validate(['item_qty' => ['required', 'integer','max:'.$qty]]);
        if ($qty < $this->item_qty) {
            $this->alert('error', 'Return qty must not be greater than ' . $qty);
        } else {
            OrderReturn::create([
                'product_id' => $item->product_id,
                'item_id' => $item->item_id,
                'qty' => $this->item_qty,
                'date_returned' => now(),
                'item_type' => $type,
                'return_no' => $this->rsn->id,
                'reason' => $this->item_remarks,
            ]);

            $this->resetExcept('rsn');
        }
    }

    public function return_item($type)
    {
        $this->validate(['item_id' => 'required']);
        $item = OrderItem::find($this->item_id);

        $qty = $item->released;
        $current_returns = OrderReturn::where('item_id', $item->item_id)->where('item_type', $type)->where('return_no', $this->rsn->id)->first();
        $qty -= $current_returns->qty ?? 0;


        $this->validate(['item_qty' => ['required', 'integer', 'max:' . $qty]]);
        OrderReturn::create([
            'product_id' => $item->product_id,
            'item_id' => $item->item_id,
            'qty' => $this->item_qty,
            'date_returned' => now(),
            'item_type' => $type,
            'return_no' => $this->rsn->id,
            'reason' => $this->item_remarks,
        ]);

        $this->resetExcept('rsn');
    }

    public function return_gift($type)
    {
        $this->validate(['gift_id' => 'required']);
        $item = OrderGift::find($this->gift_id);

        $qty = $item->released;
        $current_returns = OrderReturn::where('item_id', $item->gift_id)->where('item_type', $type)->where('return_no', $this->rsn->id)->first();
        $qty -= $current_returns->qty ?? 0;


        $this->validate(['gift_qty' => ['required', 'integer', 'max:' . $qty]]);
        OrderReturn::create([
            'product_id' => $item->product_id,
            'item_id' => $item->gift_id,
            'qty' => $this->gift_qty,
            'date_returned' => now(),
            'item_type' => $type,
            'return_no' => $this->rsn->id,
            'reason' => $this->gift_remarks,
        ]);

        $this->resetExcept('rsn');
    }

    public function confirm_approval($status)
    {
        $this->status = $status;

        if($status == 'Approved'){
            $this->alert('warning', 'Approve Return Slip? Note that after confirmation, return quantity will reflect on product inventory and actions shall be disabled in this return slip.', [
                'position' => 'center',
                'showConfirmButton' => true,
                'confirmButtonText' => 'Confirm',
                'onConfirmed' => 'approve_returns',
                'allowOutsideClick' => false,
                'timer' => null,
                'toast' => false
            ]);
        }else{
            $this->alert('warning', 'Reject Return Slip? Note that after confirmation, actions shall be disabled in this return slip.', [
                'position' => 'center',
                'showConfirmButton' => true,
                'confirmButtonText' => 'Confirm',
                'onConfirmed' => 'reject_returns',
                'allowOutsideClick' => false,
                'timer' => null,
                'toast' => false
            ]);
        }
    }

    public function reject_returns()
    {
        $this->rsn->status = 'Rejected';
        $this->rsn->save();

        $this->alert('success', 'RSN-' . $this->rsn->id . ' rejected!');
    }

    public function approve_returns()
    {
        Artisan::call('inventory:begin');

        $items = $this->rsn->return_items()->get();
        foreach ($items as $return) {
            $update_item = [];
            switch ($return->item_type) {
                case 'item':
                    $update_item = OrderItem::find($return->item_id);
                    break;
                case 'gift':

                    $update_item = OrderGift::find($return->item_id);
                    break;
            }

            $product = Product::find($return->product_id);
            $product->product_qty += $return->qty;
            $update_item->returned += $return->qty;
            $update_item->released -= $return->qty;
            $update_item->item_total = $update_item->released * $update_item->item_price;

            $product->save();
            $update_item->save();
        }

        $this->rsn->status = 'Approved';
        $this->rsn->save();

        $this->alert('success', 'RSN-' . $this->rsn->id . ' approved! Product inventory updated.');
    }

    public function print_this()
    {
        $this->print_val = true;
        $this->dispatchBrowserEvent('print_div');
    }
}
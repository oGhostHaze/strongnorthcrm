<?php

namespace App\Http\Livewire\Merchandise;

use App\Http\Controllers\InventoryController;
use App\Models\MerchandiseDelivery;
use App\Models\MerchandiseDeliveryItem;
use App\Models\MerchandiseItem;
use App\Models\MerchandiseOrderHeader;
use App\Models\MerchandiseOrderItem;
use App\Models\MerchandiseOrderReturn;
use App\Models\MerchandiseOrderReturnInfo;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class MerchOrdersView extends Component
{
    use LivewireAlert;

    protected $listeners = ['return_item', 'remove_item'];
    public $merch, $client, $address, $contact, $consultant, $assoc, $presenter, $tb, $distributor;
    public $item_id, $item_qty, $item_remarks;
    public $code, $return_product, $return_price, $return_id, $return_qty;

    public function render()
    {
        $items = MerchandiseItem::all();
        return view('livewire.merchandise.merch-orders-view', compact('items'));
    }

    public function mount(MerchandiseOrderHeader $merch)
    {
        $this->merch = $merch;
        $this->client = $merch->client;
        $this->address = $merch->address;
        $this->contact = $merch->contact;
        $this->consultant = $merch->consultant;
        $this->assoc = $merch->associate;
        $this->presenter = $merch->presenter;
        $this->tb = $merch->team_builder;
        $this->distributor = $merch->distributor;
    }

    public function add_item()
    {
        $this->validate([
            'item_id' => 'required|numeric',
            'item_qty' => 'required|min:1',
            'item_remarks' => 'nullable',
        ]);

        $product = MerchandiseItem::find($this->item_id);

        MerchandiseOrderItem::create([
            'transno' => $this->merch->transno,
            'product_id' => $this->item_id,
            'item_price' => $product->price,
            'item_qty_ordered' => $this->item_qty,
            'item_total' => $this->item_qty * $product->price,
            'remarks' => $this->item_remarks,
        ]);

        $this->dispatchBrowserEvent('item_added');
        $this->alert('success', $this->item_qty . ' ' . $product->item . ' added to merchandise order.');

        $this->reset(['item_id', 'item_qty', 'item_remarks']);
    }

    public function remove_item($remove_id)
    {
        if (MerchandiseOrderItem::destroy($remove_id)) {
            $this->alert('success', 'Item removed from merchandise orders.');
        } else {
            $this->alert('error', 'Something went wrong');
        }
    }

    public function new_dr()
    {
        $this->validate(['code' => 'required']);
        $pending_dr = MerchandiseDelivery::where('mo_no', $this->merch->transno)->where('print_count', '0')->count();
        if (!$pending_dr) {
            $year = date('Y');
            $month = date('m');
            $day = date('d');
            $date = $year . $month . $day;
            $increment = (int)MerchandiseDelivery::all()->count() + 1;
            $transno = "MDR" . $date . "-" . $increment;
            $merch_id   = $this->merch->transno;

            $new_dr = MerchandiseDelivery::create([
                'date' => date('Y-m-d'),
                'transno' => $transno,
                'client' => $this->client,
                'address' => $this->address,
                'contact' => $this->contact,
                'consultant' => $this->consultant,
                'associate' => $this->assoc,
                'presenter' => $this->presenter,
                'team_builder' => $this->tb,
                'distributor' => $this->distributor,
                'code' => $this->code,
                'dr_count' => $increment,
                'mo_no' => $merch_id,
            ]);

            $merch_items = $this->merch->items()->where('item_qty_released', '0')->where('item_qty_returned', '0')->get();

            foreach ($merch_items as $merch_item) {
                $item_total = $merch_item->item_total;
                $status     = $merch_item->status;
                $status = "To Follow";
                $remaining_items = $merch_item->item_qty_ordered - $merch_item->item_qty_released - $merch_item->item_qty_returned;
                if ($remaining_items != 0) {
                    MerchandiseDeliveryItem::create([
                        'transno' => $transno,
                        'product_id' => $merch_item->product_id,
                        'item_price' => $merch_item->item_price,
                        'item_qty' => $remaining_items,
                        'item_total' => $item_total,
                        'status' => $status,
                        'type' => $merch_item->type ?? null
                    ]);
                }
            }

            $this->view_dr((string)$transno);
        } else {
            $this->alert('error', 'Please finalize pending DR for this order first before creating a new one.', ['toast' => false]);
        }
    }

    public function return_item()
    {
        $item_id = $this->return_id;

        $order_item =  MerchandiseOrderItem::find($item_id);
        $this->validate(['return_qty' => 'integer|max:' . $order_item->item_qty_released]);
        $return_qty = $this->return_qty;
        $return_id = $this->return_product;
        $oa_id = $this->merch->id;
        $receiver = auth()->user()->username;
        $inc = 0;


        $get_inc2 = MerchandiseOrderReturn::where('oa_id', $oa_id)->first();
        if (!$get_inc2) {
            $inc = MerchandiseOrderReturn::select('return_no')->orderByDesc('return_no')->first()->return_no ?? 0;
            $inc += 1;
        } else {
            $inc = $get_inc2->return_no;
        }
        $product = MerchandiseItem::find($return_id);
        $product->qty += $return_qty;
        $product->save();

        $order_item->item_qty_returned += $return_qty;
        $order_item->item_qty_released -= $return_qty;
        $order_item->item_total = $order_item->item_qty_released * $order_item->item_price;
        $order_item->save();

        MerchandiseOrderReturn::create([
            'product_id' => $return_id,
            'qty' => $return_qty,
            'received_by' => $receiver,
            'oa_id' => $oa_id,
            'item_type' => 'Item',
            'return_no' => $inc
        ]);

        InventoryController::record_merchandise_inventory('return', $return_qty, $return_id);

        $this->dispatchBrowserEvent('success', $return_qty . ' ' . $product->product_description . ' returned successfully');
    }

    public function view_dr($transno)
    {
        return redirect()->route('merch.delivery.view', ['transno' => $transno]);
    }

    public function new_rsn()
    {
        $rsn = MerchandiseOrderReturnInfo::create([
            'oa_id' => $this->merch->id,
            'oa_no' => $this->merch->transno,
            'received_by' => Auth::user()->id,
            'status' => 'For Approval',
        ]);

        $this->view_rsn($rsn->id);
    }

    public function view_rsn($rsn)
    {
        return redirect()->route('merch.rsn.view', ['rsn' => $rsn]);
    }
}

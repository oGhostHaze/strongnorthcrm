<?php

namespace App\Http\Livewire\Orders;

use App\Models\Delivery;
use App\Models\DeliveryGift;
use App\Models\DeliveryItem;
use App\Models\InventoryDate;
use App\Models\InventoryItem;
use App\Models\ModeOfPayment;
use App\Models\Order;
use App\Models\OrderAgreementPaymentHistory;
use App\Models\OrderGift;
use App\Models\OrderItem;
use App\Models\OrderPaymentHistory;
use App\Models\OrderReturn;
use App\Models\OrderReturnInfo;
use App\Models\Product;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class AgreementView extends Component
{
    use LivewireAlert;

    protected $listeners = ['return_item', 'return_gift', 'remove_item', 'remove_gift', 'new_payment', 'clear_payment'];

    public $oa;
    public $item_id, $item_qty, $item_remarks;
    public $gift_id, $gift_qty, $gift_type;
    public $return_id, $return_qty, $return_price, $return_product;
    public $delivery_client, $delivery_address, $delivery_contact, $delivery_consultant, $delivery_assoc, $delivery_presenter, $delivery_tb, $delivery_distributor, $delivery_code;
    public $price_difference, $price_override;
    public $mop, $date_of_payment, $amount, $status, $payment_id, $delivery_id;
    public $remarks;

    public $rsn_dr;
    public $selected_delivery_id = null;

    public function render()
    {
        $products = Product::all();
        $payments = OrderPaymentHistory::where('oa_id', $this->oa->oa_id)->latest('date_of_payment')->get();
        $initial = OrderPaymentHistory::where('oa_id', $this->oa->oa_id)->orderBy('date_of_payment')->first();
        $total_paid = OrderPaymentHistory::where('oa_id', $this->oa->oa_id)->where('status', '<>', 'Voided')->sum('amount');
        $mops = ModeOfPayment::all();

        return view('livewire.orders.agreement-view', [
            'products' => $products,
            'payments' => $payments,
            'initial' => $initial,
            'total_paid' => $total_paid,
            'mops' => $mops,
        ]);
    }

    public function mount(Order $oa)
    {
        $this->oa = $oa;
        $this->delivery_client = $oa->oa_client;
        $this->delivery_address = $oa->oa_address;
        $this->delivery_contact = $oa->oa_contact;
        $this->delivery_consultant = $oa->oa_consultant;
        $this->delivery_assoc = $oa->oa_associate;
        $this->delivery_presenter = $oa->oa_presenter;
        $this->delivery_tb = $oa->oa_team_builder;
        $this->delivery_distributor = $oa->oa_distributor;
    }

    public function add_item()
    {
        $this->validate([
            'item_id' => 'required|numeric',
            'item_qty' => 'required|min:1',
            'item_remarks' => 'nullable',
        ]);

        $product = Product::find($this->item_id);


        if ($product->tblset_id) {
            OrderItem::create([
                'oa_id' => $this->oa->oa_id,
                'product_id' => $this->item_id,
                'item_price' => $product->product_price,
                'item_qty' => $this->item_qty,
                'item_total' => $this->item_qty * $product->product_price,
                'remarks' => 'Composed of:',
            ]);

            foreach ($product->set->compositions()->get() as $row) {
                OrderItem::create([
                    'oa_id' => $this->oa->oa_id,
                    'product_id' => $row->product_id,
                    'item_price' => '0',
                    'item_qty' => $row->qty,
                    'item_total' => '0',
                    'remarks' => $product->set->set_name,
                    'tblset_id' => $row->tblsets_id,
                ]);
            }
        } else {
            OrderItem::create([
                'oa_id' => $this->oa->oa_id,
                'product_id' => $this->item_id,
                'item_price' => $product->product_price,
                'item_qty' => $this->item_qty,
                'item_total' => $this->item_qty * $product->product_price,
                'remarks' => $this->item_remarks,
            ]);
        }

        $this->dispatchBrowserEvent('item_added');
        $this->dispatchBrowserEvent('success', $this->item_qty . ' ' . $product->product_description . ' added to order as item.');

        $this->item_id = null;
        $this->item_qty = null;
        $this->item_remarks = null;
    }

    public function add_gift()
    {
        $this->validate([
            'gift_id' => 'required|numeric',
            'gift_qty' => 'required|min:1',
            'gift_type' => 'nullable',
        ]);

        $product = Product::find($this->gift_id);

        OrderGift::create([
            'oa_id' => $this->oa->oa_id,
            'product_id' => $this->gift_id,
            'item_price' => $product->product_price,
            'item_qty' => $this->gift_qty,
            'item_total' => $this->gift_qty * $product->product_price,
            'type' => $this->gift_type,
        ]);

        $this->dispatchBrowserEvent('gift_added');
        $this->dispatchBrowserEvent('success', $this->item_qty . ' ' . $product->product_description . ' added to order as gift.');

        $this->gift_id = null;
        $this->gift_qty = null;
        $this->gift_type = null;
    }

    public function add_payment()
    {
        $this->validate([
            'mop' => 'required|string',
            'date_of_payment' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        OrderPaymentHistory::create([
            'oa_id' => $this->oa->oa_id,
            'mop' => $this->mop,
            'date_of_payment' => $this->date_of_payment,
            'amount' => $this->amount,
        ]);

        if ($this->oa->reference_oa) {
            OrderAgreementPaymentHistory::create([
                'order_agreement_id' => $this->oa->reference_oa,
                'mop' => $this->mop,
                'date_of_payment' => $this->date_of_payment,
                'amount' => $this->amount,
            ]);
        }

        $this->reset('mop', 'date_of_payment', 'amount');

        $this->alert('success', 'Payment Added!');
    }

    public function return_item()
    {
        Artisan::call('inventory:begin');
        $item_id = $this->return_id;

        $order_item =  OrderItem::find($item_id);
        $this->validate(['return_qty' => 'integer|max:' . $order_item->released]);
        $return_qty = $this->return_qty;
        $return_id = $this->return_product;
        $oa_id = $this->oa->oa_id;
        $receiver = auth()->user()->username;
        $inc = 0;


        $get_inc2 = OrderReturn::where('oa_id', $oa_id)->first();
        if (!$get_inc2) {
            $inc = OrderReturn::select('return_no')->orderByDesc('return_no')->first()->return_no + 1;
        } else {
            $inc = $get_inc2->return_no;
        }
        $product = Product::find($return_id);
        $product->product_qty += $return_qty;
        $product->save();

        $order_item->returned += $return_qty;
        $order_item->released -= $return_qty;
        $order_item->item_total = $order_item->released * $order_item->item_price;
        $order_item->save();

        OrderReturn::create([
            'product_id' => $return_id,
            'qty' => $return_qty,
            'received_by' => $receiver,
            'oa_id' => $oa_id,
            'item_type' => 'Item',
            'return_no' => $inc
        ]);

        $inventory_date = InventoryDate::firstOrCreate(['inv_date' => date('Y-m-d')]);
        $inventory_item = InventoryItem::firstOrCreate([
            'inventory_date_id' => $inventory_date->id,
            'product_id' => $return_id
        ]);
        $inventory_item->total_returned += $return_qty;
        $inventory_item->save();

        $this->dispatchBrowserEvent('success', $return_qty . ' ' . $product->product_description . ' returned successfully');
    }


    public function return_gift()
    {
        Artisan::call('inventory:begin');
        $gift_id = $this->return_id;

        $order_gift =  OrderGift::find($gift_id);
        $this->validate(['return_qty' => 'integer|max:' . $order_gift->released]);
        $return_qty = $this->return_qty;
        $return_id = $this->return_product;
        $oa_id = $this->oa->oa_id;
        $receiver = auth()->user()->username;
        $inc = 0;


        $get_inc2 = OrderReturn::where('oa_id', $oa_id)->first();
        if (!$get_inc2) {
            $inc = OrderReturn::select('return_no')->orderByDesc('return_no')->first()->return_no + 1;
        } else {
            $inc = $get_inc2->return_no;
        }
        $product = Product::find($return_id);
        $product->product_qty += $return_qty;
        $product->save();

        $order_gift->returned += $return_qty;
        $order_gift->released -= $return_qty;
        $order_gift->item_total = $order_gift->released * $order_gift->item_price;
        $order_gift->save();

        OrderReturn::create([
            'product_id' => $return_id,
            'qty' => $return_qty,
            'received_by' => $receiver,
            'oa_id' => $oa_id,
            'item_type' => 'Gift',
            'return_no' => $inc
        ]);

        $inventory_date = InventoryDate::firstOrCreate(['inv_date' => date('Y-m-d')]);
        $inventory_item = InventoryItem::firstOrCreate([
            'inventory_date_id' => $inventory_date->id,
            'product_id' => $return_id
        ]);
        $inventory_item->total_returned += $return_qty;
        $inventory_item->save();

        $this->dispatchBrowserEvent('success', $return_qty . ' ' . $product->product_description . ' returned successfully');
    }

    public function remove_item($remove_id)
    {
        $item = OrderItem::find($remove_id);
        if ($item->item->tblset_id) {
            $order_items = OrderItem::where('oa_id', $item->oa_id)->where('tblset_id', $item->item->tblset_id)->get();
            foreach ($order_items as $row) {
                OrderItem::destroy($row->item_id);
            }
        }

        if (OrderItem::destroy($remove_id)) {
            $this->dispatchBrowserEvent('success', 'Item removed from orders.');
        } else {
            $this->dispatchBrowserEvent('error', 'Something went wrong');
        }
    }

    public function remove_gift($remove_id)
    {
        if (OrderGift::destroy($remove_id)) {
            $this->dispatchBrowserEvent('success', 'Gift removed from orders.');
        } else {
            $this->dispatchBrowserEvent('error', 'Something went wrong');
        }
    }

    /**
     * Clear payments associated with an order
     */
    public function clear_payment()
    {
        try {
            DB::beginTransaction();

            // Get all payments for this order
            $payments = OrderPaymentHistory::where('oa_id', $this->oa->oa_id)->get();

            // Clear the payments or mark them as processed
            foreach ($payments as $payment) {
                // Option 1: Delete the payments
                // $payment->delete();

                // Option 2: Update status (better for audit trails)
                $payment->status = 'Processed';
                $payment->save();
            }

            DB::commit();
            $this->alert('success', 'Payments cleared successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', 'Failed to clear payments: ' . $e->getMessage());
        }
    }

    public function new_dr()
    {
        $this->validate(['delivery_code' => 'required']);

        try {
            DB::beginTransaction();

            // First handle the payments - mark them as processed or move them
            $this->clear_payment();

            // Now create the delivery
            $year = date('Y');
            $month = date('m');
            $day = date('d');
            $date = $year . $month . $day;
            $increment = (int)Delivery::count() + 1;
            $transno = "SGH" . $date . "-" . $increment;
            $oa_id = $this->oa->oa_id;
            $existing_dr = $this->oa->drs()->count();

            $new_dr = Delivery::create([
                'date' => date('Y-m-d'),
                'transno' => $transno,
                'client' => $this->delivery_client,
                'address' => $this->delivery_address,
                'contact' => $this->delivery_contact,
                'consultant' => $this->delivery_consultant,
                'associate' => $this->delivery_assoc,
                'presenter' => $this->delivery_presenter,
                'team_builder' => $this->delivery_tb,
                'distributor' => $this->delivery_distributor,
                'code' => $this->delivery_code,
                'dr_count' => $increment,
                'oa_no' => $oa_id,
                'price_diff' => $existing_dr ? 0 : $this->oa->oa_price_diff,
                'price_override' => $existing_dr ? 0 : $this->oa->oa_price_override,
            ]);

            // Associate the payments with the new delivery
            $payments = OrderPaymentHistory::where('oa_id', $this->oa->oa_id)
                ->where('status', 'Processed')
                ->where('delivery_id', null)
                ->get();

            foreach ($payments as $payment) {
                $payment->delivery_id = $new_dr->info_id;
                $payment->save();
            }

            $oa_items = $this->oa->items()->where('item_qty', '>', '0')->get();
            foreach ($oa_items as $oa_item) {
                $item_total = $oa_item->item_total;
                $status = $oa_item->status;
                if ($status == "To Follow") {
                    $item_total = '0';
                }
                $status = "To Follow";
                DeliveryItem::create([
                    'transno' => $transno,
                    'product_id' => $oa_item->product_id,
                    'item_price' => $oa_item->item_price,
                    'item_qty' => $oa_item->item_qty,
                    'item_total' => $existing_dr ? 0 : $item_total,
                    'tblset_id' => $oa_item->tblset_id,
                    'status' => $status
                ]);
            }

            $oa_gifts = $this->oa->gifts()->where('item_qty', '>', '0')->get();
            foreach ($oa_gifts as $oa_gift) {
                DeliveryGift::create([
                    'transno' => $transno,
                    'product_id' => $oa_gift->product_id,
                    'item_price' => $oa_gift->item_price,
                    'item_qty' => $oa_gift->item_qty,
                    'item_total' => 0,
                    'status' => "To Follow",
                    'type' => $oa_gift->type
                ]);
            }

            DB::commit();
            $this->view_dr($transno);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', 'Failed to create delivery: ' . $e->getMessage());
        }
    }

    public function view_dr($transno)
    {
        return redirect()->route('order.delivery.view', ['transno' => $transno]);
    }

    public function view_rsn($rsn)
    {
        return redirect()->route('order.returns.view', ['rsn' => $rsn]);
    }

    public function new_rsn()
    {
        $this->validate(['rsn_dr' => ['required', 'string', 'min:10']]);
        $rsn = OrderReturnInfo::create([
            'oa_id' => $this->oa->oa_id,
            'oa_no' => $this->oa->oa_number,
            'dr_no' => $this->rsn_dr,
            'received_by' => Auth::user()->id,
            'status' => 'For Approval',
        ]);

        $this->view_rsn($rsn->id);
    }

    public function override_price()
    {
        $this->validate(['price_override' => 'required']);
        $this->oa->oa_price_override = $this->price_override;
        $this->oa->save();
        $items = $this->oa->items()->get();
        foreach ($items as $item) {
            $item->item_total = 0;
            $item->save();
        }
        $this->dispatchBrowserEvent('success', 'Total price successfully overriden for ORDER #' . $this->oa->oa_number);
    }

    public function add_pricediff()
    {
        $this->validate(['price_difference' => 'required']);
        $this->oa->oa_price_diff = $this->price_difference;
        $this->oa->save();
        $this->dispatchBrowserEvent('success', 'Updated price difference for ORDER #' . $this->oa->oa_number);
    }

    public function cancel_override()
    {
        $this->oa->oa_price_override = null;
        $this->oa->save();
        $items = $this->oa->items()->get();
        foreach ($items as $item) {
            $item->item_total = (float)$item->item_qty * (float)$item->item_price;
            $item->save();
        }
        $this->dispatchBrowserEvent('success', 'Total price successfully overriden for ORDER #' . $this->oa->oa_number);
    }

    public function update_payment()
    {
        $this->validate([
            'payment_id' => 'required',
            'status' => 'required',
            'delivery_id' => 'nullable', // Can be null for payments without a linked delivery
        ]);

        $payment = OrderPaymentHistory::find($this->payment_id);
        $payment->status = $this->status;
        $payment->remarks = $this->remarks;
        $payment->delivery_id = $this->delivery_id ?: null; // Use null if empty string
        $payment->save();

        $this->reset('status', 'payment_id', 'remarks', 'delivery_id');

        $this->alert('success', 'Payment Updated!');
    }

    /**
     * Proceed to the payments page with selected delivery
     */
    public function proceedToPayments()
    {
        // Validate the selection
        $this->validate([
            'selected_delivery_id' => 'nullable', // Can be null for payments without delivery
        ]);

        // Redirect to the batch payments page with the selected delivery
        if ($this->selected_delivery_id) {
            return redirect()->route('order.agreements.batch-add-payments', [
                'oa_id' => $this->oa->oa_id,
                'delivery_id' => $this->selected_delivery_id
            ]);
        } else {
            return redirect()->route('order.agreements.batch-add-payments', [
                'oa_id' => $this->oa->oa_id
            ]);
        }
    }
}

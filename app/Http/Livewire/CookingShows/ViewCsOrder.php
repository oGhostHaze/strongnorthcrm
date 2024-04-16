<?php

namespace App\Http\Livewire\CookingShows;

use App\Models\Order;
use App\Models\OrderAgreement;
use App\Models\OrderAgreementGift;
use App\Models\OrderAgreementItem;
use App\Models\OrderGift;
use App\Models\OrderItem;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ViewCsOrder extends Component
{
    use LivewireAlert;
    protected $listeners = ['return_item', 'return_gift', 'delete_item', 'submit_order', 'cancel_order', 'remove_item'];

    public $oa;
    public $item_id, $item_qty = 1, $item_remarks;
    public $gift_id, $gift_qty = 1, $gift_type;
    public $initial_investment, $delivery_date, $delivery_time, $current_level, $terms;
    public $price_override, $price_difference;

    public function render()
    {
        $products = Product::all();
        return view('livewire.cooking-shows.view-cs-order', [
            'products' => $products,
        ]);
    }

    public function mount($oa_id)
    {
        $this->oa = OrderAgreement::find($oa_id);
    }

    public function override_price()
    {
        $this->validate(['price_override' => 'required']);
        $this->oa->price_override = $this->price_override;
        $this->oa->save();
        $items = $this->oa->items()->get();
        foreach ($items as $item) {
            $item->item_total = 0;
            $item->save();
        }
        $this->alert('success', 'Total price successfully overriden');
    }

    public function add_pricediff()
    {
        $this->validate(['price_difference' => 'required']);
        $this->oa->price_diff = $this->price_difference;
        $this->oa->save();
        $this->alert('success', 'Updated price difference');
    }

    public function cancel_override()
    {
        $this->oa->price_override = null;
        $this->oa->save();
        $items = $this->oa->items()->get();
        foreach ($items as $item) {
            $item->item_total = (float)$item->item_qty * (float)$item->item_price;
            $item->save();
        }
        $this->alert('success', 'Total price successfully overriden');
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
            OrderAgreementItem::create([
                'order_agreement_id' => $this->oa->id,
                'product_id' => $this->item_id,
                'item_price' => $product->product_price,
                'item_qty' => $this->item_qty,
                'item_total' => $this->item_qty * $product->product_price,
                'remarks' => 'Composed of:',
            ]);

            foreach ($product->set->compositions()->get() as $row) {
                OrderAgreementItem::create([
                    'order_agreement_id' => $this->oa->id,
                    'product_id' => $row->product_id,
                    'item_price' => '0',
                    'item_qty' => $this->item_qty,
                    'item_total' => '0',
                    'remarks' => $product->set->set_name,
                    'tblset_id' => $row->tblsets_id,
                ]);
            }
        } else {
            OrderAgreementItem::create([
                'order_agreement_id' => $this->oa->id,
                'product_id' => $this->item_id,
                'item_price' => $product->product_price,
                'item_qty' => $this->item_qty,
                'item_total' => $this->item_qty * $product->product_price,
                'remarks' => $this->item_remarks,
            ]);
        }

        $this->resetExcept('oa');
        $this->alert('success', $this->item_qty . ' ' . $product->product_description . ' added to order as item.');
    }

    public function add_gift()
    {
        $this->validate([
            'gift_id' => 'required|numeric',
            'gift_qty' => 'required|min:1',
            'gift_type' => 'nullable',
        ]);

        $product = Product::find($this->gift_id);

        OrderAgreementGift::create([
            'order_agreement_id' => $this->oa->id,
            'product_id' => $this->gift_id,
            'item_price' => $product->product_price,
            'item_qty' => $this->gift_qty,
            'item_total' => $this->gift_qty * $product->product_price,
            'type' => $this->gift_type,
        ]);

        $this->resetExcept('oa');
        $this->alert('success', $this->item_qty . ' ' . $product->product_description . ' added to order as gift.');
    }

    public function remove_item($item_id, $type)
    {
        switch ($type) {
            case "item":
                $delete = OrderAgreementItem::find($item_id);
                break;

            case "gift":
                $delete = OrderAgreementGift::find($item_id);
                break;
        }
        $delete->delete();
        $this->alert('success', $type . ' removed successfully!');
    }

    public function update_details()
    {
        $this->oa->current_level = $this->current_level;
        $this->oa->delivery_date = $this->delivery_date;
        $this->oa->delivery_time = $this->delivery_time;
        $this->oa->initial_investment = $this->initial_investment;
        $this->oa->terms = $this->terms;
        $this->oa->save();

        $this->resetExcept('oa');
        $this->alert('success', 'Updated added details.');
    }

    public function upload_sig()
    {
        $folderPath = public_path('upload/');

        $image_parts = explode(";base64,", $this->signed);

        $image_type_aux = explode("image/", $image_parts[0]);

        $image_type = $image_type_aux[1];

        $image_base64 = base64_decode($image_parts[1]);

        $file = $folderPath . uniqid() . '.' . $image_type;
        file_put_contents($file, $image_base64);

        return $this->alert('success', 'success Full upload signature');
    }

    public function submit_order()
    {
        $this->oa->status = 'Waiting Confirmation';
        $this->oa->submitted = true;
        $this->oa->save();

        $this->alert('success', 'Congratulation! Your order has been sent to Saladmaster. Happy healthy cooking!', [
            'toast' => false,
            'position' => 'center',
            'timer' => false,
        ]);
    }

    public function cancel_order()
    {
        $this->oa->status = 'Cancelled';
        $this->oa->save();

        $this->resetExcept('oa');
        $this->alert('success', 'Order Cancelled.');
    }

    public function approve()
    {
        $date = date('Ymj');
        $oa_count = Order::max('oa_count') + 1;
        $oa_number = "OA" . $date . "-" . $oa_count;

        $this->oa->status = 'Approved';
        $this->oa->save();
        return;
        $new_order = Order::create([
            'oa_number' => $oa_number,
            'oa_count' => $oa_count,
            'oa_date' => $this->oa->date,
            'oa_client' => $this->oa->client,
            'oa_address' => $this->oa->address,
            'oa_contact' => $this->oa->contact,
            'oa_consultant' => $this->oa->consultant,
            'oa_associate' => $this->oa->associate,
            'oa_presenter' => $this->oa->presenter,
            'oa_team_builder' => $this->oa->team_builder,
            'oa_distributor' => $this->oa->distributor,
            'reference_oa' => $this->oa->id,
            'oa_price_diff' => $this->oa->id->price_diff,
            'oa_price_override' => $this->oa->id->price_override,
        ]);

        foreach ($this->oa->items->all() as $item) {
            $product = Product::find($item->product_id);
            if ($product->tblset_id) {
                OrderItem::create([
                    'oa_id' => $new_order->oa_id,
                    'product_id' => $item->product_id,
                    'item_price' => $item->item_price,
                    'item_qty' => $item->item_qty,
                    'item_total' => $item->item_total,
                    'remarks' => 'Composed of:',
                ]);

                foreach ($product->set->compositions()->get() as $row) {
                    OrderItem::create([
                        'oa_id' => $new_order->oa_id,
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
                    'oa_id' => $new_order->oa_id,
                    'product_id' => $item->product_id,
                    'item_price' => $item->item_price,
                    'item_qty' => $item->item_qty,
                    'item_total' => $item->item_qty * $item->item_price,
                    'remarks' => $item->remarks,
                ]);
            }
        }

        foreach ($this->oa->items->all() as $gift) {
            $product = Product::find($item->product_id);
            if ($product->tblset_id) {
                OrderGift::create([
                    'oa_id' => $new_order->oa_id,
                    'product_id' => $gift->product_id,
                    'item_price' => $gift->item_price,
                    'item_qty' => $gift->item_qty,
                    'item_total' => $gift->item_total,
                    'remarks' => 'Composed of:',
                    'type' => $gift->gift_type,
                ]);

                foreach ($product->set->compositions()->get() as $row) {
                    OrderGift::create([
                        'oa_id' => $new_order->oa_id,
                        'product_id' => $row->product_id,
                        'item_price' => '0',
                        'item_qty' => $row->qty,
                        'item_total' => '0',
                        'remarks' => $product->set->set_name,
                        'tblset_id' => $row->tblsets_id,
                    ]);
                }
            } else {
                OrderGift::create([
                    'oa_id' => $new_order->oa_id,
                    'product_id' => $gift->product_id,
                    'item_price' => $gift->item_price,
                    'item_qty' => $gift->item_qty,
                    'item_total' => $gift->item_qty * $gift->item_price,
                    'remarks' => $gift->remarks,
                    'type' => $gift->type,
                ]);
            }
        }
        $this->alert('success', 'Created new Order Agreement #' . $oa_number);
    }
}

<?php

namespace App\Http\Livewire\Supplies;

use App\Http\Controllers\InventoryController;
use Livewire\Component;
use App\Models\SupplyItem;
use Livewire\WithPagination;
use App\Models\SupplyCategory;
use App\Models\MerchandiseItem;
use App\Models\SupplyItemDisposed;
use App\Models\SupplyLocation;
use App\Models\UnitOfMeasurement;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SupplyItemList extends Component
{
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['update_price', 'dispose'];
    public $item_name, $unit_price = 0, $qty = 0, $date_purchased, $location, $category, $search, $unit, $reason, $max;


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $supplies = SupplyItem::where('item_name', 'LIKE', '%'.$this->search.'%')->paginate(20);
        $categories = SupplyCategory::all();
        $locations = SupplyLocation::all();
        $measurements = UnitOfMeasurement::all();

        return view('livewire.supplies.supply-item-list', [
            'supplies' => $supplies,
            'locations' => $locations,
            'measurements' => $measurements,
            'categories' => $categories,
        ]);
    }

    public function mount()
    {
        $this->date_purchased = date('Y-m-d');
    }

    public function save()
    {
        $validated = $this->validate([
            'date_purchased' => 'required|date',
            'location' => 'string|nullable',
            'category' => 'required|string',
            'item_name'=>'required|unique:supply_items,item_name',
            'unit' => 'string|nullable',
            'qty' => 'required|integer',
            'unit_price' => 'required|integer|min:0',
        ]);
        $added = SupplyItem::create($validated);
        if($added){
            InventoryController::record_supply_inventory('add', $this->qty, $added->id);
            $this->alert('success', 'New office supply saved!');
            $this->reset();
        }else{
            $this->alert('error', 'Oops! Something went wrong.');
        }
    }

    public function update_price($id)
    {
        $supp = SupplyItem::find($id);
        $supp->unit_price = $this->unit_price;
        $supp->save();
        $this->reset();
        $this->alert('success', 'Office supply price for '.$supp->item.' updated.');
    }

    public function dispose($id)
    {
        $supply = SupplyItem::find($id);

        if($this->max < $this->qty){
            $this->alert('error', $supply->item_name.' has only '.$this->max.' in current qty. Dispose qty must be lower or equal to current available qty.');
        }
        $validated = $this->validate([
            'qty' => 'required|integer|max:'.$this->max.'|min:1',
            'reason' => 'string|nullable',
        ]);

        $supply->qty -= $this->qty;
        $supply->save();

        SupplyItemDisposed::create([
            'item_id' => $id,
            'qty' => $this->qty,
            'reason' => $this->reason,
        ]);

        InventoryController::record_supply_inventory('dispose', $this->qty, $id);

        $this->reset();
        $this->alert('success', $this->qty.' Office supply '.$supply->item_name.' disposed.');
    }
}

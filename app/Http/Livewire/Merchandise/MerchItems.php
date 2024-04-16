<?php

namespace App\Http\Livewire\Merchandise;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MerchandiseItem;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class MerchItems extends Component
{
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['update_price'];
    public $item, $price = 0, $search;


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $merchs = MerchandiseItem::where('item', 'LIKE', '%'.$this->search.'%')->paginate(20);

        return view('livewire.merchandise.merch-items', compact('merchs'));
    }

    public function save()
    {
        $validated = $this->validate(['item'=>'required|unique:merchandise_items,item', 'price' => 'required|integer|min:0']);
        if(MerchandiseItem::create($validated)){
            $this->alert('success', 'New merchandise item saved!');
            $this->reset();
        }else{
            $this->alert('error', 'Oops! Something went wrong.');
        }
    }

    public function update_price($id)
    {
        $merch = MerchandiseItem::find($id);
        $merch->price = $this->price;
        $merch->save();
        $this->reset();
        $this->dispatchBrowserEvent('success', 'Merchandise price for '.$merch->item.' updated.');
    }
}

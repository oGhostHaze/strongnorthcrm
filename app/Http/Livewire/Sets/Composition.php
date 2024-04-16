<?php

namespace App\Http\Livewire\Sets;

use App\Models\Product;
use App\Models\Set;
use App\Models\SetComposition;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Composition extends Component
{
    use LivewireAlert;

    public $set, $product_id, $qty = 1;

    public function render()
    {
        $products = Product::all();

        return view('livewire.sets.composition', compact(
            'products'
        ));
    }

    public function mount(Set $set)
    {
        $this->set = $set;
    }

    public function add_item()
    {
        $this->validate(['product_id'=>'required']);
        $compo = SetComposition::firstOrCreate(['product_id' => $this->product_id, 'tblsets_id' => $this->set->set_id]);
        $compo->qty = $this->qty;
        $compo->save();
        $this->alert('success', 'Set composition updated successfully!');
    }
}

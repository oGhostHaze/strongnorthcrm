<?php

namespace App\Http\Livewire\Products;

use App\Models\Product;
use App\Models\Stockin;
use Carbon\Carbon;
use Livewire\Component;

class StockinReportFiltered extends Component
{
    public $from, $to, $remarks, $product_id;

    public function render()
    {
        // $data = Stockin::whereRelation('product', 'product_description', 'LIKE', '%' . $this->search . '%')->orderByDesc('stockIn_id')->get();
        $from = Carbon::parse($this->from)->startOfDay();
        $to = Carbon::parse($this->to)->endOfDay();
        $data = Stockin::whereBetween('date', [$from, $to]);
        $marks = Stockin::select('remarks');

        if ($this->remarks) {
            $data->where('remarks', 'LIKE', $this->remarks . '%');
        }
        if ($this->product_id) {
            $data->where('product_id', $this->product_id);
            $marks->where('product_id', $this->product_id);
        }
        if ($this->from and $this->to) {
            $marks->whereBetween('date', [$from, $to]);
        }


        return view('livewire.products.stockin-report-filtered', [
            'data' => $data->orderByDesc('stockIn_id')->get(),
            'products' => Product::all(),
            'marks' => $marks->distinct('remarks')->get()
        ]);
    }
}

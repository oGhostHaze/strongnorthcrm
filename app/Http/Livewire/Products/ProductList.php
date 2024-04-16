<?php

namespace App\Http\Livewire\Products;

use App\Mail\ReorderLevelReportMail;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Mail;

class ProductList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['update_price', 'update_reorder'];
    public $search, $product_price, $code, $product_description, $category_id, $spv, $reorder_level;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = Product::where('product_description', 'LIKE', '%' . $this->search . '%')->paginate(20);
        return view('livewire.products.product-list', [
            'data' => $data,
            'categories' => Category::all(),
        ]);
    }

    public function send_report()
    {
        Mail::to('joshua070915@gmail.com')->send(new ReorderLevelReportMail());
    }

    public function update_price($product_id)
    {
        $product = Product::find($product_id);
        $product->product_price = $this->product_price;
        $product->save();
        $this->reset_data();
        $this->dispatchBrowserEvent('success', 'Product price for ' . $product->product_description . ' updated.');
    }

    public function update_reorder($product_id)
    {
        $product = Product::find($product_id);
        $product->reorder_level = $this->reorder_level;
        $product->save();
        $this->reset_data();
        $this->dispatchBrowserEvent('success', 'Reorder level for ' . $product->product_description . ' updated.');
    }

    public function save()
    {
        $validated_data = $this->validate([
            'code' => ['required', 'unique:tblproducts,code'],
            'product_description' => ['required', 'unique:tblproducts,product_description'],
            'product_price' => ['required', 'numeric', 'min:0'],
            'category_id' => 'required',
            'spv' => 'nullable',
        ]);
        Product::create($validated_data);
        $this->reset_data();
        $this->dispatchBrowserEvent('success', 'Product saved!');
    }

    public function reset_data()
    {
        $this->product_price = null;
        $this->code = null;
        $this->product_description = null;
        $this->category_id = null;
        $this->spv = null;
    }

    public function download_csv()
    {
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',   'Content-type'        => 'text/csv',   'Content-Disposition' => 'attachment; filename=saladmaster-products' . now() . '.csv',   'Expires'             => '0',   'Pragma'              => 'public'
        ];

        $list = Product::select('product_id', 'code', 'product_description', 'product_qty')->get()->toArray();

        # add headers for each column in the CSV download
        array_unshift($list, array_keys($list[0]));

        $callback = function () use ($list) {
            $FH = fopen('php://output', 'w');
            foreach ($list as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };

        return response()->stream($callback, 200, $headers);
    }
}

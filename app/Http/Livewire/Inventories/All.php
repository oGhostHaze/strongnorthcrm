<?php

namespace App\Http\Livewire\Inventories;

use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\InventoryDate;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class All extends Component
{
    public $search, $start_date, $end_date;

    public function render()
    {
        $start_date = Carbon::parse($this->start_date)->startOfDay();
        $end_date = Carbon::parse($this->end_date)->endOfDay();

        // Get the list of products matching the search criteria
        $products = Product::where('product_description', 'like', '%' . $this->search . '%')
            ->orWhere('code', 'like', '%' . $this->search . '%')
            ->pluck('product_id');

        if (count($products) > 0) {
            // Find the earliest date ID in the range
            $earliest_date = InventoryDate::where('inv_date', '>=', $start_date)
                ->where('inv_date', '<=', $end_date)
                ->orderBy('inv_date', 'asc')
                ->first();

            if ($earliest_date) {
                // Get beginning balances from the earliest date in range
                $beginning_balances = InventoryItem::whereIn('product_id', $products)
                    ->where('inventory_date_id', $earliest_date->id)
                    ->select('product_id', 'beginning_balance')
                    ->get()
                    ->keyBy('product_id');

                // Get aggregated data for the entire date range
                $data = InventoryItem::whereIn('product_id', $products)
                    ->whereHas('inv', function ($query) use ($start_date, $end_date) {
                        $query->whereBetween('inv_date', [$start_date, $end_date]);
                    })
                    ->select(
                        'product_id',
                        DB::raw('SUM(total_delivered) as total_delivered'),
                        DB::raw('SUM(total_released) as total_released'),
                        DB::raw('SUM(total_returned) as total_returned')
                    )
                    ->groupBy('product_id')
                    ->with(['product'])
                    ->get();

                // Merge the beginning balances with the aggregated data
                $data = $data->map(function ($item) use ($beginning_balances, $start_date, $end_date) {
                    $item->beginning_balance = $beginning_balances->has($item->product_id)
                        ? $beginning_balances[$item->product_id]->beginning_balance
                        : 0;

                    $item->start_date = $start_date->format('Y-m-d');
                    $item->end_date = $end_date->format('Y-m-d');

                    // Calculate ending balance
                    $item->ending_balance = ($item->beginning_balance + $item->total_delivered + $item->total_returned) - $item->total_released;

                    return $item;
                });
            } else {
                $data = collect();
            }
        } else {
            $data = collect();
        }

        return view('livewire.inventories.all', [
            'data' => $data,
        ]);
    }

    public function mount()
    {
        $this->start_date = Carbon::now()->subWeek()->toDateString();
        $this->end_date = Carbon::now()->toDateString();
    }
}

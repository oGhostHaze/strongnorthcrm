<?php

namespace App\Http\Livewire\Reports;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderGift;
use App\Models\Product;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\DeliveryGift;
use App\Models\OrderReturn;
use App\Models\OrderReturnInfo;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ItemLifecycleReport extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $start_date;
    public $end_date;
    public $filter_type = 'all'; // all, ordered, delivered, returned
    public $product_id;
    public $client_name;

    public function mount()
    {
        $this->start_date = Carbon::now()->subMonth()->format('Y-m-d');
        $this->end_date = Carbon::now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $startDate = Carbon::parse($this->start_date)->startOfDay();
        $endDate = Carbon::parse($this->end_date)->endOfDay();

        // Get base query for order items
        $items = OrderItem::with([
            'item',
            'details',
            'details.drs',
            'details.returns'
        ])
            ->whereHas('details', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('oa_date', [$startDate, $endDate]);

                if (!empty($this->client_name)) {
                    $query->where('oa_client', 'like', '%' . $this->client_name . '%');
                }
            });

        // Apply product filter if set
        if (!empty($this->product_id)) {
            $items->where('product_id', $this->product_id);
        }

        // Apply text search to product description
        if (!empty($this->search)) {
            $items->whereHas('item', function ($query) {
                $query->where('product_description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply delivery/return status filter
        if ($this->filter_type == 'delivered') {
            $items->where('released', '>', 0);
        } elseif ($this->filter_type == 'returned') {
            $items->where('returned', '>', 0);
        } elseif ($this->filter_type == 'pending') {
            $items->whereRaw('(item_qty - (released + returned)) > 0');
        }

        // Get gifts with similar logic
        $gifts = OrderGift::with([
            'gift',
            'details',
            'details.drs',
            'details.returns'
        ])
            ->whereHas('details', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('oa_date', [$startDate, $endDate]);

                if (!empty($this->client_name)) {
                    $query->where('oa_client', 'like', '%' . $this->client_name . '%');
                }
            });

        // Apply product filter if set
        if (!empty($this->product_id)) {
            $gifts->where('product_id', $this->product_id);
        }

        // Apply text search to product description
        if (!empty($this->search)) {
            $gifts->whereHas('gift', function ($query) {
                $query->where('product_description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply delivery/return status filter
        if ($this->filter_type == 'delivered') {
            $gifts->where('released', '>', 0);
        } elseif ($this->filter_type == 'returned') {
            $gifts->where('returned', '>', 0);
        } elseif ($this->filter_type == 'pending') {
            $gifts->whereRaw('(item_qty - (released + returned)) > 0');
        }

        // Get each item's detailed lifecycle information
        $itemResults = $items->get()->map(function ($item) {
            $deliveryInfo = [];
            $returnInfo = [];

            // Find all deliveries that contain this item
            foreach ($item->details->drs as $dr) {
                $deliveryItems = DeliveryItem::where('transno', $dr->transno)
                    ->where('product_id', $item->product_id)
                    ->get();

                foreach ($deliveryItems as $deliveryItem) {
                    if ($deliveryItem->status == 'Released') {
                        $deliveryInfo[] = [
                            'transno' => $dr->transno,
                            'date' => $dr->date,
                            'qty' => $deliveryItem->item_qty,
                            'code' => $dr->code
                        ];
                    }
                }
            }

            // Find all returns for this item
            $returns = OrderReturn::where('oa_id', $item->oa_id)
                ->where('product_id', $item->product_id)
                ->where('item_type', 'Item')
                ->get();

            foreach ($returns as $return) {
                $returnSlipInfo = OrderReturnInfo::find($return->return_no);
                if ($returnSlipInfo) {
                    $returnInfo[] = [
                        'return_id' => $return->return_no, // Add the ID for the link
                        'return_no' => 'RSN-' . $return->return_no,
                        'date' => $return->date_returned,
                        'qty' => $return->qty,
                        'reason' => $return->reason
                    ];
                }
            }

            return [
                'type' => 'Item',
                'item_id' => $item->item_id,
                'product_description' => $item->item->product_description,
                'oa_id' => $item->oa_id,
                'oa_number' => $item->details->oa_number,
                'oa_date' => $item->details->oa_date,
                'client' => $item->details->oa_client,
                'ordered_qty' => $item->item_qty + $item->released + $item->returned, // Include original qty plus movement
                'released_qty' => $item->released,
                'returned_qty' => $item->returned,
                'pending_qty' => $item->item_qty, // Original item quantity from the order
                'deliveries' => $deliveryInfo,
                'returns' => $returnInfo,
                'price' => $item->item_price,
                'total' => $item->item_total
            ];
        });

        // Get gift lifecycle information with similar logic
        $giftResults = $gifts->get()->map(function ($gift) {
            $deliveryInfo = [];
            $returnInfo = [];

            // Find all deliveries for this gift
            foreach ($gift->details->drs as $dr) {
                $deliveryGifts = DeliveryGift::where('transno', $dr->transno)
                    ->where('product_id', $gift->product_id)
                    ->get();

                foreach ($deliveryGifts as $deliveryGift) {
                    if ($deliveryGift->status == 'Released') {
                        $deliveryInfo[] = [
                            'transno' => $dr->transno,
                            'date' => $dr->date,
                            'qty' => $deliveryGift->item_qty,
                            'code' => $dr->code
                        ];
                    }
                }
            }

            // Find all returns
            $returns = OrderReturn::where('oa_id', $gift->oa_id)
                ->where('product_id', $gift->product_id)
                ->where('item_type', 'Gift')
                ->get();

            foreach ($returns as $return) {
                $returnSlipInfo = OrderReturnInfo::find($return->return_no);
                if ($returnSlipInfo) {
                    $returnInfo[] = [
                        'return_id' => $return->return_no, // Add the ID for the link
                        'return_no' => 'RSN-' . $return->return_no,
                        'date' => $return->date_returned,
                        'qty' => $return->qty,
                        'reason' => $return->reason
                    ];
                }
            }

            return [
                'type' => 'Gift (' . $gift->type . ')',
                'item_id' => $gift->gift_id,
                'product_description' => $gift->gift->product_description,
                'oa_id' => $gift->oa_id,
                'oa_number' => $gift->details->oa_number,
                'oa_date' => $gift->details->oa_date,
                'client' => $gift->details->oa_client,
                'ordered_qty' => $gift->item_qty + $gift->released + $gift->returned, // Include original qty plus movement
                'released_qty' => $gift->released,
                'returned_qty' => $gift->returned,
                'pending_qty' => $gift->item_qty, // Original item quantity from the order
                'deliveries' => $deliveryInfo,
                'returns' => $returnInfo,
                'price' => $gift->item_price,
                'total' => $gift->item_total
            ];
        });

        // Combine both item and gift results
        $combinedResults = $itemResults->concat($giftResults);

        // Apply sorting
        $combinedResults = $combinedResults->sortByDesc(function ($item) {
            return Carbon::parse($item['oa_date'])->timestamp;
        });

        // Get list of products for filter dropdown
        $products = Product::orderBy('product_description')->get();

        return view('livewire.reports.item-lifecycle-report', [
            'results' => $combinedResults,
            'products' => $products,
        ]);
    }

    public function exportCsv()
    {
        // Export logic would go here
        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Report exported to CSV']);
    }

    public function exportPdf()
    {
        // Export logic would go here
        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Report exported to PDF']);
    }
}

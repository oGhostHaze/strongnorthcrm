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

        // Get base query for order items - FIXED to use DRS date consistently
        $items = OrderItem::with([
            'item',
            'details',
            'details.drs' => function ($query) use ($startDate, $endDate) {
                // Only load DRS records within the date range
                $query->whereBetween('date', [$startDate, $endDate]);

                if (!empty($this->client_name)) {
                    $query->where('client', 'like', '%' . $this->client_name . '%');
                }
            },
            'details.returns'
        ])
            ->whereHas('details', function ($query) use ($startDate, $endDate) {
                $query->whereHas('drs', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);

                    if (!empty($this->client_name)) {
                        $query->where('client', 'like', '%' . $this->client_name . '%');
                    }
                });
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

        // Get gifts with similar logic - FIXED to use DRS date consistently
        $gifts = OrderGift::with([
            'gift',
            'details',
            'details.drs' => function ($query) use ($startDate, $endDate) {
                // Only load DRS records within the date range
                $query->whereBetween('date', [$startDate, $endDate]);

                if (!empty($this->client_name)) {
                    $query->where('client', 'like', '%' . $this->client_name . '%');
                }
            },
            'details.returns'
        ])
            ->whereHas('details', function ($query) use ($startDate, $endDate) {
                $query->whereHas('drs', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);

                    if (!empty($this->client_name)) {
                        $query->where('client', 'like', '%' . $this->client_name . '%');
                    }
                });
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
        $itemResults = $items->get()->filter(function ($item) {
            // Only include items that actually have DRS records after filtering
            return $item->details && $item->details->drs && $item->details->drs->count() > 0;
        })->map(function ($item) use ($startDate, $endDate) {
            $deliveryInfo = [];
            $returnInfo = [];

            // Find all deliveries that contain this item - ONLY within date range
            foreach ($item->details->drs as $dr) {
                // Since we already filtered DRS in the with() clause, we only get DRS within date range
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

            // Find all returns for this item - OPTIONALLY filter returns by date too
            $returns = OrderReturn::where('oa_id', $item->oa_id)
                ->where('product_id', $item->product_id)
                ->where('item_type', 'Item')
                // Uncomment the next line if you also want to filter returns by date
                // ->whereBetween('date_returned', [$startDate, $endDate])
                ->get();

            foreach ($returns as $return) {
                $returnSlipInfo = OrderReturnInfo::find($return->return_no);
                if ($returnSlipInfo) {
                    $returnInfo[] = [
                        'return_id' => $return->return_no,
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
                'ordered_qty' => $item->item_qty + $item->released + $item->returned,
                'released_qty' => $item->released,
                'returned_qty' => $item->returned,
                'pending_qty' => $item->item_qty,
                'deliveries' => $deliveryInfo,
                'returns' => $returnInfo,
                'price' => $item->item_price,
                'total' => $item->item_total
            ];
        });

        // Get gift lifecycle information with similar logic
        $giftResults = $gifts->get()->filter(function ($gift) {
            // Only include gifts that actually have DRS records after filtering
            return $gift->details && $gift->details->drs && $gift->details->drs->count() > 0;
        })->map(function ($gift) use ($startDate, $endDate) {
            $deliveryInfo = [];
            $returnInfo = [];

            // Find all deliveries for this gift - ONLY within date range
            foreach ($gift->details->drs as $dr) {
                // Since we already filtered DRS in the with() clause, we only get DRS within date range
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

            // Find all returns - OPTIONALLY filter returns by date too
            $returns = OrderReturn::where('oa_id', $gift->oa_id)
                ->where('product_id', $gift->product_id)
                ->where('item_type', 'Gift')
                // Uncomment the next line if you also want to filter returns by date
                // ->whereBetween('date_returned', [$startDate, $endDate])
                ->get();

            foreach ($returns as $return) {
                $returnSlipInfo = OrderReturnInfo::find($return->return_no);
                if ($returnSlipInfo) {
                    $returnInfo[] = [
                        'return_id' => $return->return_no,
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
                'ordered_qty' => $gift->item_qty + $gift->released + $gift->returned,
                'released_qty' => $gift->released,
                'returned_qty' => $gift->returned,
                'pending_qty' => $gift->item_qty,
                'deliveries' => $deliveryInfo,
                'returns' => $returnInfo,
                'price' => $gift->item_price,
                'total' => $gift->item_total
            ];
        });

        // Combine both item and gift results
        $combinedResults = $itemResults->concat($giftResults);

        // Apply sorting - you might want to sort by DRS date instead of OA date
        $combinedResults = $combinedResults->sortByDesc(function ($item) {
            // Sort by the latest delivery date if deliveries exist, otherwise by OA date
            if (!empty($item['deliveries'])) {
                $latestDelivery = collect($item['deliveries'])->sortByDesc('date')->first();
                return Carbon::parse($latestDelivery['date'])->timestamp;
            }
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
        // Execute the render method to get the filtered data
        $renderData = $this->render();
        $results = $renderData->getData()['results']->values();

        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=item-lifecycle-report-' . now()->format('Y-m-d-H-i-s') . '.csv',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        $callback = function () use ($results) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Item Type',
                'Item Description',
                'Order Agreement',
                'Date',
                'Client',
                'Ordered',
                'Released',
                'Returned',
                'Pending',
                'Delivery Details',
                'Return Details'
            ]);

            // Add data rows
            foreach ($results as $item) {
                $deliveryInfo = '';
                if (count($item['deliveries']) > 0) {
                    $deliveries = [];
                    foreach ($item['deliveries'] as $delivery) {
                        $deliveries[] = $delivery['transno'] . ' (' . $delivery['date'] . ' Qty:' . $delivery['qty'] . ' - ' . $delivery['code'] . ')';
                    }
                    $deliveryInfo = implode('; ', $deliveries);
                }

                $returnInfo = '';
                if (count($item['returns']) > 0) {
                    $returns = [];
                    foreach ($item['returns'] as $return) {
                        $returnDetail = $return['return_no'] . ' (' . $return['date'] . ' Qty:' . $return['qty'] . ')';
                        if ($return['reason']) {
                            $returnDetail .= ' Reason: ' . $return['reason'];
                        }
                        $returns[] = $returnDetail;
                    }
                    $returnInfo = implode('; ', $returns);
                }

                fputcsv($file, [
                    $item['type'],
                    $item['product_description'],
                    $item['oa_number'],
                    $item['oa_date'],
                    $item['client'],
                    $item['ordered_qty'],
                    $item['released_qty'],
                    $item['returned_qty'],
                    $item['pending_qty'],
                    $deliveryInfo ?: 'No deliveries',
                    $returnInfo ?: 'No returns'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        // Execute the render method to get the filtered data
        $renderData = $this->render();
        $results = $renderData->getData()['results']->values();

        // You should install a PDF library like TCPDF or DOMPDF
        // Here's a basic implementation using TCPDF

        // First, make sure to require TCPDF in your composer.json
        // You'll need to install TCPDF or DOMPDF for this to work

        // For now, let's create a simple HTML view for PDF
        $html = view('livewire.reports.item-lifecycle-pdf', [
            'results' => $results,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'filter_type' => $this->filter_type,
            'client_name' => $this->client_name,
            'search' => $this->search
        ])->render();

        // If DOMPDF is installed, you can use:
        // $pdf = new Dompdf();
        // $pdf->loadHtml($html);
        // $pdf->setPaper('A4', 'landscape');
        // $pdf->render();
        // return $pdf->stream('item-lifecycle-report-' . now()->format('Y-m-d') . '.pdf');

        // For now, just show an alert that PDF export needs DOMPDF to be installed
        $this->dispatchBrowserEvent('alert', [
            'type' => 'warning',
            'message' => 'Please install DOMPDF package to enable PDF export functionality'
        ]);
    }
}

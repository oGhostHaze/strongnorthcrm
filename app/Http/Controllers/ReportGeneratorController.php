<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\InventoryItem;
use App\Models\MerchandiseDeliveryItem;
use App\Models\MerchandiseInventoryItem;
use App\Models\MerchandiseItem;
use App\Models\ModeOfPayment;
use App\Models\Order;
use App\Models\OrderPaymentHistory;
use App\Models\OrderReturn;
use App\Models\OrderReturnInfo;
use App\Models\Product;
use App\Models\Stockin;
use App\Models\SupplyInventoryItem;
use App\Models\SupplyItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportGeneratorController extends Controller
{
    /**
     * Show the report generator interface
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $reportTypes = [
            'inventory' => 'Inventory Report',
            'sales' => 'Sales Report',
            'payments' => 'Payment Report',
            'returns' => 'Returns Report',
            'stockin' => 'Stock-in Report',
            'merchandise' => 'Merchandise Report',
            'supplies' => 'Office Supplies Report',
        ];

        $categories = Category::all();
        $paymentModes = ModeOfPayment::all();

        return view('reports.generator', compact('reportTypes', 'categories', 'paymentModes'));
    }

    /**
     * Generate a report based on provided criteria
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:html,pdf,csv,excel',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $reportType = $request->input('report_type');
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        $format = $request->input('format');
        $additionalCriteria = $request->input('additional_criteria', []);

        $reportData = [];
        $reportTitle = '';

        switch ($reportType) {
            case 'inventory':
                $reportTitle = 'Inventory Report';
                $reportData = $this->generateInventoryReport($startDate, $endDate, $additionalCriteria);
                break;

            case 'sales':
                $reportTitle = 'Sales Report';
                $reportData = $this->generateSalesReport($startDate, $endDate, $additionalCriteria);
                break;

            case 'payments':
                $reportTitle = 'Payments Report';
                $reportData = $this->generatePaymentsReport($startDate, $endDate, $additionalCriteria);
                break;

            case 'returns':
                $reportTitle = 'Returns Report';
                $reportData = $this->generateReturnsReport($startDate, $endDate, $additionalCriteria);
                break;

            case 'stockin':
                $reportTitle = 'Stock-in Report';
                $reportData = $this->generateStockinReport($startDate, $endDate, $additionalCriteria);
                break;

            case 'merchandise':
                $reportTitle = 'Merchandise Report';
                $reportData = $this->generateMerchandiseReport($startDate, $endDate, $additionalCriteria);
                break;

            case 'supplies':
                $reportTitle = 'Office Supplies Report';
                $reportData = $this->generateSuppliesReport($startDate, $endDate, $additionalCriteria);
                break;
        }

        $viewData = [
            'reportTitle' => $reportTitle,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'reportData' => $reportData,
            'additionalCriteria' => $additionalCriteria,
            'reportType' => $reportType
        ];

        if ($format === 'html') {
            return view('reports.view', $viewData);
        } elseif ($format === 'pdf') {
            return $this->generatePDF($viewData);
        } elseif ($format === 'csv') {
            return $this->generateCSV($viewData);
        } elseif ($format === 'excel') {
            return $this->generateExcel($viewData);
        }

        return redirect()->back()->with('error', 'Invalid report format selected');
    }

    /**
     * Generate inventory report
     */
    private function generateInventoryReport($startDate, $endDate, $additionalCriteria)
    {
        $query = Product::query();

        // Apply category filter if provided
        if (!empty($additionalCriteria['category_id'])) {
            $query->where('category_id', $additionalCriteria['category_id']);
        }

        // Apply low stock filter if selected
        if (!empty($additionalCriteria['low_stock']) && $additionalCriteria['low_stock'] === 'yes') {
            $query->whereRaw('product_qty < reorder_level');
        }

        // Get products matching the criteria
        $products = $query->get();

        // Get inventory items in the date range
        $inventoryItems = InventoryItem::whereHas('inv', function($q) use ($startDate, $endDate) {
            $q->whereBetween('inv_date', [$startDate, $endDate]);
        })->get()->groupBy('product_id');

        $result = [];

        foreach ($products as $product) {
            $productItems = $inventoryItems->get($product->product_id, collect([]));

            $initialStock = $product->product_qty;
            $stockIn = 0;
            $stockOut = 0;
            $returns = 0;

            foreach ($productItems as $item) {
                $initialStock = $item->beginning_balance;
                $stockIn += $item->total_delivered;
                $stockOut += $item->total_released;
                $returns += $item->total_returned;
            }

            $currentStock = $initialStock + $stockIn - $stockOut + $returns;

            $result[] = [
                'product_id' => $product->product_id,
                'code' => $product->code,
                'description' => $product->product_description,
                'category' => $product->category ? $product->category->category_name : 'N/A',
                'initial_stock' => $initialStock,
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
                'returns' => $returns,
                'current_stock' => $currentStock,
                'unit_price' => $product->product_price,
                'stock_value' => $currentStock * $product->product_price,
                'reorder_level' => $product->reorder_level
            ];
        }

        return $result;
    }

    /**
     * Generate sales report
     */
    private function generateSalesReport($startDate, $endDate, $additionalCriteria)
    {
        $orderItemsQuery = DeliveryItem::whereHas('delivery', function($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        });

        if (!empty($additionalCriteria['client'])) {
            $orderItemsQuery->whereHas('delivery', function($query) use ($additionalCriteria) {
                $query->where('client', 'LIKE', '%' . $additionalCriteria['client'] . '%');
            });
        }

        if (!empty($additionalCriteria['status'])) {
            $orderItemsQuery->where('status', $additionalCriteria['status']);
        }

        if (!empty($additionalCriteria['product_id'])) {
            $orderItemsQuery->where('product_id', $additionalCriteria['product_id']);
        }


        $orderItems = $orderItemsQuery->with(['item', 'delivery'])->get();

        $result = [];

        // Group by product
        if (!empty($additionalCriteria['group_by']) && $additionalCriteria['group_by'] === 'product') {
            $productSales = $orderItems->groupBy('product_id');

            foreach ($productSales as $productId => $items) {
                $product = Product::find($productId);

                if (!$product) {
                    continue;
                }else{
                    $totalQty = $items->sum('item_qty');
                    $totalAmount = $items->sum('item_total');

                    $result[] = [
                        'product_id' => $productId,
                        'code' => $product->code,
                        'description' => $product->product_description,
                        'category' => $product->category ? $product->category->category_name : 'N/A',
                        'quantity_sold' => $totalQty,
                        'unit_price' => $product->product_price,
                        'total_amount' => $totalAmount,
                    ];
                }

            }
        }
        // Group by date
        else if (!empty($additionalCriteria['group_by']) && $additionalCriteria['group_by'] === 'date') {
            $dateSales = $orderItems->groupBy(function($item) {
                return Carbon::parse($item->delivery->date)->format('Y-m-d');
            });

            foreach ($dateSales as $date => $items) {
                $totalQty = $items->sum('item_qty');
                $totalAmount = $items->sum('item_total');

                $result[] = [
                    'date' => $date,
                    'items_count' => $items->count(),
                    'quantity_sold' => $totalQty,
                    'total_amount' => $totalAmount,
                ];
            }
        }
        // Group by client
        else if (!empty($additionalCriteria['group_by']) && $additionalCriteria['group_by'] === 'client') {
            $clientSales = $orderItems->groupBy(function($item) {
                return $item->delivery->client;
            });

            foreach ($clientSales as $client => $items) {
                $totalQty = $items->sum('item_qty');
                $totalAmount = $items->sum('item_total');

                $result[] = [
                    'client' => $client,
                    'items_count' => $items->count(),
                    'quantity_sold' => $totalQty,
                    'total_amount' => $totalAmount,
                ];
            }
        }
        // Individual sales details
        else {
            foreach ($orderItems as $item) {
                $product = $item->item;

                if (!$product) {
                    continue;
                }

                $result[] = [
                    'dr_number' => $item->transno,
                    'date' => $item->delivery->date,
                    'client' => $item->delivery->client,
                    'product_id' => $item->product_id,
                    'description' => $product->product_description,
                    'quantity' => $item->item_qty,
                    'unit_price' => $item->item_price,
                    'total_amount' => $item->item_total,
                    'status' => $item->status,
                ];
            }
        }

        return $result;
    }

    /**
     * Generate payments report
     */
    private function generatePaymentsReport($startDate, $endDate, $additionalCriteria)
    {
        $query = OrderPaymentHistory::whereBetween('date_of_payment', [$startDate, $endDate]);

        if (!empty($additionalCriteria['payment_status'])) {
            $query->where('status', $additionalCriteria['payment_status']);
        }

        if (!empty($additionalCriteria['payment_mode'])) {
            $query->where('mop', $additionalCriteria['payment_mode']);
        }

        if (!empty($additionalCriteria['client'])) {
            $query->whereHas('details', function($query) use ($additionalCriteria) {
                $query->where('oa_client', 'LIKE', '%' . $additionalCriteria['client'] . '%');
            });
        }

        $payments = $query->with('details')->get();

        $result = [];

        foreach ($payments as $payment) {
            $order = $payment->details;

            $result[] = [
                'payment_id' => $payment->id,
                'order_number' => $order ? $order->oa_number : 'N/A',
                'client' => $order ? $order->oa_client : 'N/A',
                'payment_date' => $payment->date_of_payment,
                'payment_mode' => $payment->mop,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'reference_no' => $payment->reference_no,
                'due_date' => $payment->due_date,
                'remarks' => $payment->remarks,
            ];
        }

        return $result;
    }

    /**
     * Generate returns report
     */
    private function generateReturnsReport($startDate, $endDate, $additionalCriteria)
    {
        $query = OrderReturn::whereHas('info', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        });

        if (!empty($additionalCriteria['item_type'])) {
            $query->where('item_type', $additionalCriteria['item_type']);
        }


        if (!empty($additionalCriteria['product_id'])) {
            $query->where('product_id', $additionalCriteria['product_id']);
        }


        $returns = $query->with(['info', 'item'])->get();

        $result = [];

        foreach ($returns as $return) {
            $product = $return->item;

            if (!$product) {
                continue;
            }

            $result[] = [
                'return_slip_number' => 'RSN-' . $return->return_no,
                'order_number' => $return->info->oa_no,
                'date_returned' => $return->date_returned,
                'product_code' => $product->code,
                'product_description' => $product->product_description,
                'quantity_returned' => $return->qty,
                'item_type' => $return->item_type,
                'reason' => $return->reason,
                'status' => $return->info->status,
            ];
        }

        return $result;
    }

    /**
     * Generate stock-in report
     */
    private function generateStockinReport($startDate, $endDate, $additionalCriteria)
    {
        $query = Stockin::whereBetween('date', [$startDate, $endDate]);

        if (!empty($additionalCriteria['category_id'])) {
            $query->whereHas('product', function($q) use ($additionalCriteria) {
                $q->where('category_id', $additionalCriteria['category_id']);
            });
        }


        if (!empty($additionalCriteria['product_id'])) {
            $query->where('product_id', $additionalCriteria['product_id']);
        }

        $stockins = $query->with(['product', 'user'])->get();

        $result = [];

        foreach ($stockins as $stockin) {
            $product = $stockin->product;

            if (!$product) {
                continue;
            }

            $result[] = [
                'stockin_id' => $stockin->stockIn_id,
                'date' => $stockin->date,
                'product_code' => $product->code,
                'product_description' => $product->product_description,
                'category' => $product->category ? $product->category->category_name : 'N/A',
                'quantity' => $stockin->stockin_qty,
                'remarks' => $stockin->remarks,
                'recorded_by' => $stockin->user ? $stockin->user->emp_name : 'System',
            ];
        }

        return $result;
    }

    /**
     * Generate merchandise report
     */
    private function generateMerchandiseReport($startDate, $endDate, $additionalCriteria)
    {
        // Get merchandise items
        $query = MerchandiseItem::query();

        $items = $query->get();

        // Get inventory items in the date range
        $inventoryItems = MerchandiseInventoryItem::whereHas('inv', function($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        })->get()->groupBy('product_id');

        // Get merchandise delivered in the date range
        $deliveredItems = MerchandiseDeliveryItem::whereHas('delivery', function($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        })->get()->groupBy('product_id');

        $result = [];

        foreach ($items as $item) {
            $itemInventory = $inventoryItems->get($item->id, collect([]));

            $initialStock = $item->qty;
            $stockIn = 0;
            $stockOut = 0;
            $returns = 0;

            foreach ($itemInventory as $invItem) {
                $initialStock = $invItem->beginning_balance;
                $stockIn += $invItem->total_delivered;
                $stockOut += $invItem->total_released;
                $returns += $invItem->total_returned;
            }

            $currentStock = $initialStock + $stockIn - $stockOut + $returns;

            // Calculate delivered quantity
            $deliveredQty = 0;
            if ($deliveredItems->has($item->id)) {
                $deliveredQty = $deliveredItems->get($item->id)->sum('item_qty');
            }

            $result[] = [
                'item_id' => $item->id,
                'item_name' => $item->item,
                'initial_stock' => $initialStock,
                'stock_in' => $stockIn,
                'delivered' => $deliveredQty,
                'returns' => $returns,
                'current_stock' => $currentStock,
                'unit_price' => $item->price,
                'stock_value' => $currentStock * $item->price,
            ];
        }

        return $result;
    }

    /**
     * Generate office supplies report
     */
    private function generateSuppliesReport($startDate, $endDate, $additionalCriteria)
    {
        // Get supply items
        $query = SupplyItem::query();

        if (!empty($additionalCriteria['category_id'])) {
            $query->where('category', $additionalCriteria['category_id']);
        }

        if (!empty($additionalCriteria['location_id'])) {
            $query->where('location', $additionalCriteria['location_id']);
        }

        $items = $query->with(['category_name', 'location_name', 'unit_name'])->get();

        // Get inventory items in the date range
        $inventoryItems = SupplyInventoryItem::whereHas('inv', function($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        })->get()->groupBy('item_id');

        $result = [];

        foreach ($items as $item) {
            $itemInventory = $inventoryItems->get($item->id, collect([]));

            $initialQty = $item->qty;
            $added = 0;
            $disposed = 0;

            foreach ($itemInventory as $invItem) {
                $initialQty = $invItem->beginning_balance;
                $added += $invItem->added;
                $disposed += $invItem->disposed;
            }

            $currentQty = $initialQty + $added - $disposed;

            $result[] = [
                'item_id' => $item->id,
                'item_name' => $item->item_name,
                'category' => $item->category_name ? $item->category_name->name : 'N/A',
                'location' => $item->location_name ? $item->location_name->name : 'N/A',
                'unit' => $item->unit_name ? $item->unit_name->unit : 'N/A',
                'initial_qty' => $initialQty,
                'added' => $added,
                'disposed' => $disposed,
                'current_qty' => $currentQty,
                'unit_price' => $item->unit_price,
                'total_value' => $currentQty * $item->unit_price,
                'date_purchased' => $item->date_purchased,
            ];
        }

        return $result;
    }

    /**
     * Generate PDF format of the report
     */
    private function generatePDF($viewData)
    {
        // This implementation depends on your PDF library
        // Using Laravel's built-in PDF generation capabilities with Dompdf
        $pdf = \PDF::loadView('reports.pdf', $viewData);

        $fileName = strtolower(str_replace(' ', '_', $viewData['reportTitle'])) . '_' . date('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Generate CSV format of the report
     */
    private function generateCSV($viewData)
    {
        $fileName = strtolower(str_replace(' ', '_', $viewData['reportTitle'])) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $reportData = $viewData['reportData'];

        if (empty($reportData)) {
            return redirect()->back()->with('error', 'No data available for export');
        }

        $columns = array_keys($reportData[0]);

        $callback = function() use ($reportData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($reportData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate Excel format of the report
     */
    private function generateExcel($viewData)
    {
        // This implementation depends on your Excel library
        // Using Laravel Excel or similar package would be required

        // Example implementation with Laravel Excel
        // return Excel::download(new ReportExport($viewData), 'report.xlsx');

        // For now, fallback to CSV
        return $this->generateCSV($viewData);
    }
}
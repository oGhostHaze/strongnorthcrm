@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Report Generator</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.generate') }}" method="POST" id="reportForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="report_type">Report Type</label>
                                <select class="form-control @error('report_type') is-invalid @enderror" id="report_type"
                                    name="report_type" required>
                                    <option value="">Select Report Type</option>
                                    @foreach ($reportTypes as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('report_type') == $value ? 'selected' : '' }}>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('report_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label>Date Range</label>
                            <div class="date-range-picker">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa-solid fa-calendar"></i></span>
                                    </div>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        id="start_date" name="start_date"
                                        value="{{ old('start_date', date('Y-m-d', strtotime('-30 days'))) }}" required>
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text">to</span>
                                    </div>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                        id="end_date" name="end_date" value="{{ old('end_date', date('Y-m-d')) }}"
                                        required>
                                </div>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="quick-range-buttons">
                                <button type="button" id="quick_range_today"
                                    class="btn btn-sm btn-outline-secondary">Today</button>
                                <button type="button" id="quick_range_yesterday"
                                    class="btn btn-sm btn-outline-secondary">Yesterday</button>
                                <button type="button" id="quick_range_this_week"
                                    class="btn btn-sm btn-outline-secondary">This Week</button>
                                <button type="button" id="quick_range_this_month"
                                    class="btn btn-sm btn-outline-secondary">This Month</button>
                                <button type="button" id="quick_range_last_month"
                                    class="btn btn-sm btn-outline-secondary">Last Month</button>
                                <button type="button" id="quick_range_this_year"
                                    class="btn btn-sm btn-outline-secondary">This Year</button>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Additional Criteria</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Dynamic criteria fields will be loaded here based on report type -->
                                    <div id="additional_criteria_container">
                                        <div class="text-center text-muted py-3">
                                            <i class="fa-solid fa-filter fa-2x mb-2"></i>
                                            <p>Select a report type to view additional filter options</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="format">Report Format</label>
                                <select class="form-control @error('format') is-invalid @enderror" id="format"
                                    name="format" required>
                                    <option value="html" {{ old('format') == 'html' ? 'selected' : '' }}>View in Browser
                                    </option>
                                    <option value="pdf" {{ old('format') == 'pdf' ? 'selected' : '' }}>PDF</option>
                                    <option value="csv" {{ old('format') == 'csv' ? 'selected' : '' }}>CSV</option>
                                    <option value="excel" {{ old('format') == 'excel' ? 'selected' : '' }}>Excel</option>
                                </select>
                                @error('format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-file-export mr-1"></i> Generate Report
                            </button>
                            <button type="reset" class="btn btn-secondary ml-2">
                                <i class="fa-solid fa-undo mr-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Report Description</h5>
            </div>
            <div class="card-body">
                <div id="report_description">
                    <p class="text-muted">Select a report type to see its description.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Load appropriate criteria options when report type changes
            $('#report_type').on('change', function() {
                const reportType = $(this).val();
                if (!reportType) {
                    $('#additional_criteria_container').html(`
                    <div class="text-center text-muted py-3">
                        <i class="fa-solid fa-filter fa-2x mb-2"></i>
                        <p>Select a report type to view additional filter options</p>
                    </div>
                `);
                    $('#report_description').html(`
                    <p class="text-muted">Select a report type to see its description.</p>
                `);
                    return;
                }

                // Load the right criteria form based on report type
                switch (reportType) {
                    case 'inventory':
                        loadInventoryCriteria();
                        updateReportDescription('inventory');
                        break;
                    case 'sales':
                        loadSalesCriteria();
                        updateReportDescription('sales');
                        break;
                    case 'payments':
                        loadPaymentsCriteria();
                        updateReportDescription('payments');
                        break;
                    case 'returns':
                        loadReturnsCriteria();
                        updateReportDescription('returns');
                        break;
                    case 'stockin':
                        loadStockinCriteria();
                        updateReportDescription('stockin');
                        break;
                    case 'merchandise':
                        loadMerchandiseCriteria();
                        updateReportDescription('merchandise');
                        break;
                    case 'supplies':
                        loadSuppliesCriteria();
                        updateReportDescription('supplies');
                        break;
                    default:
                        $('#additional_criteria_container').html(
                            '<p>No additional criteria available for this report type.</p>');
                        $('#report_description').html(`
                        <p class="text-muted">No description available for this report type.</p>
                    `);
                }
            });

            // Trigger change event to load criteria for any pre-selected report type
            $('#report_type').trigger('change');

            // Functions to load different criteria forms
            function loadInventoryCriteria() {
                $('#additional_criteria_container').html(`
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">Product Category</label>
                            <select class="form-control" id="category_id" name="additional_criteria[category_id]">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="low_stock">Show Only Low Stock Items</label>
                            <select class="form-control" id="low_stock" name="additional_criteria[low_stock]">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="min_stock">Minimum Stock Level</label>
                            <input type="number" class="form-control" id="min_stock" name="additional_criteria[min_stock]" min="0" placeholder="Enter minimum stock level">
                            <small class="form-text text-muted">Filter products with stock at or above this level</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="max_stock">Maximum Stock Level</label>
                            <input type="number" class="form-control" id="max_stock" name="additional_criteria[max_stock]" min="0" placeholder="Enter maximum stock level">
                            <small class="form-text text-muted">Filter products with stock at or below this level</small>
                        </div>
                    </div>
                </div>
            `);
            }

            function loadSalesCriteria() {
                $('#additional_criteria_container').html(`
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="group_by">Group Results By</label>
                            <select class="form-control" id="group_by" name="additional_criteria[group_by]">
                                <option value="">No Grouping (Show Details)</option>
                                <option value="product">Group by Product</option>
                                <option value="date">Group by Date</option>
                                <option value="client">Group by Client</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Item Status</label>
                            <select class="form-control" id="status" name="additional_criteria[status]">
                                <option value="">All Statuses</option>
                                <option value="Released">Released</option>
                                <option value="To Follow">To Follow</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="min_amount">Minimum Amount</label>
                            <input type="number" class="form-control" id="min_amount" name="additional_criteria[min_amount]" min="0" step="0.01" placeholder="Enter minimum amount">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="client">Client</label>
                            <input type="text" class="form-control" id="client" name="additional_criteria[client]" placeholder="Enter client name">
                            <small class="form-text text-muted">Filter sales by client name (partial match)</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select class="form-control" id="product_id" name="additional_criteria[product_id]">
                                <option value="">All Products</option>
                                @foreach ($categories as $category)
                                    <optgroup label="{{ $category->category_name }}">
                                        @foreach ($category->products as $product)
                                            <option value="{{ $product->product_id }}">{{ $product->product_description }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sort_by">Sort By</label>
                            <select class="form-control" id="sort_by" name="additional_criteria[sort_by]">
                                <option value="date_desc">Date (Newest First)</option>
                                <option value="date_asc">Date (Oldest First)</option>
                                <option value="amount_desc">Amount (Highest First)</option>
                                <option value="amount_asc">Amount (Lowest First)</option>
                            </select>
                        </div>
                    </div>
                </div>
            `);
            }

            function loadPaymentsCriteria() {
                $('#additional_criteria_container').html(`
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_status">Payment Status</label>
                            <select class="form-control" id="payment_status" name="additional_criteria[payment_status]">
                                <option value="">All Statuses</option>
                                <option value="Posted">Posted</option>
                                <option value="Unposted">Unposted</option>
                                <option value="On-hold">On-hold</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_mode">Payment Mode</label>
                            <select class="form-control" id="payment_mode" name="additional_criteria[payment_mode]">
                                <option value="">All Payment Modes</option>
                                @foreach ($paymentModes as $mode)
                                    <option value="{{ $mode->legend }}">{{ $mode->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="min_amount">Minimum Amount</label>
                            <input type="number" class="form-control" id="min_amount" name="additional_criteria[min_amount]" min="0" step="0.01" placeholder="Enter minimum amount">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="client">Client</label>
                            <input type="text" class="form-control" id="client" name="additional_criteria[client]" placeholder="Enter client name">
                            <small class="form-text text-muted">Filter payments by client name (partial match)</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="reference_no">Reference Number</label>
                            <input type="text" class="form-control" id="reference_no" name="additional_criteria[reference_no]" placeholder="Enter reference number">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="group_by">Group Results By</label>
                            <select class="form-control" id="group_by" name="additional_criteria[group_by]">
                                <option value="">No Grouping (Show Details)</option>
                                <option value="status">Group by Status</option>
                                <option value="payment_mode">Group by Payment Mode</option>
                                <option value="client">Group by Client</option>
                                <option value="date">Group by Date</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="due_date_start">Due Date Start</label>
                            <input type="date" class="form-control" id="due_date_start" name="additional_criteria[due_date_start]">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="due_date_end">Due Date End</label>
                            <input type="date" class="form-control" id="due_date_end" name="additional_criteria[due_date_end]">
                        </div>
                    </div>
                </div>
            `);
            }

            function loadReturnsCriteria() {
                $('#additional_criteria_container').html(`
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="return_status">Return Status</label>
                            <select class="form-control" id="return_status" name="additional_criteria[return_status]">
                                <option value="">All Statuses</option>
                                <option value="For Approval">For Approval</option>
                                <option value="Approved">Approved</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="item_type">Item Type</label>
                            <select class="form-control" id="item_type" name="additional_criteria[item_type]">
                                <option value="">All Types</option>
                                <option value="item">Regular Items</option>
                                <option value="gift">Gift Items</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select class="form-control" id="product_id" name="additional_criteria[product_id]">
                                <option value="">All Products</option>
                                @foreach ($categories as $category)
                                    <optgroup label="{{ $category->category_name }}">
                                        @foreach ($category->products as $product)
                                            <option value="{{ $product->product_id }}">{{ $product->product_description }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="client">Client</label>
                            <input type="text" class="form-control" id="client" name="additional_criteria[client]" placeholder="Enter client name">
                            <small class="form-text text-muted">Filter returns by client name (partial match)</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="reason">Return Reason</label>
                            <input type="text" class="form-control" id="reason" name="additional_criteria[reason]" placeholder="Enter return reason">
                            <small class="form-text text-muted">Filter by reason (partial match)</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="min_qty">Minimum Quantity</label>
                            <input type="number" class="form-control" id="min_qty" name="additional_criteria[min_qty]" min="0" placeholder="Enter minimum quantity">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="group_by">Group Results By</label>
                            <select class="form-control" id="group_by" name="additional_criteria[group_by]">
                                <option value="">No Grouping (Show Details)</option>
                                <option value="product">Group by Product</option>
                                <option value="date">Group by Date</option>
                                <option value="status">Group by Status</option>
                            </select>
                        </div>
                    </div>
                </div>
            `);
            }

            function loadStockinCriteria() {
                $('#additional_criteria_container').html(`
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">Product Category</label>
                            <select class="form-control" id="category_id" name="additional_criteria[category_id]">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="min_qty">Minimum Quantity</label>
                            <input type="number" class="form-control" id="min_qty" name="additional_criteria[min_qty]" min="0" placeholder="Enter minimum quantity">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select class="form-control" id="product_id" name="additional_criteria[product_id]">
                                <option value="">All Products</option>
                                @foreach ($categories as $category)
                                    <optgroup label="{{ $category->category_name }}">
                                        @foreach ($category->products as $product)
                                            <option value="{{ $product->product_id }}">{{ $product->product_description }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <input type="text" class="form-control" id="remarks" name="additional_criteria[remarks]" placeholder="Enter remarks">
                            <small class="form-text text-muted">Filter by remarks (partial match)</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="group_by">Group Results By</label>
                            <select class="form-control" id="group_by" name="additional_criteria[group_by]">
                                <option value="">No Grouping (Show Details)</option>
                                <option value="product">Group by Product</option>
                                <option value="date">Group by Date</option>
                                <option value="category">Group by Category</option>
                            </select>
                        </div>
                    </div>
                </div>
            `);
            }

            function loadMerchandiseCriteria() {
                $('#additional_criteria_container').html(`
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="stock_status">Stock Status</label>
                            <select class="form-control" id="stock_status" name="additional_criteria[stock_status]">
                                <option value="">All Items</option>
                                <option value="in_stock">In Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="min_price">Minimum Price</label>
                            <input type="number" class="form-control" id="min_price" name="additional_criteria[min_price]" min="0" step="0.01" placeholder="Enter minimum price">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="item_name">Item Name</label>
                            <input type="text" class="form-control" id="item_name" name="additional_criteria[item_name]" placeholder="Enter item name">
                            <small class="form-text text-muted">Filter by item name (partial match)</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sort_by">Sort By</label>
                            <select class="form-control" id="sort_by" name="additional_criteria[sort_by]">
                                <option value="name_asc">Name (A to Z)</option>
                                <option value="name_desc">Name (Z to A)</option>
                                <option value="price_asc">Price (Low to High)</option>
                                <option value="price_desc">Price (High to Low)</option>
                                <option value="stock_asc">Stock (Low to High)</option>
                                <option value="stock_desc">Stock (High to Low)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="include_movement">Include Movement Details</label>
                            <select class="form-control" id="include_movement" name="additional_criteria[include_movement]">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                            <small class="form-text text-muted">Include detailed stock movement</small>
                        </div>
                    </div>
                </div>
            `);
            }

            function loadSuppliesCriteria() {
                $('#additional_criteria_container').html(`
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="category_id">Supply Category</label>
                            <select class="form-control" id="category_id" name="additional_criteria[category_id]">
                                <option value="">All Categories</option>
                                <!-- Add supply categories here -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="location_id">Location</label>
                            <select class="form-control" id="location_id" name="additional_criteria[location_id]">
                                <option value="">All Locations</option>
                                <!-- Add locations here -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="stock_status">Stock Status</label>
                            <select class="form-control" id="stock_status" name="additional_criteria[stock_status]">
                                <option value="">All Items</option>
                                <option value="in_stock">In Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="item_name">Item Name</label>
                            <input type="text" class="form-control" id="item_name" name="additional_criteria[item_name]" placeholder="Enter item name">
                            <small class="form-text text-muted">Filter by item name (partial match)</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="min_price">Minimum Price</label>
                            <input type="number" class="form-control" id="min_price" name="additional_criteria[min_price]" min="0" step="0.01" placeholder="Enter minimum price">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="group_by">Group Results By</label>
                            <select class="form-control" id="group_by" name="additional_criteria[group_by]">
                                <option value="">No Grouping (Show Details)</option>
                                <option value="category">Group by Category</option>
                                <option value="location">Group by Location</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="include_disposed">Include Disposed Items</label>
                            <select class="form-control" id="include_disposed" name="additional_criteria[include_disposed]">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                            <small class="form-text text-muted">Include details of disposed items</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sort_by">Sort By</label>
                            <select class="form-control" id="sort_by" name="additional_criteria[sort_by]">
                                <option value="name_asc">Name (A to Z)</option>
                                <option value="name_desc">Name (Z to A)</option>
                                <option value="value_desc">Value (High to Low)</option>
                                <option value="value_asc">Value (Low to High)</option>
                            </select>
                        </div>
                    </div>
                </div>
            `);
            }

            function updateReportDescription(reportType) {
                let description = '';

                switch (reportType) {
                    case 'inventory':
                        description = `
                        <h6>Inventory Report</h6>
                        <p>Shows current inventory levels for all products, including initial stock, stock-in, stock-out, returns, and current stock quantities. Also calculates total stock value based on product prices.</p>
                        <ul>
                            <li>Filter by category to focus on specific product groups</li>
                            <li>Show only low stock items to identify products needing reorder</li>
                            <li>Set minimum and maximum stock levels to filter products within a specific range</li>
                            <li>Low stock items are highlighted in the report for easy identification</li>
                            <li>The report includes a summary with total stock value</li>
                        </ul>
                    `;
                        break;
                    case 'sales':
                        description = `
                        <h6>Sales Report</h6>
                        <p>Tracks all sales transactions, showing delivery receipts, products sold, quantities, prices, and totals. Can be grouped by product, date, or client for different analysis perspectives.</p>
                        <ul>
                            <li>Group by product to see best-selling items</li>
                            <li>Group by date to see sales trends over time</li>
                            <li>Group by client to identify top customers</li>
                            <li>Filter by status to focus on specific transaction types</li>
                            <li>Set minimum amount to focus on higher-value sales</li>
                            <li>The report includes totals for quantities sold and total sales value</li>
                        </ul>
                    `;
                        break;
                    case 'payments':
                        description = `
                        <h6>Payments Report</h6>
                        <p>Provides details on all payments received, including payment method, amount, date, status, and reference numbers. Helps track financial transactions and outstanding balances.</p>
                        <ul>
                            <li>Filter by payment status to focus on posted, unposted, or on-hold payments</li>
                            <li>Filter by payment mode to analyze different payment methods</li>
                            <li>Set minimum amount to focus on larger payments</li>
                            <li>Group results by various criteria for different analysis perspectives</li>
                            <li>Filter by due date range to focus on specific payment periods</li>
                            <li>The report includes summaries by payment status and payment mode</li>
                        </ul>
                    `;
                        break;
                    case 'returns':
                        description = `
                        <h6>Returns Report</h6>
                        <p>Tracks all returned items, including return slip numbers, dates, products, quantities, and reasons. Helps analyze return patterns and identify quality issues.</p>
                        <ul>
                            <li>Filter by return status to focus on approved or pending returns</li>
                            <li>Filter by item type to separate regular items from gifts</li>
                            <li>Filter by product to track specific item return rates</li>
                            <li>Filter by return reason to identify common issues</li>
                            <li>Group by various criteria for different analysis perspectives</li>
                            <li>The report includes summaries by return status and item type</li>
                        </ul>
                    `;
                        break;
                    case 'stockin':
                        description = `
                        <h6>Stock-in Report</h6>
                        <p>Shows all stock additions to inventory, including dates, products, quantities, and the person who recorded the transaction. Helps track inventory growth and auditing.</p>
                        <ul>
                            <li>Filter by category to focus on specific product groups</li>
                            <li>Filter by minimum quantity to focus on larger stock additions</li>
                            <li>Filter by product to track specific item stock history</li>
                            <li>Filter by remarks to identify special stock-in cases</li>
                            <li>Group by various criteria for different analysis perspectives</li>
                            <li>The report includes summaries by category and date</li>
                        </ul>
                    `;
                        break;
                    case 'merchandise':
                        description = `
                        <h6>Merchandise Report</h6>
                        <p>Tracks promotional merchandise inventory, showing current stock levels, stock movement, and values. Helps manage non-product inventory items.</p>
                        <ul>
                            <li>Filter by stock status to focus on in-stock or out-of-stock items</li>
                            <li>Filter by minimum price to focus on higher-value merchandise</li>
                            <li>Filter by item name to find specific merchandise</li>
                            <li>Sort results by various criteria for different views</li>
                            <li>Include movement details for a more comprehensive analysis</li>
                            <li>The report includes stock status summaries and total value calculations</li>
                        </ul>
                    `;
                        break;
                    case 'supplies':
                        description = `
                        <h6>Office Supplies Report</h6>
                        <p>Monitors office supply inventory, showing current quantities, additions, disposals, and values. Helps manage operational supplies and control expenses.</p>
                        <ul>
                            <li>Filter by category to focus on specific supply types</li>
                            <li>Filter by location to focus on supplies in specific areas</li>
                            <li>Filter by stock status to identify out-of-stock items</li>
                            <li>Group by category or location for different analysis perspectives</li>
                            <li>Include disposed items to track supply usage patterns</li>
                            <li>The report includes summaries by category and location</li>
                        </ul>
                    `;
                        break;
                    default:
                        description = `<p class="text-muted">No description available for this report type.</p>`;
                }

                $('#report_description').html(description);
            }

            // Quick date range selections
            $('#quick_range_today').click(function(e) {
                e.preventDefault();
                const today = new Date().toISOString().split('T')[0];
                $('#start_date').val(today);
                $('#end_date').val(today);
            });

            $('#quick_range_yesterday').click(function(e) {
                e.preventDefault();
                const yesterday = new Date();
                yesterday.setDate(yesterday.getDate() - 1);
                const yesterdayStr = yesterday.toISOString().split('T')[0];
                $('#start_date').val(yesterdayStr);
                $('#end_date').val(yesterdayStr);
            });

            $('#quick_range_this_week').click(function(e) {
                e.preventDefault();
                const today = new Date();
                const firstDayOfWeek = new Date(today);
                const day = today.getDay() || 7; // Get current day number, converting 0 from Sunday to 7
                if (day !== 1) // Only adjust if not Monday
                    firstDayOfWeek.setHours(-24 * (day - 1));

                $('#start_date').val(firstDayOfWeek.toISOString().split('T')[0]);
                $('#end_date').val(today.toISOString().split('T')[0]);
            });

            $('#quick_range_this_month').click(function(e) {
                e.preventDefault();
                const today = new Date();
                const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

                $('#start_date').val(firstDayOfMonth.toISOString().split('T')[0]);
                $('#end_date').val(today.toISOString().split('T')[0]);
            });

            $('#quick_range_last_month').click(function(e) {
                e.preventDefault();
                const today = new Date();
                const firstDayOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                const lastDayOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);

                $('#start_date').val(firstDayOfLastMonth.toISOString().split('T')[0]);
                $('#end_date').val(lastDayOfLastMonth.toISOString().split('T')[0]);
            });

            $('#quick_range_this_year').click(function(e) {
                e.preventDefault();
                const today = new Date();
                const firstDayOfYear = new Date(today.getFullYear(), 0, 1);

                $('#start_date').val(firstDayOfYear.toISOString().split('T')[0]);
                $('#end_date').val(today.toISOString().split('T')[0]);
            });

            // Form validation before submit
            $('#reportForm').on('submit', function(e) {
                const reportType = $('#report_type').val();
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const format = $('#format').val();

                if (!reportType || !startDate || !endDate || !format) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing Required Fields',
                        text: 'Please fill in all required fields: Report Type, Start Date, End Date, and Format.',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // Validate date range
                if (new Date(startDate) > new Date(endDate)) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Date Range',
                        text: 'Start Date cannot be after End Date.',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                return true;
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .report-generator-container {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
        }

        .date-range-picker {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .quick-range-buttons {
            margin-top: 10px;
        }

        .quick-range-buttons button {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .criteria-section {
            background-color: #fff;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

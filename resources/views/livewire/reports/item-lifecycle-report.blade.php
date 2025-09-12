<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Item Lifecycle Report</h5>
            <p class="card-text">Track items from order to delivery and returns</p>
        </div>
        <div class="card-body">
            <!-- Filters and Search -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" wire:model="start_date" id="start_date" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" wire:model="end_date" id="end_date" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filter_type">Status Filter</label>
                        <select wire:model="filter_type" id="filter_type" class="form-select">
                            <option value="all">All Items</option>
                            <option value="pending">To Follow Items</option>
                            <option value="delivered">Delivered Items</option>
                            <option value="returned">Returned Items</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="search">Search Item Description</label>
                        <input type="text" wire:model.debounce.300ms="search" id="search" class="form-control"
                            placeholder="Search products...">
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="product_id">Product Filter</label>
                        <select wire:model="product_id" id="product_id" class="form-select">
                            <option value="">All Products</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->product_id }}">{{ $product->product_description }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="client_name">Client Name</label>
                        <input type="text" wire:model.debounce.300ms="client_name" id="client_name"
                            class="form-control" placeholder="Filter by client...">
                    </div>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button class="btn btn-success me-2" wire:click="exportCsv">
                        <i class="fa fa-file-excel"></i> Export CSV
                    </button>
                    <button class="btn btn-danger" wire:click="exportPdf">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>

            <!-- Results Table -->
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Item Type</th>
                            <th>Item Description</th>
                            <th>Order Agreement</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Ordered</th>
                            <th>Released</th>
                            <th>Returned</th>
                            <th>To Follow</th>
                            <th>Delivery Details</th>
                            <th>Return Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $item)
                            <tr>
                                <td>{{ $item['type'] }}</td>
                                <td>{{ $item['product_description'] }}</td>
                                <td>
                                    <a href="{{ route('order.agreements.view', ['oa' => $item['oa_id']]) }}"
                                        class="text-primary" target="_blank">
                                        {{ $item['oa_number'] }}
                                        <i class="fa fa-external-link-alt fa-xs ms-1"></i>
                                    </a>
                                </td>
                                <td>{{ $item['oa_date'] }}</td>
                                <td>{{ $item['client'] }}</td>
                                <td class="text-center">{{ $item['ordered_qty'] }}</td>
                                <td class="text-center">{{ $item['released_qty'] }}</td>
                                <td class="text-center">{{ $item['returned_qty'] }}</td>
                                <td class="text-center">{{ $item['pending_qty'] }}</td>
                                <td>
                                    @if (count($item['deliveries']) > 0)
                                        @foreach ($item['deliveries'] as $delivery)
                                            <div class="mb-1">
                                                <a href="{{ route('order.delivery.view', ['transno' => $delivery['transno']]) }}"
                                                    class="badge bg-primary text-decoration-none" target="_blank">
                                                    {{ $delivery['transno'] }}
                                                    <i class="fa fa-external-link-alt fa-xs"></i>
                                                </a>
                                                <small>{{ $delivery['date'] }} ({{ $delivery['qty'] }})</small>
                                                <small class="text-muted">{{ $delivery['code'] }}</small>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="badge bg-secondary">No deliveries</span>
                                    @endif
                                </td>
                                <td>
                                    @if (count($item['returns']) > 0)
                                        @foreach ($item['returns'] as $return)
                                            <div class="mb-1">
                                                <a href="{{ route('order.returns.view', ['rsn' => $return['return_id']]) }}"
                                                    class="badge bg-danger text-decoration-none" target="_blank">
                                                    {{ $return['return_no'] }}
                                                    <i class="fa fa-external-link-alt fa-xs"></i>
                                                </a>
                                                <small>{{ $return['date'] }} ({{ $return['qty'] }})</small>
                                                @if ($return['reason'])
                                                    <br><small class="text-muted">Reason:
                                                        {{ $return['reason'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="badge bg-secondary">No returns</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-3">
                                    <span class="text-muted">No items found matching your filters</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.addEventListener('alert', event => {
            Swal.fire({
                icon: event.detail.type,
                title: 'Notification',
                text: event.detail.message,
                timer: 3000,
                showConfirmButton: false
            });
        });
    </script>
@endpush

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <span class="text-uppercase fw-bolder">Inventory Report</span>
                        <div class="d-flex">
                            <button class="btn btn-sm btn-primary" onClick="print_div()"><i
                                    class="fas fa-print me-1"></i>Print</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 my-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" id="search">FROM</span>
                                <input type="date" class="form-control form-control-sm" aria-label="Search"
                                    aria-describedby="search" wire:model.lazy="start_date">
                                <span class="input-group-text" id="search">TO</span>
                                <input type="date" class="form-control form-control-sm" aria-label="Search"
                                    aria-describedby="search" wire:model.lazy="end_date">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7 my-2">
                            <div class="input-group">
                                <span class="input-group-text" id="search"><i
                                        class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" class="form-control form-control-sm" placeholder="Search"
                                    aria-label="Search" aria-describedby="search" wire:model.lazy="search">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" id='print_div'>
                        <div class="text-center my-3">
                            <h4>Inventory Report</h4>
                            <h6>Date Range: {{ date('F j, Y', strtotime($start_date)) }} to
                                {{ date('F j, Y', strtotime($end_date)) }}</h6>
                        </div>
                        <table class="table table-sm table-hover table-striped table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>Date Range</th>
                                    <th>Product Code</th>
                                    <th>Product Description</th>
                                    <th class="text-end pe-2">Beginning Balance</th>
                                    <th class="text-end pe-2">Total Stock In</th>
                                    <th class="text-end pe-2">Total Released</th>
                                    <th class="text-end pe-2">Total Returned</th>
                                    <th class="text-end pe-2">Ending Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $row)
                                    <tr>
                                        <td>{{ date('M j', strtotime($row->start_date)) }} -
                                            {{ date('M j, Y', strtotime($row->end_date)) }}</td>
                                        <td>{{ $row->product->code }}</td>
                                        <td>{{ $row->product->product_description }}</td>
                                        <td class="text-end pe-2">{{ $row->beginning_balance }}</td>
                                        <td class="text-end pe-2">{{ $row->total_delivered }}</td>
                                        <td class="text-end pe-2">{{ $row->total_released }}</td>
                                        <td class="text-end pe-2">{{ $row->total_returned }}</td>
                                        <td class="text-end pe-2">{{ $row->ending_balance }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No Record Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function print_div() {
            var printContents = document.getElementById('print_div').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload(true);
        }
    </script>
@endpush

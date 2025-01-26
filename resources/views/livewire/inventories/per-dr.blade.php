<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <span class="text-uppercase fw-bolder">Inventory Report</span>
                        <div class="d-flex">
                                <button class="btn btn-sm btn-primary"  onClick="print_div()"><i class="fas fa-print me-1"></i>Print</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 my-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" id="search">FROM</span>
                                <input type="date" class="form-control form-control-sm" aria-label="Search" aria-describedby="search" wire:model.lazy="start_date">
                                <span class="input-group-text" id="search">TO</span>
                                <input type="date" class="form-control form-control-sm" aria-label="Search" aria-describedby="search" wire:model.lazy="end_date">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7 my-2">
                            <div class="input-group">
                                <span class="input-group-text" id="search"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" class="form-control form-control-sm" placeholder="Search" aria-label="Search" aria-describedby="search" wire:model.lazy="search">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" id='print_div'>
                        <table class="table table-sm table-hover table-striped table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>Date</th>
                                    <th>DR #</th>
                                    <th>Client</th>
                                    <th>Product Description</th>
                                    <th class="text-end pe-2">Price</th>
                                    <th class="text-end pe-2">QTY</th>
                                    <th class="text-end pe-2">Total</th>
                                    <th class="text-end pe-2">Type</th>
                                    <th class="text-end pe-2">Status</th>
                                    <th class="text-end pe-2">Code</th>
                                    <th class="text-end pe-2">Referenced DR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $row)
                                    <tr>
                                        <td>{{date('F j, Y',strtotime($row->date))}}</td>
                                        <td>{{$row->transno}}</td>
                                        <td>{{$row->client}}</td>
                                        <td>{{$row->product_description}}</td>
                                        <td class="text-end pe-2">{{$row->item_price}}</td>
                                        <td class="text-end pe-2">{{$row->item_qty}}</td>
                                        <td class="text-end pe-2">{{$row->item_total}}</td>
                                        <td class="text-end pe-2">{{$row->type ?? 'ITEMS'}}</td>
                                        <td class="text-end">{{$row->status}}</td>
                                        <td class="text-end">{{$row->code}}</td>
                                        <td class="text-end">{{$row->dr_reference}}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">No Record Found</td>
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
        function print_div(){
            var printContents = document.getElementById('print_div').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload(true);
        }
    </script>
@endpush

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <span class="text-uppercase fw-bolder">Inventory Report</span>
                        <div class="d-flex w-25">
                            <div class="">
                                {{-- <a href="" class="btn btn-sm btn-primary">New Product</a> --}}
                            </div>
                            <div class="col ms-2">
                                <div class="input-group">
                                    <span class="input-group-text" id="search"><i class="fa-solid fa-magnifying-glass"></i></span>
                                    <input type="text" class="form-control form-control-sm" placeholder="Search" aria-label="Search" aria-describedby="search" wire:model.lazy="search">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
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
                                        <td colspan="7" class="text-center">No Record Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <caption>{{$data->links()}}</caption>
                </div>
            </div>
        </div>
    </div>
</div>

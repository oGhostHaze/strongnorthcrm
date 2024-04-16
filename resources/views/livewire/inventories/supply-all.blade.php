<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <span class="text-uppercase fw-bolder">Office Supply Inventory Report</span>
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
                                    <th>Inventory Date</th>
                                    <th>Office Supply</th>
                                    <th class="text-end pe-2">Beginning Balance</th>
                                    <th class="text-end pe-2">Total Added</th>
                                    <th class="text-end pe-2">Total Disposed</th>
                                    <th class="text-end pe-2">Ending Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $row)
                                    <tr>
                                        <td>{{date('F j, Y',strtotime($row->inv->date))}}</td>
                                        <td>{{$row->item->item_name}}</td>
                                        <td class="text-end pe-2">{{$row->beginning_balance}}</td>
                                        <td class="text-end pe-2">{{$row->added}}</td>
                                        <td class="text-end pe-2">{{$row->disposed}}</td>
                                        <td class="text-end pe-2">{{($row->beginning_balance + $row->added) - $row->disposed}}</td>
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

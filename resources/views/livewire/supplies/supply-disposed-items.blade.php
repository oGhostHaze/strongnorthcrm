<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">Disposed Office Supply List</span>
                <div class="d-flex w-25">
                    <div class="">
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
                            <th>Date Purchased</th>
                            <th>Date Disposed</th>
                            <th>Category</th>
                            <th>Supply Name</th>
                            <th class="text-end">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($supplies as $row)
                            <tr>
                                <td>{{$row->item->date_purchased}}</td>
                                <td>{{$row->created_at}}</td>
                                <td>{{$row->item->category_name->name}}</td>
                                <td>{{$row->item->item_name}}</td>
                                <td class="text-end">{{$row->qty}} {{$row->item->unit_name->unit}}</td>
                            </tr>
                        @empty
                            <tr class="table-danger">
                                <td class="text-center" colspan="5">No Record Found!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <caption>{{$supplies->links()}}</caption>
        </div>
    </div>
</div>

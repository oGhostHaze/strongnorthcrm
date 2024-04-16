<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">Sets List</span>
                <div class="d-flex w-25">
                    <div class="">
                        @can('add-set')
                        <a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">New Set</a>
                        @endcan
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
                            <th width="10%">Set ID</th>
                            <th>Set Name</th>
                            <th>Set Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sets as $row)
                            <tr wire:click="$emit('view_set', {{$row}})" style="cursor: pointer">
                                <td>{{$row->set_id}}</td>
                                <td>{{$row->set_name}}</td>
                                <td>{{number_format($row->set_price, 2)}}</td>
                            </tr>
                        @empty
                        <tr class="table-danger">
                            <td colspan="2" class="text-center fw-bold">No record found!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

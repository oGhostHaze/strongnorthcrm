<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">Supply Category List</span>
                <div class="d-flex w-25">
                    <div class="">
                        <a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">New Unit of Measurement</a>
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
                            <th width="10%">UOM ID</th>
                            <th>Unit</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($measurements as $row)
                            <tr>
                                <td>{{$row->id}}</td>
                                <td>{{$row->unit}}</td>
                                <td>{{$row->description}}</td>
                            </tr>
                        @empty
                        <tr class="table-danger">
                            <td colspan="3" class="text-center fw-bold">No record found!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <caption>{{$measurements->links()}}</caption>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">New Unit of Measurement</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @error('unit')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text w-25" id="unit">Unit</span>
                    <input type="text" class="form-control form-control-sm" wire:model.defer="unit">
                </div>
                @error('description')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text w-25" id="description">Description</span>
                    <input type="text" class="form-control form-control-sm" wire:model.defer="description">
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" wire:click="save()">Save</button>
            </div>
        </div>
        </div>
    </div>
</div>

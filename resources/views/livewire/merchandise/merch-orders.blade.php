<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">Merchandise Orders</span>
                <div class="d-flex w-25">
                    <div class="">
                        <a href="" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">New Order</a>
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
                            <th>Transaction #</th>
                            <th>Client</th>
                            <th class="text-end"><span class="pe-2">Contact #</span></th>
                            <th>Lifechanger</th>
                            <th>Partner</th>
                            <th>Presenter</th>
                            <th>Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $row)
                            <tr wire:click="$emit('view_order', {{$row}})" style="cursor: pointer">
                                <td>{{$row->date}}</td>
                                <td>{{$row->transno}}</td>
                                <td>{{$row->client}}</td>
                                <td class="text-end"><span class="pe-2">{{$row->contact}}</span></td>
                                <td>{{$row->consultant}}</td>
                                <td>{{$row->associate}}</td>
                                <td>{{$row->presenter}}</td>
                                <td>{{$row->items()->sum('item_qty_ordered')}}</td>
                            </tr>
                        @empty

                        @endforelse
                    </tbody>
                </table>
            </div>
            <caption>{{$data->links()}}</caption>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">New Merchandise Order</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @error('date')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2 w-25">
                    <span class="input-group-text" id="date">Date</span>
                    <input type="date" class="form-control form-control-sm" wire:model.defer="date">
                </div>

                <label class="h4">Client Details</label>
                @error('client')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text" id="client">Client</span>
                    <input type="text" class="form-control form-control-sm" wire:model.defer="client">
                </div>
                <div class="row">
                    <div class="col-6">
                        @error('address')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                        <div class="input-group mb-2">
                            <span class="input-group-text" id="address">Address</span>
                            <input type="text" class="form-control form-control-sm" wire:model.defer="address">
                        </div>
                    </div>
                    <div class="col-6">
                        @error('contact')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                        <div class="input-group mb-2">
                            <span class="input-group-text" id="contact">Contact</span>
                            <input type="text" class="form-control form-control-sm" wire:model.defer="contact">
                        </div>
                    </div>
                </div>

                <label class="h4">Lifechangers Involved</label>
                @error('consultant')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text" id="consultant">Consultant</span>
                    <input type="text" class="form-control form-control-sm" wire:model.defer="consultant">
                </div>
                <div class="row">
                    <div class="col-6">
                        @error('associate')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                        <div class="input-group mb-2">
                            <span class="input-group-text" id="associate">Associate</span>
                            <input type="text" class="form-control form-control-sm" wire:model.defer="associate">
                        </div>
                    </div>
                    <div class="col-6">
                        @error('presenter')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                        <div class="input-group mb-2">
                            <span class="input-group-text" id="presenter">Presenter</span>
                            <input type="text" class="form-control form-control-sm" wire:model.defer="presenter">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        @error('team_builder')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                        <div class="input-group mb-2">
                            <span class="input-group-text" id="team_builder">Team Builder</span>
                            <input type="text" class="form-control form-control-sm" wire:model.defer="team_builder">
                        </div>
                    </div>
                    <div class="col-6">
                        @error('distributor')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                        <div class="input-group mb-2">
                            <span class="input-group-text" id="distributor">Distributor</span>
                            <input type="text" class="form-control form-control-sm" wire:model.defer="distributor">
                        </div>
                    </div>
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

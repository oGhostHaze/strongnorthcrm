<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">Servicing</span>
                <div class="d-flex w-25">
                    <div class="">
                        <a href="" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">New Servicing</a>
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
                            <th>Received From</th>
                            <th>Inspected By</th>
                            <th>Client</th>
                            <th class="text-end">Contact #</th>
                            <th>SR #</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $row)
                            <tr wire:click="$emit('view_servicing', {{$row}})" style="cursor: pointer">
                                <td>{{$row->date_received}}</td>
                                <td>{{$row->received_from}}</td>
                                <td>{{$row->inspected_by}}</td>
                                <td>{{$row->client}}</td>
                                <td class="text-end">{{$row->contact_no}}</td>
                                <td>{{$row->sr_no}}</td>
                                <td>{{$row->status}}</td>
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
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">New Servicing</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @error('date_received')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text" id="date_received">Date</span>
                    <input type="date" class="form-control form-control-sm" wire:model.defer="date_received">
                </div>

                @error('client')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text" id="client">Client</span>
                    <input type="text" class="form-control form-control-sm" wire:model.defer="client">
                </div>

                @error('contact_no')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text" id="contact_no">Contact #</span>
                    <input type="text" class="form-control form-control-sm" wire:model.defer="contact_no">
                </div>

                @error('received_from')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text" id="received_from">Received From</span>
                    <input type="text" class="form-control form-control-sm" wire:model.defer="received_from">
                </div>

                @error('inspected_by')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text" id="inspected_by">Inspected By</span>
                    <input type="text" class="form-control form-control-sm" wire:model.defer="inspected_by">
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

@push('scripts')
    <script>
        function update_price(product_id, product_price, product_desc)
        {
            Swal.fire({
                title: '<h5> Update price of ' + ' <strong>' + product_desc + '</strong></h5>',
                html: `<div class="input-group mb-2">
                            <span class="input-group-text" id="up">Price</span>
                            <input id="new_price" type="number" step="0.01" class="form-control form-control-sm" aria-label="Price" aria-describedby="up">
                        </div>`,
                showCancelButton: true,
                confirmButtonText: `Confirm`,
                didOpen: () => {
                    const new_price = Swal.getHtmlContainer().querySelector('#new_price')
                    new_price.value = product_price;

                    new_price.addEventListener('input', () => {
                        @this.set('product_price', new_price.value);
                    })
                }
            }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        Livewire.emit('update_price', product_id)
                    }
            });
        }
    </script>
@endpush

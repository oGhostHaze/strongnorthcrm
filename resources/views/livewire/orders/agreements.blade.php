<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">Order Agreements</span>
                <div class="d-flex w-25">
                    <div class="">
                        @can('create-orders')
                            <a href="" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">New Order</a>
                        @endcan
                    </div>
                    <div class="col ms-2">
                        <div class="input-group">
                            <span class="input-group-text" id="search"><i
                                    class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" class="form-control form-control-sm" placeholder="Search"
                                aria-label="Search" aria-describedby="search" wire:model.lazy="search">
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
                            <th>OA #</th>
                            <th>Client</th>
                            <th>Contact #</th>
                            <th>Lifechanger</th>
                            <th>Partner</th>
                            <th>Presenter</th>
                            <th>To Follow</th>
                            <th>Released</th>
                            <th>DR Count</th>
                            {{-- <th>Status</th> --}}
                            <th>Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $row)
                            @php
                                $released = 0;
                                $to_follow = 0;
                            @endphp
                            <tr wire:click="$emit('view_order', {{ $row }})" style="cursor: pointer">
                                <td>{{ $row->oa_date }}</td>
                                <td>{{ $row->oa_number }}</td>
                                <td>{{ $row->oa_client }}</td>
                                <td>{{ $row->oa_contact }}</td>
                                <td>{{ $row->oa_consultant }}</td>
                                <td>{{ $row->oa_associate }}</td>
                                <td>{{ $row->oa_presenter }}</td>
                                <td>{{ $to_follow += $row->items()->sum('item_qty') + $row->gifts()->sum('item_qty') }}
                                </td>
                                <td>{{ $released += $row->items()->sum('released') + $row->gifts()->sum('released') }}
                                </td>
                                <td>{{ $row->drs()->count() }}</td>
                                {{-- <td>{{ $released$row->oa_status }}</td> --}}
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar @if ($row->percentage() >= 30) bg-success @else bg-danger @endif"
                                            role="progressbar" style="width: {{ $row->percentage() }}%"
                                            aria-valuenow="{{ $row->percentage() }}" aria-valuemin="0"
                                            aria-valuemax="100">
                                            {{ $row->percentage() }}%</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
            <caption>{{ $data->links() }}</caption>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Order Agreement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @error('oa_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <div class="input-group mb-2 w-25">
                        <span class="input-group-text" id="oa_date">Date</span>
                        <input type="date" class="form-control form-control-sm" wire:model.defer="oa_date">
                    </div>

                    <label class="h4">Client Details</label>
                    @error('oa_client')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <div class="input-group mb-2">
                        <span class="input-group-text" id="oa_client">Client</span>
                        <input type="text" class="form-control form-control-sm" wire:model.defer="oa_client">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            @error('oa_address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="oa_address">Address</span>
                                <input type="text" class="form-control form-control-sm"
                                    wire:model.defer="oa_address">
                            </div>
                        </div>
                        <div class="col-6">
                            @error('oa_contact')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="oa_contact">Contact</span>
                                <input type="text" class="form-control form-control-sm"
                                    wire:model.defer="oa_contact">
                            </div>
                        </div>
                    </div>

                    <label class="h4">Lifechangers Involved</label>
                    @error('oa_consultant')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <div class="input-group mb-2">
                        <span class="input-group-text" id="oa_consultant">Consultant</span>
                        <input type="text" class="form-control form-control-sm" wire:model.defer="oa_consultant">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            @error('oa_associate')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="oa_associate">Associate</span>
                                <input type="text" class="form-control form-control-sm"
                                    wire:model.defer="oa_associate">
                            </div>
                        </div>
                        <div class="col-6">
                            @error('oa_presenter')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="oa_presenter">Presenter</span>
                                <input type="text" class="form-control form-control-sm"
                                    wire:model.defer="oa_presenter">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            @error('oa_team_builder')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="oa_team_builder">Team Builder</span>
                                <input type="text" class="form-control form-control-sm"
                                    wire:model.defer="oa_team_builder">
                            </div>
                        </div>
                        <div class="col-6">
                            @error('oa_distributor')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="oa_distributor">Distributor</span>
                                <input type="text" class="form-control form-control-sm"
                                    wire:model.defer="oa_distributor">
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

@push('scripts')
    <script>
        function update_price(product_id, product_price, product_desc) {
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

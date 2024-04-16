<div class="container-fluid">
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <span class="text-uppercase fw-bolder">Merchandise Stockin History</span>
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
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Item</th>
                                    <th class="text-end pe-2">QTY</th>
                                    <th class="ps-2">Stock in By</th>
                                    <th>Remarks</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $row)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{date('F j, Y h:m A',strtotime($row->date))}}</td>
                                        <td>{{$row->item->item}}</td>
                                        <td class="text-end pe-2">{{$row->stockin_qty}}</td>
                                        <td class="ps-2">{{$row->user->username}}</td>
                                        <td>{{$row->remarks}}</td>
                                        <td>
                                            @if($row->date == date('Y-m-d'))
                                                <a href="#" class="btn btn-sm btn-danger" onclick="delete_stk('{{$row->id}}', '{{$row->stockin_qty}}', '{{$row->item->item}}')"><i class="fa-solid fa-x"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty

                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <caption>{{$data->links()}}</caption>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <span class="text-uppercase fw-bolder">Merchandise Stockin</span>
                </div>
                <div class="card-body">
                    <label for="select_item" class="form-label">Item
                        @error('product_id')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                    </label>
                    <div class="mb-3" wire:ignore>
                        <select class="select2-single w-100" id="select_item" wire:model.defer="product_id">
                                <option ></option>
                            @foreach ($merchs as $item)
                                <option value="{{$item->id}}">{{$item->item}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="qty" class="form-label">QTY
                            @error('stockin_qty')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                        </label>
                        <input type="number" class="form-control form-control-sm" wire:model.defer="stockin_qty">
                    </div>
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <input type="text" class="form-control form-control-sm" wire:model.defer="remarks">
                    </div>
                    <div class="d-flex">
                        <a href="#" class="btn btn-sm btn-primary" wire:click="submit_stk()"><i class="fa-solid fa-plus"></i> Submit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2-single').select2();
            $('#select_item').on('change', function (e) {
            var data = $('#select_item').select2("val");
            @this.set('product_id', data);
        });
        });

        function delete_stk(stk_id, qty, product_desc)
        {
            Swal.fire({
                title: '<h5> Delete Stockin of ' + ' <strong>' + product_desc + '</strong></h5>',showCancelButton: true,
                confirmButtonText: `Confirm`
            }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        Livewire.emit('delete_stk', stk_id)
                    }
            });
        }
    </script>
@endpush

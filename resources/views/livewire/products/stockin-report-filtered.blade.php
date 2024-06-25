<div class="container-fluid">
    <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>

    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <span class="text-uppercase fw-bolder">Stockin History</span>
                        <div class="d-flex w-25">
                            <div class="">
                                <button class="btn btn-sm btn-primary" onclick="ExportToExcel('xlsx')">Export</button>
                            </div>
                            <div class="col ms-2">
                                <div class="input-group">
                                    <span class="input-group-text" id="search"><i
                                            class="fa-solid fa-magnifying-glass"></i></span>
                                    <input type="text" class="form-control form-control-sm" placeholder="Search"
                                        aria-label="Search" aria-describedby="search" wire:model="search">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped table-bordered" id="table">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Product Description</th>
                                    <th class="text-end pe-2">QTY</th>
                                    <th class="ps-2">Stock in By</th>
                                    <th>Remarks</th>
                                    {{-- <th></th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ date('F j, Y h:m A', strtotime($row->date)) }}</td>
                                        <td>{{ $row->product->product_description }}</td>
                                        <td class="text-end pe-2">{{ $row->stockin_qty }}</td>
                                        <td class="ps-2">{{ $row->user->username }}</td>
                                        <td>{{ $row->remarks }}</td>
                                        {{-- <td><a href="#" class="btn btn-sm btn-danger"
                                                onclick="delete_stk('{{ $row->stockIn_id }}', '{{ $row->stockin_qty }}', '{{ $row->product->product_description }}')"><i
                                                    class="fa-solid fa-x"></i></a></td> --}}
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <span class="text-uppercase fw-bolder">Filters</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="qty" class="form-label">Date Start
                        </label>
                        <input type="date" class="form-control form-control-sm" wire:model.defer="from">
                    </div>
                    <div class="mb-3">
                        <label for="qty" class="form-label">Date End
                        </label>
                        <input type="date" class="form-control form-control-sm" wire:model.defer="to">
                    </div>
                    <label for="select_item" class="form-label">Product
                        @error('product_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </label>
                    <div class="mb-3" wire:ignore>
                        <select class="select2-single w-100" id="select_item" wire:model.defer="product_id">
                            <option></option>
                            @foreach ($products as $item)
                                <option value="{{ $item->product_id }}">{{ $item->product_description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="select_item" class="form-label">Remarks
                    </label>
                    <div class="mb-3" wire:ignore>
                        <select class="select2-single w-100" id="select_item" wire:model.defer="remarks">
                            <option></option>
                            @foreach ($marks as $mark)
                                <option value="{{ $mark->remarks }}">{{ $mark->remarks }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="">
                        <button class="btn btn-sm btn-primary" wire:click='render()'>Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@push('scripts')
    <script>
        function ExportToExcel(type, fn, dl) {
            var elt = document.getElementById('table');
            var wb = XLSX.utils.table_to_book(elt, {
                sheet: "sheet1"
            });
            return dl ?
                XLSX.write(wb, {
                    bookType: type,
                    bookSST: true,
                    type: 'base64'
                }) :
                XLSX.writeFile(wb, fn || ('StockInReport.' + (type || 'xlsx')));
        }
    </script>
@endpush

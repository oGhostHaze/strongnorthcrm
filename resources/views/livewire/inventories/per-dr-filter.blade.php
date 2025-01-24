@push('head')
    <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
@endpush

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <span class="text-uppercase fw-bolder" id="pageTitle">Inventory Report</span>
                                
                        <div class="d-flex w-75">

                            <div class="col ms-2">
                                <button onclick="ExportToExcel('xlsx')" class="btn btn-sm btn-primary"><i
                                        class="far fa-file-excel me-1"></i> Export CSV</button>
                            </div>

                            <!-- Date From Input -->
                            <div class="col ms-2">
                                <div class="input-group">
                                    <label class="input-group-text">From</label>
                                    <input type="date" class="form-control form-control-sm" wire:model="from_date">
                                </div>
                            </div>
            
                            <!-- Date To Input -->
                            <div class="col ms-2">
                                <div class="input-group">
                                    <label class="input-group-text">To</label>
                                    <input type="date" class="form-control form-control-sm" wire:model="to_date">
                                </div>
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
                        <table class="table table-sm table-hover table-striped table-bordered" id="table">
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
                                        <td colspan="11" class="text-center">No Record Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function slugify(str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim leading/trailing white space
            str = str.toLowerCase(); // convert string to lowercase
            str = str.replace(/[^a-z0-9 -]/g, '') // remove any non-alphanumeric characters
                .replace(/\s+/g, '-') // replace spaces with hyphens
                .replace(/-+/g, '-'); // remove consecutive hyphens
            return str;
        }

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
                XLSX.writeFile(wb, fn || (slugify($('#pageTitle').text()) + '.' + (type || 'xlsx')));
        }
    </script>
@endpush

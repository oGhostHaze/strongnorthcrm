<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">Order Agreements from Cooking Shows</span>
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
                            <th>CS ID</th>
                            <th>Client</th>
                            <th>Contact</th>
                            <th>Consultant</th>
                            <th>Associate</th>
                            <th>Presenter</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $oa)
                            <tr wire:key="view-oa-{{ $oa->id }}" wire:click="view_oa({{ $oa->id }})">
                                <td>{{ $oa->date }}</td>
                                <td>{{ $oa->cs_id }}</td>
                                <td>{{ $oa->client }}</td>
                                <td>{{ $oa->contact }}</td>
                                <td>{{ $oa->consultant }}</td>
                                <td>{{ $oa->associate }}</td>
                                <td>{{ $oa->presenter }}</td>
                                <td>
                                    @if ($oa->status == 'Cancelled')
                                        <span class="badge bg-danger">{{ $oa->status }}</span>
                                    @elseif ($oa->status == 'Pending')
                                        <span class="badge bg-warning">{{ $oa->status }}</span>
                                    @else
                                        <span class="badge bg-success">{{ $oa->status }}</span>
                                    @endif

                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
            <caption>{{ $orders->links() }}</caption>
        </div>
    </div>
</div>

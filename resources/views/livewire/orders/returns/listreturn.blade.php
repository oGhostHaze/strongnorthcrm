<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">Order Return Slips</span>
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
                            <th>Date</th>
                            <th>OA #</th>
                            <th>Return Slip</th>
                            <th>Client</th>
                            <th>Contact #</th>
                            <th>Lifechanger</th>
                            <th>Associate</th>
                            <th>Items Returned</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $row)
                            <tr wire:click="view_rsn({{$row->id}})" style="cursor: pointer">
                                <td>{{$row->created_at->format('F j, Y')}}</td>
                                <td>{{$row->oa_no}}</td>
                                <td>RSN-{{$row->id}}</td>
                                <td>{{$row->oa->oa_client}}</td>
                                <td>{{$row->oa->oa_contact}}</td>
                                <td>{{$row->oa->oa_consultant}}</td>
                                <td>{{$row->oa->oa_associate}}</td>
                                <td>{{$row->return_items()->sum('qty')}}</td>
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
</div>

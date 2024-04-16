<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">Employee Accounts</span>
                <div class="d-flex w-25">
                    <div class="">
                        @can('add-employee')
                            <a href="" class="btn btn-sm btn-primary">New Employee</a>
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
            <div class="row">
                <div class="col-lg-10">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th class="text-end">Contact #</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $row)
                                    <tr class="{{$model_id == $row->id ? 'table-info' : ''}}" wire:click="filter_permission({{$row->id}})">
                                        <td>{{$row->emp_name}}</td>
                                        <td>{{$row->email}}</td>
                                        <td>{{$row->username}}</td>
                                        <td class="text-end">{{$row->zContact}}</td>
                                    </tr>
                                @empty

                                @endforelse
                            </tbody>
                        </table>
                        <caption>{{$data->links()}}</caption>
                    </div>
                </div>
                <div class="col-lg-2">
                    <ul class="list-group">
                        <li class="list-group-item bg-light text-uppercase font-weight-bold">
                            Permissions
                        </li>
                        @forelse ($permissions as $permission)
                            <li class="list-group-item">
                                {{ $permission->name}}
                                @php
                                    if($model_id)
                                        $hasPermission = $model_permission->where('permission_id', $permission->id)->count();
                                @endphp
                                @if($model_id && $hasPermission != 1)
                                    <a class="btn btn-sm btn-primary float-right" wire:click="confirmAdd({{$permission->id}})"><i class="fas fa-plus"></i> Assign</a>
                                @elseif($model_id && $hasPermission == 1)
                                    <a class="btn btn-sm btn-danger float-right" wire:click="confirmRevoke({{$permission->id}})"><i class="fas fa-times"></i> Revoke</a>
                                @endif
                            </li>
                        @empty
                            @if(!$model_id)
                                <li class="list-group-item bg-danger">
                                    No permissions found
                                </li>
                            @else
                                <li class="list-group-item btn-outline-primary">
                                    All permissions are registered to this user.
                                    No permissions found
                                </li>
                            @endif
                        @endforelse
                    </ul>

                </div>
            </div>
        </div>
    </div>
</div>

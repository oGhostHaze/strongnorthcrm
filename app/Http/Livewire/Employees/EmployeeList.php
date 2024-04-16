<?php

namespace App\Http\Livewire\Employees;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ModelHasPermission;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EmployeeList extends Component
{
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['reset_all', 'revokePermission', 'addPermission'];

    public $search, $model_id, $selected;


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // $user = User::find(Auth::user()->id);
        // $user->givePermissionTo('add-employee');

        if($this->model_id){
            $model_permission = ModelHasPermission::where('model_id', $this->model_id)->get();
        }

        $permissions = Permission::all();

        $data = User::where('username', 'like', '%'.$this->search.'%')
                    ->orWhere('emp_name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('zContact', 'like', '%'.$this->search.'%')
                    ->paginate(20);

        return view('livewire.employees.employee-list', [
            'data' => $data,
            'permissions' => $permissions,
            'model_permission' => $model_permission ?? null,
        ]);
    }

    public function filter_permission($model_id)
    {
        $this->selected = User::find($model_id);
        $this->model_id = $model_id;
    }

    public function confirmAdd($permission_id)
    {
        $this->alert('info', 'Confirm add permission to user?', [
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Confirm',
            'onConfirmed' => 'addPermission',
            'data' => [$permission_id,],
            'allowOutsideClick' => false,
            'timer' => null
        ]);
    }

    public function confirmRevoke($permission_id)
    {
        $this->alert('error', 'Confirm revoke permission from user?', [
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Confirm',
            'onConfirmed' => 'revokePermission',
            'data' => [$permission_id,],
            'allowOutsideClick' => false,
            'timer' => null
        ]);
    }

    public function addPermission($data)
    {
        $permission = Permission::find($data['data'][0]);
        $this->selected->givePermissionTo($permission->name);
        $this->alert('success', 'Permission '.$permission->name.' has been assinged to '.$this->selected->name.'.');
        $this->filter_permission($this->model_id);
    }

    public function revokePermission($data)
    {
        $permission = Permission::find($data['data'][0]);
        $this->selected->revokePermissionTo($permission->name);
        $this->alert('success', 'Permission '.$permission->name.' has been revoked from '.$this->selected->name.'.');
        $this->filter_permission($this->model_id);
    }
}

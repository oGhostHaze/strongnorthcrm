<?php

namespace App\Http\Livewire\CookingShows;

use Livewire\Component;
use App\Models\CookingShow;
use Livewire\WithPagination;
use App\Models\OrderAgreement;
use Illuminate\Support\Facades\Auth;

class PendingOrders extends Component
{
    use WithPagination;

    public $search;
    public $cs_id, $oa_number, $oa_count, $oa_date, $oa_client, $oa_address, $oa_contact, $oa_consultant, $oa_associate, $oa_presenter, $oa_team_builder, $oa_distributor;

    public function updatedCsId()
    {
        $cs = CookingShow::find($this->cs_id);
        if ($cs) {
            $this->oa_date = $cs->date;
            $this->oa_client = $cs->host_fullname();
            $this->oa_address = $cs->address;
            $this->oa_contact = $cs->contact_no;
            $this->oa_consultant = $cs->lifechanger;
            $this->oa_associate = $cs->partner;
            $this->oa_presenter = $cs->presenter;
            $this->oa_team_builder = $cs->team_builder;
            $this->oa_distributor = $cs->distributor;
        } else {
            $this->resetExcept('cs_id');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orders = OrderAgreement::where('oa_number', 'like', '%' . $this->search . '%')
            ->orWhere('client', 'like', '%' . $this->search . '%')
            ->orWhere('presenter', 'like', '%' . $this->search . '%')
            ->orWhere('associate', 'like', '%' . $this->search . '%')
            ->paginate(20);
        $bookings = CookingShow::where('host', 'LIKE', '%' . $this->search . '%')
            ->where('user_id', Auth::user()->user_id)
            ->where('result', '<>', 'Booked')
            ->orderBy('date', 'DESC');

        return view('livewire.cooking-shows.pending-orders', [
            'orders' => $orders,
            'bookings' => $bookings->get(),
        ]);
    }

    public function view_oa($oa_id)
    {
        $this->redirect(route('oa.view', ['oa_id' => $oa_id]));
    }
}

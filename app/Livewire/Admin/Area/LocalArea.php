<?php

namespace App\Livewire\Admin\Area;

use App\Models\Zone;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LocalArea extends Component
{
    public $isOpen = false;
    public $zones = [];
    public $zone_id;
    public $area_id;
    public $cities;
    public $city_id;
    public $name;
    public $areas = [];

    public function render()
    {
        $this->zones = DB::table('zones')
            ->join('areas', 'zones.area_id', '=', 'areas.id')
            ->select('zones.id as zone_id', 'zones.name as zone_name', 'areas.id as area_id', 'areas.name as areas_name')
            ->get();
        return view('livewire.admin.area.local-area');
    }

    public function create()
    {
        $this->cities = DB::table('cities')->select('id', 'name')->orderBy('id', 'desc')->get();
        $this->toggle();
    }

    public function resetInput()
    {
        $this->zone_id = null;
    }

    public function updatedCityId()
    {
        $this->areas = DB::table('areas')->where('district_id', $this->city_id)->select('id', 'name')->get();
    }

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function closeModal()
    {
        $this->toggle();
    }

    public function validated()
    {
        $this->validate([
            'area_id' => 'required',
            'name' => 'required'
        ]);
    }

    public function store()
    {
        $data = $this->validate([
            'city_id' => 'required',
            'area_id' => 'required',
            'name' => 'required'
        ]);
        try {
            Zone::create($data);
            $this->toggle();
            $this->resetInput();
            session()->flash('success', 'Zone Created Successfully');
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
        }
    }
}

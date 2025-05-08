<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\HomeData;
use App\Models\HomeDataItem;

class HomeDataUser extends Component
{
    public $homeData;
    public $homeDataItems;

    public function mount()
    {
        $this->homeData = HomeData::with('items')->first();
        if ($this->homeData) {
            $this->homeDataItems = $this->homeData->items;
        }
    }

    public function render()
    {
        return view('livewire.user.home-data-user', [
            'homeData' => $this->homeData,
            'homeDataItems' => $this->homeDataItems
        ]);
    }
}

<?php

namespace App\Livewire\User;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\HeroSection;
use App\Models\Message;
use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class HomeComponent extends Component
{
    public $cities = [];
    public $areas = [];
    public $packages = [];

    public $selectedCountry;
    public $selectedCity = null;
    public $selectedArea = null;
    public $keyword = '';
    public $noPackagesFound;

    public $backgroundImage;
    public $titleSmall;
    public $titleBig;
    public $heroSection;
    protected $listeners = ['noPackagesFound', 'countryUpdated'];

    public function mount()
    {
        $this->selectedCountry = session('selectedCountry', 1); // Default to United Kingdom's ID
        $this->loadCities();
        $this->heroSection = HeroSection::first();

        $threshold = Carbon::now()->subHours(24);
        Message::where('created_at', '<', $threshold)->delete();
    }

    public function getFirstAvailablePrice($prices)
    {
        $types = ['Day', 'Week', 'Month'];
        foreach ($types as $type) {
            foreach ($prices as $price) {
                if ($price->type === $type) {
                    return [
                        'price' => $price,
                        'type' => $type
                    ];
                }
            }
        }
        return null;
    }

    public function getPriceIndicator($type)
    {
        switch ($type) {
            case 'Day':
                return '(P/N by Room)';
            case 'Week':
                return '(P/W by Room)';
            case 'Month':
                return '(P/M by Room)';
            default:
                return '';
        }
    }
    public function getPropertyPriceIndicator($type)
    {
        switch ($type) {
            case 'Day':
                return '(P/N by Property)';
            case 'Week':
                return '(P/W by Property)';
            case 'Month':
                return '(P/M by Property)';
            default:
                return '';
        }
    }



    public function loadCities()
    {
        if ($this->selectedCountry) {
            $this->cities = City::where('country_id', $this->selectedCountry)->get();
        }
    }

    public function countryUpdated($countryId)
    {
        $this->selectedCountry = $countryId;
        $this->loadCities();
        $this->selectedCity = null;
        $this->areas = [];
        $this->packages = [];
    }

    public function updatedSelectedCity($cityId)
    {
        $this->areas = Area::where('city_id', $cityId)->get();
        $this->selectedArea = null;
        $this->packages = [];
    }

    public function updatedSelectedArea($areaId)
    {
        $this->searchPackages();
    }

    public function updatedKeyword()
    {
        $this->searchPackages();
    }

    public function searchPackages()
    {
        $query = Package::query();

        if ($this->selectedCountry) {
            $query->where('country_id', $this->selectedCountry);
        }
        if ($this->selectedCity) {
            $query->where('city_id', $this->selectedCity);
        }
        if ($this->selectedArea) {
            $query->where('area_id', $this->selectedArea);
        }
        if ($this->keyword) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->keyword . '%')
                    ->orWhere('address', 'like', '%' . $this->keyword . '%');
            });
        }

        $this->packages = $query->get();

        if ($this->packages->isEmpty()) {
            $this->dispatch('noPackagesFound');
        }
    }



    public function noPackagesFound()
    {
        // Handle the event, for example, setting a flag
        $this->noPackagesFound = true;
    }


    public function register()
    {
        // Validation (you can customize this based on your requirements)
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create a new user
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        return redirect()->to('/dashboard');
    }

    public function render()
    {
        $featuredPackages = Package::with(['country', 'city', 'area', 'rooms', 'photos'])
            ->take(8)
            ->get();
        return view('livewire.user.home-component', [
            'featuredPackages' => $featuredPackages,
        ])->layout('layouts.guest');
    }
}

<?php

namespace App\Livewire\User;

use App\Models\Area;
use App\Models\City;
use App\Models\User;
use App\Models\Country;
use App\Models\Package;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class PackageList extends Component
{
    use WithPagination;

    public $selectedCountry = 1;
    public $selectedCity;
    public $selectedArea;
    public $keyword;
    public $partnerSlug;
    public $partner;

    public $countries;
    public $cities;

    public $areas;

    public function mount($partnerSlug = null)
    {
        // $this->countries = Country::all();
        $this->cities = [];
        $this->areas = [];
        $this->selectedCountry = 1;

        $this->loadCities();


        if ($partnerSlug) {
            $this->partnerSlug = $partnerSlug;
            $this->partner = User::where(function ($query) use ($partnerSlug) {
                $query->whereRaw('LOWER(REPLACE(name, " ", "-")) = ?', [strtolower($partnerSlug)]);
            })->firstOrFail();
        }
    }

    public function loadCities()
    {
        if ($this->selectedCountry) {
            $this->cities = DB::table('cities')->get();
            $this->selectedCity = null; // Reset city selection
            $this->areas = []; // Reset areas
            $this->selectedArea = null; // Reset area selection
        }
    }

    public function getPackagesProperty()
    {
        $query = Package::with([
            'country',
            'city',
            'area',
            'rooms.prices',
            'entireProperty.prices',
            'photos',
            'assignedPartner',
            'creator'
        ]);

        // If viewing partner's packages
        if ($this->partner) {
            $query->where(function ($q) {
                $q->where('assigned_to', $this->partner->id)
                    ->orWhere('user_id', $this->partner->id);
            });
        }

        return $query->when($this->selectedCity, function ($query) {
            return $query->where('city_id', $this->selectedCity);
        })
            ->when($this->selectedArea, function ($query) {
                return $query->where('area_id', $this->selectedArea);
            })
            ->when($this->keyword, function ($query) {
                return $query->where('name', 'like', '%' . $this->keyword . '%');
            })
            ->paginate(10);
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
                return '(Day Rate)';
            case 'Week':
                return '(Week Rate)';
            case 'Month':
                return '(Month Rate)';
            default:
                return '';
        }
    }

    public function getPropertyPriceIndicator($type)
    {
        switch ($type) {
            case 'Day':
                return '(Day Rate)';
            case 'Week':
                return '(Week Rate)';
            case 'Month':
                return '(Month Rate)';
            default:
                return '';
        }
    }

    public function updatedSelectedCountry($value)
    {
        if ($value) {
            $this->cities = City::where('country_id', $value)->get();
            $this->selectedCity = null;
            $this->selectedArea = null;
        } else {
            $this->cities = [];
        }
    }

    public function updatedSelectedCity($value)
    {
        if ($value) {
            $this->areas = Area::where('district_id', $value)->get();
            $this->selectedArea = null;
        } else {
            $this->areas = [];
        }
    }

    public function search()
    {
        $this->resetPage();
    }



    public function render()
    {
        $featuredPackages = Package::with(['country', 'city', 'area', 'rooms', 'photos'])
            ->take(3)
            ->get();
        return view('livewire.user.package-list', [
            'packages' => $this->packages,
            'featuredPackages' => $featuredPackages,
        ])->layout('layouts.guest');
    }
}

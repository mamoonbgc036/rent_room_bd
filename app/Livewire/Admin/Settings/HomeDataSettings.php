<?php

namespace App\Livewire\Admin\Settings;

use App\Models\HomeData;
use App\Models\HomeDataItem;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class HomeDataSettings extends Component
{
    use WithFileUploads;

    public $homeDataId;
    public $section_title;
    public $items = [];
    public $newItem = [
        'item_image' => null,
        'item_title' => '',
        'item_des' => ''
    ];

    protected $rules = [
        'section_title' => 'required|string',
        'newItem.item_image' => 'nullable|image|max:1024',
        'newItem.item_title' => 'required|string',
        'newItem.item_des' => 'required|string'
    ];

    public function mount()
    {
        $homeData = HomeData::first();
        if ($homeData) {
            $this->homeDataId = $homeData->id;
            $this->section_title = $homeData->section_title;
            $this->items = $homeData->items->toArray();
        }
    }

    public function addItem()
    {
        $this->validateOnly('newItem');

        if (isset($this->newItem['item_image']) && is_object($this->newItem['item_image'])) {
            $this->newItem['item_image'] = Storage::put('images', $this->newItem['item_image']);
        }

        $homeDataItem = HomeDataItem::create([
            'home_data_id' => $this->homeDataId,
            'item_image' => $this->newItem['item_image'],
            'item_title' => $this->newItem['item_title'],
            'item_des' => $this->newItem['item_des']
        ]);

        $this->items[] = $homeDataItem->toArray();

        $this->newItem = [
            'item_image' => null,
            'item_title' => '',
            'item_des' => ''
        ];
    }

    public function removeItem($id)
    {
        HomeDataItem::destroy($id);
        $this->items = array_filter($this->items, fn($item) => $item['id'] !== $id);
    }

    public function saveSectionTitle()
    {
        $this->validateOnly('section_title');

        $homeData = HomeData::updateOrCreate(
            ['id' => $this->homeDataId],
            ['section_title' => $this->section_title]
        );

        $this->homeDataId = $homeData->id;

        session()->flash('message', 'Section title saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.home-data-settings');
    }
}

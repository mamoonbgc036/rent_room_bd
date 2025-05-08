<?php

namespace App\Livewire\Admin\Settings;

use App\Models\PartnerTermsCondition;
use Livewire\Component;

class ParetnerTermsConditionSettings extends Component
{
    public $termsConditions;
    public $title;
    public $content;
    public $partnerTermsConditionId;
    public $isEdit = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string'
    ];

    public function mount()
    {
        $this->loadTermsConditions();
    }

    public function loadTermsConditions()
    {
        $this->termsConditions = PartnerTermsCondition::all();
    }

    public function resetForm()
    {
        $this->title = '';
        $this->content = '';
        $this->partnerTermsConditionId = null;
        $this->isEdit = false;
    }

    public function edit($id)
    {
        $termsCondition = PartnerTermsCondition::findOrFail($id);
        $this->partnerTermsConditionId = $termsCondition->id;
        $this->title = $termsCondition->title;
        $this->content = $termsCondition->content;
        $this->isEdit = true;
    }

    public function save()
    {
        $this->validate();

        PartnerTermsCondition::updateOrCreate(
            ['id' => $this->partnerTermsConditionId],
            ['title' => $this->title, 'content' => $this->content]
        );

        session()->flash('message', $this->isEdit ? 'Terms and Conditions updated successfully.' : 'Terms and Conditions created successfully.');

        $this->resetForm();
        $this->loadTermsConditions();
    }

    public function delete($id)
    {
        PartnerTermsCondition::destroy($id);

        session()->flash('message', 'Terms and Conditions deleted successfully.');

        $this->loadTermsConditions();
    }

    public function render()
    {
        return view('livewire.admin.settings.paretner-terms-condition-settings');
    }
}

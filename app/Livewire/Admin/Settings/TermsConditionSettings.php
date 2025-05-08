<?php

namespace App\Livewire\Admin\Settings;

use App\Models\TermsCondition;
use Livewire\Component;

class TermsConditionSettings extends Component
{
    public $termsConditions;
    public $title;
    public $content;
    public $termsConditionId;
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
        $this->termsConditions = TermsCondition::all();
    }

    public function resetForm()
    {
        $this->title = '';
        $this->content = '';
        $this->termsConditionId = null;
        $this->isEdit = false;
    }

    public function edit($id)
    {
        $termsCondition = TermsCondition::findOrFail($id);
        $this->termsConditionId = $termsCondition->id;
        $this->title = $termsCondition->title;
        $this->content = $termsCondition->content;
        $this->isEdit = true;
    }

    public function save()
    {
        $this->validate();

        TermsCondition::updateOrCreate(
            ['id' => $this->termsConditionId],
            ['title' => $this->title, 'content' => $this->content]
        );

        session()->flash('message', $this->isEdit ? 'Terms and Conditions updated successfully.' : 'Terms and Conditions created successfully.');

        $this->resetForm();
        $this->loadTermsConditions();
    }

    public function delete($id)
    {
        TermsCondition::destroy($id);

        session()->flash('message', 'Terms and Conditions deleted successfully.');

        $this->loadTermsConditions();
    }

    public function render()
    {
        return view('livewire.admin.settings.terms-condition-settings');
    }
}

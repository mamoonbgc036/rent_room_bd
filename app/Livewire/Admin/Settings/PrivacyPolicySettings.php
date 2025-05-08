<?php

namespace App\Livewire\Admin\Settings;

use App\Models\PrivacyPolicy;
use Livewire\Component;

class PrivacyPolicySettings extends Component
{
    public $privacyPolicies;
    public $title;
    public $content;
    public $privacyPolicyId;
    public $isEdit = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string'
    ];

    public function mount()
    {
        $this->loadPrivacyPolicies();
    }

    public function loadPrivacyPolicies()
    {
        $this->privacyPolicies = PrivacyPolicy::all();
    }

    public function resetForm()
    {
        $this->title = '';
        $this->content = '';
        $this->privacyPolicyId = null;
        $this->isEdit = false;
    }

    public function edit($id)
    {
        $privacyPolicy = PrivacyPolicy::findOrFail($id);
        $this->privacyPolicyId = $privacyPolicy->id;
        $this->title = $privacyPolicy->title;
        $this->content = $privacyPolicy->content;
        $this->isEdit = true;
    }

    public function save()
    {
        $this->validate();

        PrivacyPolicy::updateOrCreate(
            ['id' => $this->privacyPolicyId],
            ['title' => $this->title, 'content' => $this->content]
        );

        session()->flash('message', $this->isEdit ? 'Privacy Policy updated successfully.' : 'Privacy Policy created successfully.');

        $this->resetForm();
        $this->loadPrivacyPolicies();
    }

    public function delete($id)
    {
        PrivacyPolicy::destroy($id);

        session()->flash('message', 'Privacy Policy deleted successfully.');

        $this->loadPrivacyPolicies();
    }

    public function render()
    {
        return view('livewire.admin.settings.privacy-policy-settings');
    }

}

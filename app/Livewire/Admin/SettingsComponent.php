<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\FooterSection4;
use App\Models\Header;
use App\Models\Footer;
use App\Models\FooterSectionFour;
use App\Models\FooterSectionThree;
use App\Models\FooterSectionTwo;
use App\Models\HeroSection;
use App\Models\HomeData;
use App\Models\HomeDataItem;
use App\Models\PrivacyPolicy;
use App\Models\TermsAndPrivacy;
use App\Models\TermsCondition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class SettingsComponent extends Component
{
    use WithFileUploads;

    public $newLogo, $backgroundImage, $titleSmall, $titleBig;
    public $footerLogo, $address, $email, $contactNumber, $website;
    public $section, $title, $link, $socialIconClass;
    public $section4Title, $socialLinkDescription, $socialLink;

    public $header, $hero, $footer, $footerLink, $footerSection4;
    public $headerId, $headerLogo, $heroId, $footerId, $footerSection4Id;
    public $termsTitle, $termsLink, $privacyTitle, $privacyLink, $rightsReservesText;

    public $updateMode = false;
    public $footerSectionThrees = [];
    public $footerSectionTwos = [];
    public $footerSectionFourId;
    public $description;
    public $socialLinks = [];
    public $newSocialLinkIconClass;
    public $newSocialLink;

    public $homeData;
    public $sectionTitle;
    public $items = [];
    public $itemImages = [];

    public $privacyPolicyId;
    public $ppTitle;
    public $ppContent;
    public $privacyPolicy;
    public $termsConditionId;

    public $tcTitle;
    public $tcContent;
    public $termsCondition;
    


    protected $rules = [
        'newLogo' => 'image|max:1024',
        'titleSmall' => 'required',
        'titleBig' => 'required',
        'termsTitle' => 'nullable|string',
        'termsLink' => 'nullable|url',
        'privacyTitle' => 'nullable|string',
        'privacyLink' => 'nullable|url',
        'rightsReservesText' => 'nullable|string',
    ];

    public function mount()
    {
        $this->header = Header::first();
        if($this->header){
            $this->headerId = $this->header->id;
            $this->headerLogo = $this->header->logo;
        }
        $this->hero = HeroSection::first();
        $this->backgroundImage = $this->hero->background_image;
        $this->titleSmall = $this->hero->title_small;
        $this->titleBig = $this->hero->title_big;
        $this->updateMode = true;
        $this->footer = Footer::first();
        if ($this->footer) {
            $this->footerId = $this->footer->id;
            $this->footerLogo = $this->footer->logo;
            $this->address = $this->footer->address;
            $this->email = $this->footer->email;
            $this->contactNumber = $this->footer->contact_number;
            $this->website = $this->footer->website;
            $this->termsTitle = $this->footer->terms_title;
            $this->termsLink = $this->footer->terms_link;
            $this->privacyTitle = $this->footer->privacy_title;
            $this->privacyLink = $this->footer->privacy_link;
            $this->rightsReservesText = $this->footer->rights_reserves_text;
        }

        $this->footer = Footer::first();
        $existingFooterSectionThrees = FooterSectionThree::where('footer_id', $this->footer->id)->get();
        $existingFooterSectionTwos = FooterSectionTwo::where('footer_id', $this->footer->id)->get();
        $this->footerSectionThrees = $existingFooterSectionThrees->toArray();
        $this->footerSectionTwos = $existingFooterSectionTwos->toArray();

        $footerSectionFour = FooterSectionFour::first();
        if ($footerSectionFour) {
            $this->footerSectionFourId = $footerSectionFour->id;
            $this->title = $footerSectionFour->title;
            $this->description = $footerSectionFour->description;
            $this->socialLinks = $footerSectionFour->socialLinks->toArray();
        }

        $this->homeData = HomeData::with('items')->first();

        if ($this->homeData) {
            $this->sectionTitle = $this->homeData->section_title;
            $this->items = $this->homeData->items->toArray();
        }

        $this->privacyPolicy = PrivacyPolicy::first();
        if ($this->privacyPolicy) {
            $this->ppTitle = $this->privacyPolicy->title;
            $this->ppContent = $this->privacyPolicy->content;
        }

        $this->termsCondition = TermsCondition::first();
        if ($this->termsCondition) {
            $this->tcTitle = $this->termsCondition->title;
            $this->tcContent = $this->termsCondition->content;
        }

    }

    public function render()
    {
        return view('livewire.admin.settings-component', [
            'footerSection4s' => FooterSection4::all(),
            'privacyPolicies' => PrivacyPolicy::all(),
            'termsConditions' => TermsCondition::all(),
        ]);
    }

    public function storeOrUpdateHeader()
    {
        $this->validate([
            'newLogo' => 'required|image|max:1024',
        ]);

        if ($this->header) {
            $this->updateHeader();
        } else {
            $this->storeHeader();
        }
    }

    public function storeHeader()
    {
        $logoPath = $this->newLogo->store('public/logos');
        $relativePath = str_replace('public/', '', $logoPath);

        Header::create(['logo' => $relativePath]);
        flash()->success('Header Created Successfully.');
        $this->resetInputFields();
    }
    public function updateHeader()
    {
        $this->deletePreviousLogo($this->header->logo);

        $logoPath = $this->newLogo->store('public/logos');
        $relativePath = str_replace('public/', '', $logoPath);

        $this->header->update(['logo' => $relativePath]);

        flash()->success('Header Updated Successfully.');
        $this->resetInputFields();
    }

    private function deletePreviousLogo($logoPath)
    {
        if ($logoPath) {
            $path = str_replace('storage', 'public', parse_url($logoPath)['path']);
            \Storage::delete($path);
        }
    }

    public function storeOrUpdateHero()
{
    $rules = [
        'titleSmall' => 'required',
        'titleBig' => 'required',
    ];


    $validatedData = $this->validate($rules);

    if ($this->hero) {
        $this->updateHero();
    } else {
        $this->storeHero();
    }
}

public function storeHero()
{
    $relativePath = $this->storeImage('backgroundImage', 'public/hero_images');

    HeroSection::create([
        'background_image' => $relativePath,
        'title_small' => $this->titleSmall,
        'title_big' => $this->titleBig,
    ]);

    flash()->success('Hero Section Created Successfully.');
    $this->resetInputFields();
}

public function updateHero()
{
    $data = [
        'title_small' => $this->titleSmall,
        'title_big' => $this->titleBig,
    ];

    if ($this->backgroundImage instanceof \Illuminate\Http\UploadedFile) {
        $this->deletePreviousImage($this->hero->background_image);
        $data['background_image'] = $this->storeImage('backgroundImage', 'public/hero_images');
    }

    $this->hero->update($data);

    flash()->success('Hero Section Updated Successfully!');
    $this->resetInputFields();
}

private function storeImage($imageName, $storagePath)
{
    return str_replace('public/', '', $this->$imageName->store($storagePath));
}

private function deletePreviousImage($path)
{
    if ($path && Storage::exists('public/' . $path)) {
        Storage::delete('public/' . $path);
    }
}


    public function storeOrUpdateFooter()
    {
        $this->validate([

            'address' => 'required',
            'email' => 'required|email',
            'contactNumber' => 'required',
            'website' => 'required',
        ]);

        if ($this->footer) {
            $this->updateFooter();
        } else {
            $this->storeFooter();
        }
    }

    public function storeFooter()
    {
        $this->validate([
// Ensure file is an image and max 1MB
        ]);
            if ($this->backgroundImage instanceof \Illuminate\Http\UploadedFile) {
        $this->deletePreviousImage($this->hero->background_image);
        $data['background_image'] = $this->storeImage('backgroundImage', 'public/hero_images');
    }

        $logoPath = $this->footerLogo->store('public/logos');
        $relativePath = str_replace('public/', '', $logoPath);

        Footer::create([
            'footer_logo' => $relativePath,
            'address' => $this->address,
            'email' => $this->email,
            'contact_number' => $this->contactNumber,
            'website' => $this->website,
            'terms_title' => $this->termsTitle,
            'terms_link' => $this->termsLink,
            'privacy_title' => $this->privacyTitle,
            'privacy_link' => $this->privacyLink,
            'rights_reserves_text' => $this->rightsReservesText,
        ]);

        session()->flash('message', 'Footer Created Successfully.');
        $this->resetInputFields();
    }

    public function updateFooter()
    {
        $data = [
            'address' => $this->address,
            'email' => $this->email,
            'contact_number' => $this->contactNumber,
            'website' => $this->website,
            'terms_title' => $this->termsTitle,
            'terms_link' => $this->termsLink,
            'privacy_title' => $this->privacyTitle,
            'privacy_link' => $this->privacyLink,
            'rights_reserves_text' => $this->rightsReservesText,
        ];

        if ($this->footerLogo instanceof \Illuminate\Http\UploadedFile) {
            $this->deletePreviousLogo($this->footer->footer_logo);
            $logoPath = $this->footerLogo->store('public/logos');
            $data['footer_logo'] = str_replace('public/', '', $logoPath);
        }

        $this->footer->update($data);

        session()->flash('message', 'Footer Updated Successfully.');
        $this->resetInputFields();
    }



    public function addLink($section)
    {
        if ($section == 'twos') {
            $this->footerSectionTwos[] = ['title' => '', 'link' => ''];
        } elseif ($section == 'threes') {
            $this->footerSectionThrees[] = ['title' => '', 'link' => ''];
        }
    }

    public function removeLink($section, $index)
    {
        if ($section == 'twos') {
            unset($this->footerSectionTwos[$index]);
            $this->footerSectionTwos = array_values($this->footerSectionTwos); // Reset array keys
        } elseif ($section == 'threes') {
            unset($this->footerSectionThrees[$index]);
            $this->footerSectionThrees = array_values($this->footerSectionThrees); // Reset array keys
        }
    }


    public function addItem()
    {
        $this->items[] = ['item_image' => null, 'item_title' => '', 'item_des' => ''];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function saveHomeData()
    {
        $this->validate([
            'sectionTitle' => 'required',
            'items.*.item_title' => 'required',
            'items.*.item_des' => 'required',
            'itemImages.*' => 'image|max:1024', // Validate each image
        ]);

        $homeData = HomeData::updateOrCreate(
            ['id' => $this->homeData->id ?? null],
            ['section_title' => $this->sectionTitle]
        );

        foreach ($this->items as $index => $item) {
            if (isset($this->itemImages[$index])) {
                $path = $this->itemImages[$index]->store('public/home_data');
                $item['item_image'] = str_replace('public/', '', $path);
            }

            HomeDataItem::updateOrCreate(
                ['id' => $item['id'] ?? null, 'home_data_id' => $homeData->id],
                $item
            );
        }

        flash()->success('Home Data saved successfully.');

        return redirect()->back();
    }

    public function storeOrUpdateFooterSectionTwos()
    {
        foreach ($this->footerSectionTwos as $index => $link) {
            FooterSectionTwo::updateOrCreate(
                [
                    'footer_id' => $this->footer->id,
                    'id' => $link['id'] ?? null,
                ],
                [
                    'title' => $link['title'],
                    'link' => $link['link'],
                ]
            );
        }
        flash()->success('Footer Section Twos saved successfully.');
    }

    public function storeOrUpdateFooterSectionThrees()
    {
        foreach ($this->footerSectionThrees as $index => $link) {
            FooterSectionThree::updateOrCreate(
                [
                    'footer_id' => $this->footer->id,
                    'id' => $link['id'] ?? null,
                ],
                [
                    'title' => $link['title'],
                    'link' => $link['link'],
                ]
            );
        }

        flash()->success('Footer Section Threes saved successfully!');
    }

    public function storeOrUpdatePrivacyPolicy()
    {
        $this->validate([
            'ppTitle' => 'required|string|max:255',
            'ppContent' => 'required|string',
        ]);

        if ($this->privacyPolicy) {
            $this->updatePrivacyPolicy();
        } else {
            $this->storePrivacyPolicy();
        }
    }

    public function storePrivacyPolicy()
    {
        PrivacyPolicy::create([
            'title' => $this->ppTitle,
            'content' => $this->ppContent,
        ]);

        flash()->success('Privacy Policy Created Successfully.');
        $this->resetInputFields();
    }

    public function updatePrivacyPolicy()
    {
        $this->privacyPolicy->update([
            'title' => $this->ppTitle,
            'content' => $this->ppContent,
        ]);

        flash()->success('Privacy Policy Updated Successfully.');
        $this->resetInputFields();
    }


    public function storeOrUpdateTermsCondition()
    {
        $this->validate([
            'tcTitle' => 'required|string|max:255',
            'tcContent' => 'required|string',
        ]);

        if ($this->termsCondition) {
            $this->updateTermsCondition();
        } else {
            $this->storeTermsCondition();
        }
    }

    public function storeTermsCondition()
    {
        TermsCondition::create([
            'title' => $this->tcTitle,
            'content' => $this->tcContent,
        ]);

        flash()->success('Terms Condition Created Successfully.');
        $this->resetInputFields();
    }

    public function updateTermsCondition()
    {
        $this->termsCondition->update([
            'title' => $this->tcTitle,
            'content' => $this->tcContent,
        ]);

        flash()->success('Terms Condition Updated Successfully.');
        $this->resetInputFields();
    }



    public function saveFooterSection()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'socialLinks.*.icon_class' => 'required|string|max:255',
            'socialLinks.*.link' => 'required|url|max:255',
        ]);

        if ($this->footerSectionFourId) {
            $footerSectionFour = FooterSectionFour::find($this->footerSectionFourId);
            $footerSectionFour->update([
                'title' => $this->title,
                'description' => $this->description,
            ]);
        } else {
            $footerSectionFour = FooterSectionFour::create([
                'title' => $this->title,
                'description' => $this->description,
            ]);
            $this->footerSectionFourId = $footerSectionFour->id;
        }

        // Delete old social links
        $footerSectionFour->socialLinks()->delete();

        // Save new social links
        foreach ($this->socialLinks as $socialLink) {
            $footerSectionFour->socialLinks()->create($socialLink);
        }

        flash()->success('Footer Section and Social Links updated successfully.');
    }

    public function addSocialLink()
    {
        $this->socialLinks[] = [
            'icon_class' => '',
            'link' => '',
        ];
    }

    public function removeSocialLink($index)
    {
        unset($this->socialLinks[$index]);
        $this->socialLinks = array_values($this->socialLinks);
    }

    private function resetInputFields()
    {
        $this->newLogo = '';
        $this->backgroundImage = '';
        $this->titleSmall = '';
        $this->titleBig = '';
        $this->footerLogo = '';
        $this->address = '';
        $this->email = '';
        $this->contactNumber = '';
        $this->website = '';
        $this->section = '';
        $this->title = '';
        $this->link = '';
        $this->socialIconClass = '';
        $this->section4Title = '';
        $this->socialLinkDescription = '';
        $this->socialLink = '';
        $this->footerLink = null;
        $this->footerSection4 = null;
    }
}

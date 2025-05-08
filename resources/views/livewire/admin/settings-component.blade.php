<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h2>Settings</h2>
            @if (session()->has('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif
        </div>

        <!-- Header Form -->
        <div class="col-lg-4 col-md-6 my-4">
            <div class="card shadow-sm border rounded p-3">
                <h3>Header</h3>
                <form wire:submit.prevent="storeOrUpdateHeader" wire:loading.class="opacity-50">
                    <div class="form-group">
                        <label for="headerLogo">Logo</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="headerLogo" wire:model="newLogo">
                            <label class="custom-file-label" for="headerLogo">
                                @if ($newLogo)
                                    {{-- {{ $newLogo->getClientOriginalName() }} --}}
                                @else
                                    Choose file...
                                @endif
                            </label>
                        </div>
                        @error('newLogo') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    @if ($header)
                        <div class="my-3">
                            <img src="{{ asset('storage/' . $header->logo) }}" class="img-thumbnail" style="max-width: 200px;" alt="Header Logo">
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove>{{ $header ? 'Update' : 'Save' }}</span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Hero Form -->
        <div class="col-lg-4 col-md-6 my-4">
            <div class="card shadow-sm border rounded p-3">
                <h3>Hero Section</h3>
                <form wire:submit.prevent="storeOrUpdateHero" wire:loading.class="opacity-50">
                    <div class="form-group">
                        <label for="backgroundImage">Background Image</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="backgroundImage" wire:model="backgroundImage">
                            <label class="custom-file-label" for="backgroundImage">
                                @if ($backgroundImage instanceof \Illuminate\Http\UploadedFile)
                                    {{ $backgroundImage->getClientOriginalName() }}
                                @elseif (is_string($backgroundImage))
                                    {{ basename($backgroundImage) }} <!-- Display just the filename -->
                                @else
                                    Choose file...
                                @endif
                            </label>
                        </div>                                               
                        @error('backgroundImage') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="titleSmall">Title Small</label>
                        <input type="text" id="titleSmall" class="form-control" wire:model="titleSmall">
                        @error('titleSmall') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="titleBig">Title Big</label>
                        <input type="text" id="titleBig" class="form-control" wire:model="titleBig">
                        @error('titleBig') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    @if ($hero)
                        <div class="my-3">
                            <img src="{{ asset('storage/' . $hero->background_image) }}" class="img-thumbnail" style="max-width: 200px;" alt="Hero Background">
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove>{{ $hero ? 'Update' : 'Save' }}</span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Privacy Policy Section -->
        <div class="col-lg-4 col-md-6 my-4">
            <div class="card shadow-sm border rounded p-3">
                <h3>Privacy Policy</h3>
                <form wire:submit.prevent="storeOrUpdatePrivacyPolicy" wire:loading.class="opacity-50">
                    <div class="form-group">
                        <label for="ppTitle">Title</label>
                        <input type="text" id="ppTitle" class="form-control" wire:model="ppTitle">
                        @error('ppTitle') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="ppContent">Content</label>
                        <textarea id="ppContent" class="form-control" rows="5" wire:model="ppContent"></textarea>
                        @error('ppContent') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                

                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove>{{ $privacyPolicies ? 'Update' : 'Save' }}</span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </span>
                    </button>
                </form>
            </div>
        </div>


        <!-- Footer Form -->
        <div class="col-lg-4 col-md-6 my-4">
            <div class="card shadow-sm border rounded p-3">
                <form wire:submit.prevent="storeOrUpdateFooter" wire:loading.class="opacity-50">
                    <div class="form-group">
                        <label for="footerLogo">Logo</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="footerLogo" wire:model="footerLogo">
                            <label class="custom-file-label" for="footerLogo">
                                @if ($footerLogo instanceof \Illuminate\Http\UploadedFile)
                                    {{ $footerLogo->getClientOriginalName() }}
                                @elseif (is_string($footerLogo))
                                    {{ basename($footerLogo) }} <!-- Display just the filename -->
                                @else
                                    Choose file...
                                @endif
                            </label>
                        </div>
                        @error('footerLogo') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    @if ($footer && $footer->footer_logo)
                        <div class="my-3">
                            <img src="{{ asset('storage/' . $footer->footer_logo) }}" class="img-thumbnail" style="max-width: 200px;" alt="Footer Logo">
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" class="form-control" wire:model="address">
                        @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" class="form-control" wire:model="email">
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="contactNumber">Contact Number</label>
                        <input type="text" id="contactNumber" class="form-control" wire:model="contactNumber">
                        @error('contactNumber') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="website">WhatsApp Number</label>
                        <input type="number" id="website" class="form-control" wire:model="website">
                        @error('website') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="termsTitle">Terms Title</label>
                        <input type="text" id="termsTitle" class="form-control" wire:model="termsTitle">
                        @error('termsTitle') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="termsLink">Terms Link</label>
                        <input type="url" id="termsLink" class="form-control" wire:model="termsLink">
                        @error('termsLink') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="privacyTitle">Privacy Title</label>
                        <input type="text" id="privacyTitle" class="form-control" wire:model="privacyTitle">
                        @error('privacyTitle') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="privacyLink">Privacy Link</label>
                        <input type="url" id="privacyLink" class="form-control" wire:model="privacyLink">
                        @error('privacyLink') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="rightsReservesText">Rights Reserves Text</label>
                        <textarea id="rightsReservesText" class="form-control" wire:model="rightsReservesText"></textarea>
                        @error('rightsReservesText') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove>{{ $footer ? 'Update' : 'Save' }}</span>
                        <span wire:loading>Loading...</span>
                    </button>
                </form>
            </div>
        </div>


        <div class="col-lg-4 col-md-6 my-4">
            <div class="card shadow-sm border rounded p-3">
                <h2>Home Data</h2>
                <form wire:submit.prevent="saveHomeData" class="form">
                    <div class="form-group mb-4">
                        <label for="sectionTitle">Section Title</label>
                        <input wire:model="sectionTitle" type="text" class="form-control" id="sectionTitle" required placeholder="Section Title">
                        @error('sectionTitle') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
            
                    @foreach ($items as $index => $item)
                        <div class="form-group mb-4" wire:key="item-{{ $index }}">
                            <label for="itemTitle{{ $index }}">Item Title</label>
                            <input wire:model="items.{{ $index }}.item_title" type="text" class="form-control" id="itemTitle{{ $index }}" required placeholder="Item Title">
                            @error('items.'.$index.'.item_title') <span class="text-danger">{{ $message }}</span> @enderror
            
                            <label for="itemDes{{ $index }}" class="mt-2">Item Description</label>
                            <textarea wire:model="items.{{ $index }}.item_des" class="form-control" id="itemDes{{ $index }}" required placeholder="Item Description"></textarea>
                            @error('items.'.$index.'.item_des') <span class="text-danger">{{ $message }}</span> @enderror
            
                            <label for="itemImage{{ $index }}" class="mt-2">Item Image</label>
                            <input type="file" wire:model="itemImages.{{ $index }}" class="form-control-file" id="itemImage{{ $index }}">
                            @error('itemImages.'.$index) <span class="text-danger">{{ $message }}</span> @enderror
            
                            @if (isset($items[$index]['item_image']) && $items[$index]['item_image'])
                                <img src="{{ asset('storage/' . $items[$index]['item_image']) }}" class="img-thumbnail mt-2" style="max-width: 200px;" alt="Item Image">
                            @endif
            
                            <button type="button" class="btn btn-danger mt-2" wire:click.prevent="removeItem({{ $index }})">Remove</button>
                        </div>
                    @endforeach
            
                    <button type="button" class="btn btn-primary mb-4" wire:click.prevent="addItem">Add Item</button>
            
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Save</button>
                </form>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 my-4">
            <div class="card shadow-sm border rounded p-3">
                <h3>Terms and Conditions</h3>
                <form wire:submit.prevent="storeOrUpdateTermsCondition" wire:loading.class="opacity-50">
                    <div class="form-group">
                        <label for="tcTitle">Title</label>
                        <input type="text" id="tcTitle" class="form-control" wire:model="tcTitle">
                        @error('tcTitle') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="tcContent">Content</label>
                        <textarea id="tcContent" class="form-control" rows="5" wire:model="tcContent"></textarea>
                        @error('tcContent') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                

                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove>{{ $termsConditions ? 'Update' : 'Save' }}</span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 my-4">
            <div class="card shadow-sm border rounded p-3">
                <h2>Footer Section 2</h2>
                <form wire:submit.prevent="storeOrUpdateFooterSectionTwos" wire:loading.class="opacity-50">
                    <div class="row">
                        @foreach ($footerSectionTwos as $index => $link)
                            <div class="col-lg-6 mt-3">
                                <div class="form-group">
                                    <label for="titleTwos{{ $index }}">Title</label>
                                    <input type="text" id="titleTwos{{ $index }}" class="form-control"
                                        wire:model="footerSectionTwos.{{ $index }}.title">
                                </div>
                                <div class="form-group">
                                    <label for="linkTwos{{ $index }}">Link</label>
                                    <input type="url" id="linkTwos{{ $index }}" class="form-control"
                                        wire:model="footerSectionTwos.{{ $index }}.link">
                                </div>

                                <!-- Button to remove link -->
                                <div>
                                    <button type="button" class="btn btn-sm btn-danger" wire:click="removeLink('twos', {{ $index }})">
                                        Remove Link
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Button to add link -->
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-primary" wire:click="addLink('twos')">
                            Add Link
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove>Save</span>
                            <span wire:loading>Loading...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 my-4">
            <div class="card shadow-sm border rounded p-3">
                <h2 class="mt-5">Footer Section 3</h2>
                <form wire:submit.prevent="storeOrUpdateFooterSectionThrees" wire:loading.class="opacity-50">
                    <div class="row">
                        @foreach ($footerSectionThrees as $index => $link)
                            <div class="col-lg-6 mt-3">
                                <div class="form-group">
                                    <label for="titleThrees{{ $index }}">Title</label>
                                    <input type="text" id="titleThrees{{ $index }}" class="form-control"
                                        wire:model="footerSectionThrees.{{ $index }}.title">
                                </div>
                                <div class="form-group">
                                    <label for="linkThrees{{ $index }}">Link</label>
                                    <input type="url" id="linkThrees{{ $index }}" class="form-control"
                                        wire:model="footerSectionThrees.{{ $index }}.link">
                                </div>

                                <!-- Button to remove link -->
                                <div>
                                    <button type="button" class="btn btn-sm btn-danger" wire:click="removeLink('threes', {{ $index }})">
                                        Remove Link
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Button to add link -->
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-primary" wire:click="addLink('threes')">
                            Add Link
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove>Save</span>
                            <span wire:loading>Loading...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer Section 4 Form -->
        <div class="col-lg-3 col-md-6 my-4">
            <div class="card shadow-sm border rounded p-3">
                <h3>Footer Section 4</h3>
                <form wire:submit.prevent="saveFooterSection" wire:loading.class="opacity-50">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" class="form-control" wire:model="title">
                        @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" id="description" class="form-control" wire:model="description">
                        @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <h4 class="mt-4">Social Links</h4>
                    @foreach($socialLinks as $index => $socialLink)
                        <div class="form-row align-items-center mb-3">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="Icon Class" wire:model="socialLinks.{{ $index }}.icon_class">
                                @error('socialLinks.' . $index . '.icon_class') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" placeholder="Link" wire:model="socialLinks.{{ $index }}.link">
                                @error('socialLinks.' . $index . '.link') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-danger btn-sm" wire:click.prevent="removeSocialLink({{ $index }})">Remove</button>
                            </div>
                        </div>
                    @endforeach

                    <button class="btn btn-secondary mt-3" wire:click.prevent="addSocialLink">Add Social Link</button>

                    <button type="submit" class="btn btn-primary mt-3">
                        <span wire:loading.remove>Save</span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
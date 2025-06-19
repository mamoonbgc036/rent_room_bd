<div>
    @if($isSearchComplete)
    <section class="d-flex flex-column">
        <div style="background-image: url('{{ asset('storage/' . $heroSection?->background_image) }}');"
            class="bg-cover d-flex align-items-center custom-vh-60" wire:ignore.self>
            <div wire:ignore.self class="container pt-lg-4 py-4" data-animate="zoomIn">
                <p class="text-white fs-md-20 fs-16 font-weight-500 letter-spacing-367 mb-2 text-center text-uppercase appealing">{{$heroSection?->title_small}}</p>
                <h2 class="text-white display-2 text-center mb-sm-4 mb-4 appealing">
                    {{$heroSection?->title_big }}
                </h2>
                <div class="container mb-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="row g-1">
                                <div class="col-12">
                                    <input type="search" placeholder="Type your place" wire:model.live="search" name="" class="form-control" style="background-color: rgba(255, 255, 255, 0.7); text-align: center; ::placeholder { color: white; text-align: center; }" id="">
                                </div>
                            </div>
                            @if (!empty($search_area))
                            <ul
                                class="position-absolute list-group w-100 z-3"
                                style="max-height: 300px; overflow-y: auto;margin-top: 5px">
                                @foreach ($search_area as $area)
                                <li
                                    class="list-group-item list-group-item-action"
                                    wire:click="selectPackage({{ is_null($area->district_id) ? "'th/".$area->id."'" : "'di/".$area->district_id."'" }})"
                                    style="cursor: pointer;">
                                    {{ $area->name }}
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    @if($packages)
    <div>
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6" style="margin-top: 100px;">
            🔍 Select Your Package
        </h1>
        <div class="d-flex justify-content-between m-2">
            <div>
                <label for="local_area" class="d-block text-center" style="margin-bottom: 0px;">Local Area</label>
                <select name="local_area" wire:model.live="zone_id" id="local_area" class="form-control-sm w-100">
                    <option value="" selected>Select Local Area</option>
                    @foreach($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="accomodation_type" class="d-block text-center" style="margin-bottom: 0px;">Accomodation Type</label>
                <select name="property_type" wire:model.live="accomodationType" id="property_type" class="form-control-sm w-100">
                    <option value="" selected>Select Accomodation Type</option>
                    @foreach($propertyTypes as $propertyType)
                    <option value="{{ $propertyType->id }}" class="form-control-sm">{{ $propertyType->type }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div id="filterPackage" class="container" style="margin-top: 60px;">
        <div class="row justify-content-center">
            @if($packages->isNotEmpty())
            @foreach($packages as $package)
            <div class="col-md-4 mb-4">
                <a href="{{ $package->getShowUrl() }}" class="text-decoration-none">
                    <div class="card py-3">
                        <div class="position-relative hover-change-image bg-hover-overlay rounded-lg card-img">
                            @if($package->photos->isNotEmpty() && file_exists('storage/'.$package->photos->first()->url))
                            <img src="{{ asset('storage/'.$package->photos->first()->url) }}" alt="Thumbnail" class="img-thumbnail">
                            @else
                            <img src="{{ asset('images/no_image.png') }}" alt="Thumbnail" class="img-thumbnail">
                            @endif
                        </div>
                        <div class="card-body pt-3 px-3 pb-1">
                            <h2 class="fs-16 mb-1">
                                {{ $package->name }}
                            </h2>
                            <p class="font-weight-500 text-gray-light mb-0">
                                {{ $package->address }}
                            </p>
                            @php
                            $roomPrices = $package->rooms->flatMap(function($room) {
                            return $room->prices;
                            });

                            $roomPriceData = $this->getFirstAvailablePrice($roomPrices);
                            $roomPrice = $roomPriceData['price'] ?? null;
                            $roomPriceType = $roomPriceData['type'] ?? null;
                            $roomPriceIndicator = $roomPriceType ? $this->getPriceIndicator($roomPriceType) : '';

                            $propertyPrices = $package->entireProperty->prices ?? [];
                            $propertyPriceData = $this->getFirstAvailablePrice($propertyPrices);
                            $propertyPrice = $propertyPriceData['price'] ?? null;
                            $propertyPriceType = $propertyPriceData['type'] ?? null;
                            $propertyPriceIndicator = $propertyPriceType ? $this->getPropertyPriceIndicator($propertyPriceType) : '';
                            @endphp

                            @if($propertyPrice)
                            @if($propertyPrice->discount_price)
                            <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                <del class="text-muted mr-2"> ৳{{ $propertyPrice->fixed_price }}</del>
                                <span class="fs-17 font-weight-bold text-heading mb-0 lh-16"> ৳{{ $propertyPrice->discount_price }}</span>
                                <span class="price-indicate">{{($propertyPriceIndicator)}}</span>
                            </p>
                            @else
                            <p class="fs-17 font-weight-bold text-heading mb-0 lh-16"> ৳{{ $propertyPrice->fixed_price }}<span class="price-indicate">(p/n by property)</span></p>
                            @endif
                            @elseif($roomPrice)
                            @if($roomPrice->discount_price)
                            <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                <del class="text-muted mr-2"> ৳{{ $roomPrice->fixed_price }}</del>
                                <span class="fs-17 font-weight-bold text-heading mb-0 lh-16"> ৳{{ $roomPrice->discount_price }}</span>
                                <span class="price-indicate">{{ $roomPriceIndicator }}</span>
                            </p>
                            @else
                            <p class="fs-17 font-weight-bold text-heading mb-0 lh-16"> ৳{{ $roomPrice->fixed_price }}<span class="price-indicate">{{ $roomPriceIndicator }}</span></p>
                            @endif
                            @endif

                        </div>
                        <div class="card-footer bg-transparent px-3 pb-0 pt-2">
                            <ul class="list-inline mb-0">
                                @if (!$propertyPrice)
                                <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7" data-toggle="tooltip" title="{{ $package->bedrooms }} Bedroom">
                                    <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                        <use xlink:href="#icon-bedroom"></use>
                                    </svg>
                                    {{ $package->rooms->count() }} Br
                                </li>
                                @else
                                <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7" data-toggle="tooltip" title="{{ $package->number_of_rooms }} Bedroom">
                                    <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                        <use xlink:href="#icon-bedroom"></use>
                                    </svg>
                                    {{ $package->number_of_rooms }} Rm
                                </li>
                                @endif
                                <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7" data-toggle="tooltip" title="{{ $package->common_bathrooms }} Bathrooms">
                                    <svg class="icon icon-shower fs-18 text-primary mr-1">
                                        <use xlink:href="#icon-shower"></use>
                                    </svg>
                                    {{ $package->common_bathrooms }} Ba
                                </li>
                                <li class="list-inline-item text-gray font-weight-500 fs-13" data-toggle="tooltip" title="{{ $package->seating }} Seating">
                                    <svg class="icon icon-square fs-18 text-primary mr-1">
                                        <use xlink:href="#icon-square"></use>
                                    </svg>
                                    {{ $package->seating }} Seating
                                </li>
                            </ul>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
            @else
            <div class="w-100 text-center">
                <div>
                    <img src="{{ asset('images/notFound.png') }}" alt="Not_Found">
                </div>
                <div id="filterPackage" class="alert alert-warning text-center" role="alert">
                    No packages found.
                </div>
            </div>
            @endif
        </div>
        {{-- @if($noPackagesFound)
        <div id="filterPackage" class="alert alert-warning text-center" role="alert">
            No packages found.
        </div>
        @endif --}}
    </div>
    @endif
    <script>
        function scrollToFilterPackage() {
            var element = document.getElementById('filterPackage');
            if (element) {
                element.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    </script>


    <div>
        <section class="py-4">
            <div class="container">
                <div class="">
                    <div class="">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fs-4">Best Place to Book</h4>
                            <div class="text-md-right">
                                <a href="{{route('package.list')}}" class="fs-8 text-light btn-accent p-2 bg-primary">
                                    <i class="fas fa-share-all"></i>
                                </a>
                            </div>
                        </div>

                        <span class="heading-divider"></span>
                        <p class="mb-2">Choose Your Package</p>
                    </div>
                </div>
                <div wire:ignore class="slick-slider slick-dots-mt-0 custom-arrow-spacing-30"
                    data-slick-options='{"slidesToShow": 4,"dots":true,"arrows":false,"responsive":[{"breakpoint": 1600,"settings": {"slidesToShow":3}},{"breakpoint": 992,"settings": {"slidesToShow":2,"arrows":false}},{"breakpoint": 768,"settings": {"slidesToShow": 2,"arrows":false,"dots":true,"autoplay":true}},{"breakpoint": 576,"settings": {"slidesToShow": 1,"arrows":false,"dots":true,"autoplay":true}}]}'>
                    @foreach($featuredPackages as $package)
                    <div class="box box pb-7 pt-2" data-animate="fadeInUp">
                        <a href="{{ $package->getShowUrl() }}" class="text-dark text-decoration-none">
                            <div class="card shadow-hover-2">
                                <div class="hover-change-image bg-hover-overlay rounded-lg card-img-top">
                                    @if($package->photos->isNotEmpty() && file_exists(public_path('storage/'. $package->photos->first()->url)))
                                    <img src="{{ asset('storage/'.$package->photos->first()->url) }}" alt="Thumbnail" class="img-thumbnail">
                                    @else
                                    <img src="{{ asset('images/no_image.png') }}" alt="Thumbnail" class="img-thumbnail">
                                    @endif
                                    <div class="card-img-overlay p-2 d-flex flex-column">
                                        <div>
                                            {{-- Optional badges or overlays can go here --}}
                                        </div>
                                        {{-- Optional additional content such as image counts or videos can go here --}}
                                    </div>
                                </div>
                                <div class="card-body pt-3">
                                    <h2 class="card-title fs-16 lh-2 mb-0">
                                        {{ $package->name }}
                                    </h2>
                                    <p class="card-text font-weight-500 text-gray-light mb-2">
                                        {{ $package->address }}
                                    </p>

                                    <div class="mb-2">
                                        @php
                                        $roomPrices = $package->rooms->flatMap(function($room) {
                                        return $room->prices;
                                        });

                                        $roomPriceData = $this->getFirstAvailablePrice($roomPrices);
                                        $roomPrice = $roomPriceData['price'] ?? null;
                                        $roomPriceType = $roomPriceData['type'] ?? null;
                                        $roomPriceIndicator = $roomPriceType ? $this->getPriceIndicator($roomPriceType) : '';

                                        $propertyPrices = $package->entireProperty->prices ?? [];
                                        $propertyPriceData = $this->getFirstAvailablePrice($propertyPrices);
                                        $propertyPrice = $propertyPriceData['price'] ?? null;
                                        $propertyPriceType = $propertyPriceData['type'] ?? null;
                                        $propertyPriceIndicator = $propertyPriceType ? $this->getPropertyPriceIndicator($propertyPriceType) : '';
                                        @endphp

                                        @if($roomPrice && !$propertyPrice)

                                        @if($roomPrice->discount_price)
                                        <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                            <del class="text-muted mr-2"> ৳{{ $roomPrice->fixed_price }}</del>
                                            <span class="fs-17 font-weight-bold text-heading mb-0 lh-16"> ৳{{ $roomPrice->discount_price }}</span>
                                            <span class="price-indicate">{{ $roomPriceIndicator }}</span>
                                        </p>
                                        @else
                                        <p class="fs-17 font-weight-bold text-heading mb-0 lh-16"> ৳{{ $roomPrice->fixed_price }}<span class="price-indicate">{{ $roomPriceIndicator }}</span></p>
                                        @endif
                                        @endif

                                        {{-- @if($propertyPrice)
                                    @if($propertyPrice->discount_price)
                                        <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                            <del class="text-muted mr-2"> ৳{{ $propertyPrice->fixed_price }}</del>
                                        <span class="fs-17 font-weight-bold text-heading mb-0 lh-16"> ৳{{ $propertyPrice->discount_price }}</span>
                                        <span class="price-indicate">{{ $propertyPriceIndicator }}</span>
                                        </p>
                                        @else
                                        <p class="fs-17 font-weight-bold text-heading mb-0 lh-16"> ৳{{ $propertyPrice->fixed_price }}<span class="price-indicate">{{ $propertyPriceIndicator }}</span></p>
                                        @endif
                                        @endif --}}

                                        @if($propertyPrice)
                                        @if($propertyPrice->discount_price)
                                        <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                            <del class="text-muted mr-2"> ৳{{ $propertyPrice->fixed_price }}</del>
                                            <span class="fs-17 font-weight-bold text-heading mb-0 lh-16"> ৳{{ $propertyPrice->discount_price }}</span>
                                            <span class="price-indicate">{{($propertyPriceIndicator)}}</span>
                                        </p>
                                        @else
                                        <p class="fs-17 font-weight-bold text-heading mb-0 lh-16"> ৳{{ $propertyPrice->fixed_price }}<span class="price-indicate">(p/n by property)</span></p>
                                        @endif
                                        @endif
                                    </div>
                                    <ul class="list-inline d-flex mb-0 flex-wrap mr-n5">
                                        @if (!$propertyPrice)
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7" data-toggle="tooltip" title="{{ $package->bedrooms }} Bedroom">
                                            <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-bedroom"></use>
                                            </svg>
                                            {{ $package->rooms->count() }} Br
                                        </li>
                                        @else
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7" data-toggle="tooltip" title="{{ $package->number_of_rooms }} Bedroom">
                                            <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-bedroom"></use>
                                            </svg>
                                            {{ $package->number_of_rooms }} Rm
                                        </li>
                                        @endif
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-5"
                                            data-toggle="tooltip" title="{{ $package->common_bathrooms }} Bathrooms">
                                            <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-shower"></use>
                                            </svg>
                                            {{ $package->common_bathrooms }} Ba
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-5"
                                            data-toggle="tooltip" title="Size">
                                            <svg class="icon icon-square fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-square"></use>
                                            </svg>
                                            {{ $package->seating }} Seating
                                        </li>
                                        {{-- Additional list items can go here --}}
                                    </ul>
                                </div>
                                <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @livewire('user.home-data-user')
    </div>
</div>
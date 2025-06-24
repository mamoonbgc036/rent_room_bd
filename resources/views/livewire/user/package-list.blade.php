<div>
    <section class="pb-8 page-title shadow">
        <div class="container mt-4">
            <button onclick="window.history.back()" class="btn btn-primary">
                &larr; Back
            </button>
            <h3 class="lh-1 mt-4 text-heading font-weight-600">
                @if ($partner)
                {{ $partner->name }}'s Packages
                @else
                Package Lists
                @endif
            </h3>
            @if ($partner)
            <p class="text-muted mt-2">
                Showing all properties by {{ $partner->name }}
            </p>
            @endif
        </div>
    </section>
    <section class="pt-2 pb-11">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 order-1 order-lg-1 primary-sidebar sidebar-sticky" id="sidebar">
                    <div class="primary-sidebar-inner">
                        <div class="card mb-4">
                            <div class="card-body px-6 py-4">
                                <h4 class="card-title fs-16 lh-2 text-dark mb-3">Find your home</h4>
                                <form wire:submit.prevent="search">
                                    <div class="form-group">
                                        <label for="city" class="sr-only">City</label>
                                        <select wire:model.live="selectedCity"
                                            class="form-control border-0 shadow-none form-control-lg" title="City"
                                            data-style="btn-lg py-2 h-52" id="city">
                                            <option value="">Select City</option>
                                            @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="area" class="sr-only">Area</label>
                                        <select wire:model.live="selectedArea"
                                            class="form-control border-0 shadow-none form-control-lg" title="Area"
                                            data-style="btn-lg py-2 h-52" id="area">
                                            <option value="">Select Area</option>
                                            @foreach ($areas as $area)
                                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- <button type="submit"
                                        class="btn btn-primary btn-lg btn-block shadow-none mt-4">Search</button> --}}
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-lg-8 mb-8 mb-lg-0 order-2 order-lg-2">
                    <div class="row align-items-sm-center mb-6">
                        <div class="col-md-6">
                            @if ($packages)
                            <h2 class="fs-15 text-dark mb-0">
                                We found <span class="text-primary">{{ $packages->total() }}</span> properties
                                available for you
                            </h2>
                        </div>
                    </div>
                    <div class="row">
                        @forelse($packages as $package)
                        <div class="col-md-6 mb-6">
                            <a href="{{ $package->getShowUrl() }}" class="text-decoration-none">
                                <div class="card border-0 py-3" wire:key="package-{{ $package->id }}">
                                    <!-- Property Image -->
                                    <div
                                        class="position-relative hover-change-image bg-hover-overlay rounded-lg card-img">
                                        <div
                                            class="position-relative hover-change-image bg-hover-overlay rounded-lg card-img">
                                            @if ($package->photos->isNotEmpty())
                                            <img src="{{ asset('storage/' . $package->photos->first()->url) }}"
                                                alt="Thumbnail" class="img-thumbnail">
                                            @else
                                            <img src="{{ asset('default-thumbnail.jpg') }}" alt="Thumbnail"
                                                class="img-thumbnail">
                                            @endif
                                            <div class="card-img-overlay d-flex flex-column">
                                                <div><span
                                                        class="badge badge-primary">{{ $package->status }}</span>
                                                </div>
                                                <div class="mt-auto d-flex hover-image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Property Details -->
                                    <div class="card-body pt-3 px-3 pb-1">
                                        <h2 class="fs-16 mb-1">{{ $package->name }}</h2>
                                        <p class="font-weight-500 text-gray-light mb-0">
                                            {{ $package->address }}
                                        </p>

                                        @php
                                        $roomPrices = $package->rooms->flatMap(function ($room) {
                                        return $room->prices;
                                        });

                                        $roomPriceData = $this->getFirstAvailablePrice($roomPrices);
                                        $propertyPriceData = $this->getFirstAvailablePrice(
                                        $package->entireProperty->prices ?? collect(),
                                        );
                                        @endphp

                                        <!-- Price Display -->
                                        @if ($propertyPriceData)
                                        <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                            @if ($propertyPriceData['price']->discount_price)
                                            <del
                                                class="text-muted mr-2">৳{{ $propertyPriceData['price']->fixed_price }}</del>
                                            ৳{{ $propertyPriceData['price']->discount_price }}
                                            @else
                                            ৳{{ $propertyPriceData['price']->fixed_price }}
                                            @endif
                                            <span class="price-indicate">
                                                {{ $this->getPropertyPriceIndicator($propertyPriceData['type']) }}
                                            </span>
                                        </p>
                                        @elseif($roomPriceData)
                                        <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                            @if ($roomPriceData['price']->discount_price)
                                            <del
                                                class="text-muted mr-2">৳{{ $roomPriceData['price']->fixed_price }}</del>
                                            ৳{{ $roomPriceData['price']->discount_price }}
                                            @else
                                            ৳{{ $roomPriceData['price']->fixed_price }}
                                            @endif
                                            <span class="price-indicate">
                                                {{ $this->getPriceIndicator($roomPriceData['type']) }}
                                            </span>
                                        </p>
                                        @endif
                                    </div>

                                    <!-- Property Features -->
                                    <div class="card-footer bg-transparent px-3 pb-0 pt-2">
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7">
                                                <i class="fas fa-bed mr-1"></i>
                                                {{ $package->rooms->count() }} Rooms
                                            </li>
                                            <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7">
                                                <i class="fas fa-bath mr-1"></i>
                                                {{ $package->common_bathrooms }} Baths
                                            </li>
                                            @if ($package->seating)
                                            <li class="list-inline-item text-gray font-weight-500 fs-13">
                                                <i class="fas fa-couch mr-1"></i>
                                                {{ $package->seating }} Seating
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                No properties found matching your criteria.
                            </div>
                        </div>
                        @endforelse
                    </div>
                    <nav class="pt-4">
                        <ul class="pagination rounded-active justify-content-center mb-0">
                            <!-- Previous page link -->
                            @if ($packages->previousPageUrl())
                            <li class="page-item"><a class="page-link" href="{{ $packages->previousPageUrl() }}"><i
                                        class="far fa-angle-double-left"></i></a></li>
                            @endif

                            <!-- Pagination numbers -->
                            @foreach (range(1, $packages->lastPage()) as $page)
                            @if ($page == $packages->currentPage())
                            <li class="page-item active"><a class="page-link"
                                    href="{{ $packages->url($page) }}">{{ $page }}</a></li>
                            @else
                            <li class="page-item"><a class="page-link"
                                    href="{{ $packages->url($page) }}">{{ $page }}</a></li>
                            @endif
                            @endforeach

                            <!-- Next page link -->
                            @if ($packages->nextPageUrl())
                            <li class="page-item"><a class="page-link" href="{{ $packages->nextPageUrl() }}"><i
                                        class="far fa-angle-double-right"></i></a></li>
                            @endif
                        </ul>
                    </nav>
                    @else
                    <p>No properties found.</p>
                    @endif

                </div>

            </div>
        </div>
    </section>


</div>
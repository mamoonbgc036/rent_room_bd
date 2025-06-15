<div class="mb-10">

    @php
    $videoUrl = $package->video_link;
    if (
    preg_match(
    '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i',
    $videoUrl,
    $matches,
    )
    ) {
    $videoId = $matches[1];
    } else {
    $videoId = null;
    }
    @endphp
    <section>
        <div class="container mt-4">
            <button onclick="window.history.back()" class="btn btn-primary">
                &larr; Back
            </button>
        </div>
        <div>


            <div class="container px-0 mt-4">
                <div class="slider-section" id="imageSlider">
                    <!-- Main Image -->
                    <div class="main-image-wrapper">
                        @if(!empty($package))
                        <img src="{{ asset('storage/' . $package?->photos[0]->url) }}" class="main-image" alt="Main Image"
                            id="mainImage">
                        @endif
                    </div>

                    <!-- Navigation Controls -->
                    <div class="slider-controls">
                        <button class="nav-btn prev" id="prevBtn">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div class="image-counter" id="imageCounter">
                            1 / {{ count($package->photos) }}
                        </div>
                        <button class="nav-btn next" id="nextBtn">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Lightbox -->
                <div class="lightbox-overlay" id="lightbox">
                    <div class="lightbox-content">
                        <img src="" alt="Lightbox Image" class="lightbox-image" id="lightboxImage">

                        <button class="lightbox-nav prev" id="lightboxPrev">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="lightbox-nav next" id="lightboxNext">
                            <i class="fas fa-chevron-right"></i>
                        </button>

                        <button class="lightbox-close" id="lightboxClose">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Get all images from PHP/Blade
                    const images = @json(
                        $package->photos->map(function($photo) {
                            return asset('storage/'.$photo->url);
                        }));

                    // Initialize variables
                    let currentIndex = 0;
                    const totalImages = images.length;

                    // Get DOM elements
                    const mainImage = document.getElementById('mainImage');
                    const prevBtn = document.getElementById('prevBtn');
                    const nextBtn = document.getElementById('nextBtn');
                    const imageCounter = document.getElementById('imageCounter');
                    const lightbox = document.getElementById('lightbox');
                    const lightboxImage = document.getElementById('lightboxImage');
                    const lightboxPrev = document.getElementById('lightboxPrev');
                    const lightboxNext = document.getElementById('lightboxNext');
                    const lightboxClose = document.getElementById('lightboxClose');

                    // Update display
                    function updateDisplay(index) {
                        mainImage.src = images[index];
                        imageCounter.textContent = `${index + 1} / ${totalImages}`;

                        // Optional: Add fade effect
                        mainImage.style.opacity = '0';
                        setTimeout(() => mainImage.style.opacity = '1', 50);
                    }

                    // Navigation functions
                    function showPrevImage() {
                        currentIndex = (currentIndex - 1 + totalImages) % totalImages;
                        updateDisplay(currentIndex);
                    }

                    function showNextImage() {
                        currentIndex = (currentIndex + 1) % totalImages;
                        updateDisplay(currentIndex);
                    }

                    // Event listeners for main slider
                    prevBtn.addEventListener('click', showPrevImage);
                    nextBtn.addEventListener('click', showNextImage);

                    // Keyboard navigation
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'ArrowLeft') {
                            showPrevImage();
                        } else if (e.key === 'ArrowRight') {
                            showNextImage();
                        }
                    });

                    // Lightbox functions
                    function openLightbox() {
                        lightbox.style.display = 'flex';
                        lightboxImage.src = images[currentIndex];
                        document.body.style.overflow = 'hidden'; // Prevent scrolling
                    }

                    function closeLightbox() {
                        lightbox.style.display = 'none';
                        document.body.style.overflow = '';
                    }

                    function updateLightboxImage() {
                        lightboxImage.src = images[currentIndex];
                        imageCounter.textContent = `${currentIndex + 1} / ${totalImages}`;
                    }

                    // Event listeners for lightbox
                    mainImage.addEventListener('click', openLightbox);
                    lightboxClose.addEventListener('click', closeLightbox);
                    lightboxPrev.addEventListener('click', (e) => {
                        e.stopPropagation();
                        showPrevImage();
                        updateLightboxImage();
                    });
                    lightboxNext.addEventListener('click', (e) => {
                        e.stopPropagation();
                        showNextImage();
                        updateLightboxImage();
                    });

                    // Close lightbox when clicking outside
                    lightbox.addEventListener('click', (e) => {
                        if (e.target === lightbox) {
                            closeLightbox();
                        }
                    });

                    // Add touch swipe support
                    let touchStartX = 0;
                    let touchEndX = 0;

                    mainImage.addEventListener('touchstart', e => {
                        touchStartX = e.changedTouches[0].screenX;
                    });

                    mainImage.addEventListener('touchend', e => {
                        touchEndX = e.changedTouches[0].screenX;
                        handleSwipe();
                    });

                    function handleSwipe() {
                        const swipeThreshold = 50;
                        const swipeLength = touchEndX - touchStartX;

                        if (Math.abs(swipeLength) > swipeThreshold) {
                            if (swipeLength > 0) {
                                showPrevImage();
                            } else {
                                showNextImage();
                            }
                        }
                    }

                    // Initialize display
                    updateDisplay(currentIndex);
                    lightbox.style.display = 'none';
                });
            </script>

            <style>
                /* Main Slider Styles */
                .slider-section {
                    position: relative;
                    width: 100%;
                    margin-bottom: 20px;
                }

                .main-image-wrapper {
                    position: relative;
                    width: 100%;
                    overflow: hidden;
                }

                .main-image {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    cursor: pointer;
                    display: block;
                    transition: opacity 0.3s ease;
                }

                /* Navigation Controls */
                .slider-controls {
                    position: absolute;
                    top: 50%;
                    left: 0;
                    right: 0;
                    transform: translateY(-50%);
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 0 20px;
                    z-index: 10;
                }

                .nav-btn {
                    background: rgba(255, 255, 255, 0.9);
                    border: none;
                    border-radius: 50%;
                    width: 44px;
                    height: 44px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
                }

                .nav-btn:hover {
                    background: #fff;
                    transform: scale(1.1);
                }

                .nav-btn i {
                    color: #333;
                    font-size: 1.2rem;
                }

                .image-counter {
                    position: absolute;
                    bottom: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: rgba(0, 0, 0, 0.7);
                    color: white;
                    padding: 8px 16px;
                    border-radius: 20px;
                    font-size: 14px;
                    font-weight: 500;
                    display: none;
                }

                /* Lightbox Styles */
                .lightbox-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.9);
                    display: none;
                    align-items: center;
                    justify-content: center;
                    z-index: 1050;
                }

                .lightbox-content {
                    position: relative;
                    max-width: 90vw;
                    max-height: 90vh;
                }

                .lightbox-image {
                    max-width: 100%;
                    max-height: 90vh;
                    object-fit: contain;
                }

                .lightbox-nav {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    background: rgba(255, 255, 255, 0.2);
                    border: none;
                    color: white;
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .lightbox-nav:hover {
                    background: rgba(255, 255, 255, 0.3);
                }

                .lightbox-nav.prev {
                    left: -70px;
                }

                .lightbox-nav.next {
                    right: -70px;
                }

                .lightbox-close {
                    position: absolute;
                    top: -40px;
                    right: 0;
                    background: none;
                    border: none;
                    color: white;
                    font-size: 24px;
                    cursor: pointer;
                }

                /* Mobile Responsive */
                @media (max-width: 768px) {
                    .main-image-wrapper {
                        height: 40vh;
                    }

                    .nav-btn {
                        width: 36px;
                        height: 36px;
                    }

                    .nav-btn i {
                        font-size: 1rem;
                    }

                    .image-counter {
                        padding: 6px 12px;
                        font-size: 12px;
                    }

                    .lightbox-nav.prev {
                        left: 10px;
                    }

                    .lightbox-nav.next {
                        right: 10px;
                    }

                    .slider-controls {
                        padding: 0 10px;
                    }
                }
            </style>



        </div>
    </section>

    <Section>
        <div class="container py-4 video-section">
            @if ($videoId)
            <div class="embed-responsive embed-responsive-16by9">
                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" title="YouTube video player"
                    class="embed-responsive-item w-100 h-100 rounded-t-lg mb-4" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
            @else
            <p class="text-center text-danger">Unable to load the video. Please check the video link or try again
                later.</p>
            @endif
        </div>
    </Section>

    <div class="primary-content pt-2">
        <div class="container">
            <div class="row">
                <article class="col-lg-8 pr-xl-7">
                    <!-- Package Details Section -->
                    <section class="package-details py-3 border-bottom">
                        <!-- Package Header -->
                        <div class="row">
                            <!-- Package Info Column -->
                            <div class="col-12 col-md-8 mb-md-0">
                                <!-- Package Title and Partner Info -->
                                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
                                    <h2 class="h4 font-weight-600 text-heading mb-1 mb-sm-0">{{ $package->name }}</h2>

                                </div>

                                <!-- Address and Map Link -->
                                <div class="d-flex flex-wrap align-items-center text-muted">
                                    <div class="d-flex align-items-center mr-3 mb-sm-0">
                                        <i class="fal fa-map-marker-alt mr-2"></i>
                                        <span class="text-break">{{ $package->address }}</span>
                                    </div>
                                    @if ($package->map_link)
                                    <a href="{{ $package->map_link }}" target="_blank"
                                        class="btn btn-sm btn-outline-secondary">
                                        Map
                                    </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Price Display Column -->
                            <div class="col-12 col-md-4">
                                <div class="price-section text-left text-md-right">
                                    @php
                                    $roomPrices = $package->rooms->flatMap(function ($room) {
                                    return $room->roomPrices->map(function ($price) use ($room) {
                                    $price->room_name = $room->name; // Add room name to each price
                                    return $price;
                                    });
                                    });
                                    $firstPrice = $roomPrices->first();
                                    $priceType = $firstPrice ? $firstPrice->type : null;
                                    @endphp

                                    @if ($firstPrice)
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary w-100 w-md-auto position-relative"
                                            type="button" id="priceDropdown" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex flex-column align-items-start">
                                                    <!-- Main Price -->
                                                    <span class="h5 mb-0 font-weight-bold">
                                                        @if ($firstPrice->discount_price)
                                                        ৳{{ number_format($firstPrice->discount_price, 2) }}
                                                        @else
                                                        ৳{{ number_format($firstPrice->fixed_price, 2) }}
                                                        @endif
                                                        <small class="text-muted" style="font-size: 0.65rem;">
                                                            {{ ucfirst($priceType) }}ly
                                                        </small>
                                                    </span>
                                                    <!-- Deleted Price, Rate Type and Room Name -->
                                                    <div class="d-flex align-items-center">
                                                        @if ($firstPrice->discount_price)
                                                        <del
                                                            class="small text-muted mr-1">৳{{ number_format($firstPrice->fixed_price, 2) }}</del>
                                                        @endif
                                                        <small class="text-muted"
                                                            style="font-size: 0.65rem;">{{ $firstPrice->room_name }}</small>
                                                    </div>
                                                </div>
                                                <!-- Dropdown Icon -->
                                                <i class="fas fa-chevron-down ml-2"></i>
                                            </div>
                                        </button>

                                        <!-- Price Dropdown Menu -->
                                        <div class="dropdown-menu dropdown-menu-right p-3 w-100"
                                            style="min-width: 280px; max-width: 100%; max-height: 400px; overflow-y: auto;">
                                            @foreach ($package->rooms as $room)
                                            <div class="room-price-item mb-3">
                                                <h6 class="border-bottom pb-2">
                                                    <i class="fas fa-bed mr-2 text-primary"></i>
                                                    {{ $room->name }}
                                                </h6>
                                                @php
                                                $pricesByType = $room->roomPrices->groupBy('type');
                                                @endphp
                                                @foreach ($pricesByType as $type => $prices)
                                                <div class="price-type-group mb-2">
                                                    <div class="text-muted small mb-1">
                                                        {{ ucfirst($type) }}ly
                                                    </div>
                                                    @foreach ($prices as $price)
                                                    <div
                                                        class="price-item d-flex justify-content-between align-items-center py-1">
                                                        <div class="price-amount">
                                                            @if ($price->discount_price)
                                                            <del
                                                                class="text-muted small">৳{{ number_format($price->fixed_price, 2) }}</del>
                                                            <span
                                                                class="text-success">৳{{ number_format($price->discount_price, 2) }}</span>
                                                            @else
                                                            <span>৳{{ number_format($price->fixed_price, 2) }}</span>
                                                            @endif
                                                        </div>
                                                        <small class="text-muted booking-fee">
                                                            +৳{{ number_format($price->booking_price, 2) }}
                                                            booking
                                                        </small>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                @endforeach
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="description-section mt-1">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <!-- Partner Info - Moves to new line on extra small screens -->
                                @if ($package->assignedPartner)
                                <div class="d-flex align-items-center">
                                    <!-- Partner Profile Photo -->
                                    <div class="partner-photo">
                                        @if ($package->user && $package->user->profile_photo_path)
                                        <img src="{{ Storage::url($package->user->profile_photo_path) }}"
                                            alt="{{ $package->user->name }}"
                                            class="rounded-circle border shadow-sm"
                                            style="width: 32px; height: 32px; object-fit: cover;">
                                        @else
                                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center border shadow-sm"
                                            style="width: 32px; height: 32px;">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Partner Name -->
                                    <div class="partner-name">
                                        @if ($package->assignedPartner)
                                        <a href="{{ route('partner.packages', ['partnerSlug' => str_replace(' ', '-', strtolower($package->assignedPartner->name))]) }}"
                                            class="text-decoration-none">
                                            <span class="font-weight-medium ml-2 text-success">
                                                {{ $package->assignedPartner->name }}
                                            </span>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="description-content">
                                @php
                                $words = explode(' ', $package->details);
                                $limitedWords = array_slice($words, 0, 50);
                                $remainingWords = array_slice($words, 50);
                                @endphp
                                <p class="mb-0 text-break">
                                    {{ implode(' ', $viewMore ? $words : $limitedWords) }}
                                    @if (count($words) > 50)
                                    <span wire:click="toggleViewMore" class="text-primary cursor-pointer">
                                        {{ $viewMore ? 'View less' : 'View more' }}
                                    </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </section>

                    <style>
                        /* Custom CSS for enhanced mobile responsiveness */
                        @media (max-width: 575.98px) {
                            .package-details h2 {
                                font-size: 1.25rem;
                            }

                            .border-left-sm {
                                border-left: none !important;
                            }

                            .price-section {
                                margin-top: 1rem;
                            }

                            .dropdown-menu {
                                width: 100%;
                                min-width: 100% !important;
                                margin-top: 0.5rem;
                            }

                            .price-item {
                                flex-direction: column;
                                align-items: flex-start !important;
                            }

                            .booking-fee {
                                margin-top: 0.25rem;
                                margin-left: 0.5rem;
                            }
                        }

                        /* Base button styles */
                        .btn-outline-secondary {
                            transition: all 0.3s ease;
                            border: 1px solid #6c757d;
                        }

                        /* Hover styles */
                        .btn-outline-secondary:hover {
                            background-color: #f8f9fa;
                            border-color: #6c757d;
                            color: #495057;
                        }

                        /* Ensure price remains visible on hover */
                        .btn-outline-secondary:hover .h5,
                        .btn-outline-secondary:hover .text-muted {
                            color: #495057 !important;
                        }

                        /* Deleted price hover state */
                        .btn-outline-secondary:hover del.text-muted {
                            color: #6c757d !important;
                        }

                        /* Smooth transition for the dropdown icon */
                        .btn-outline-secondary:hover .fa-chevron-down {
                            color: #495057;
                        }

                        @media (min-width: 576px) {
                            .border-left-sm {
                                border-left: 1px solid #dee2e6;
                            }
                        }

                        /* General styling improvements */
                        .package-details {
                            background-color: #fff;
                        }

                        .cursor-pointer {
                            cursor: pointer;
                        }

                        .text-break {
                            word-break: break-word;
                        }

                        .description-content {
                            line-height: 1.6;
                        }

                        /* Dropdown improvements */
                        .dropdown-menu {
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                        }

                        .room-price-item:last-child {
                            margin-bottom: 0;
                        }

                        .price-type-group:last-child {
                            margin-bottom: 0;
                        }

                        #priceDropdown {
                            padding: 0.5rem 1rem;
                        }

                        #priceDropdown .h5 {
                            line-height: 1.2;
                            color: #333;
                        }

                        #priceDropdown .fa-chevron-down {
                            font-size: 0.875rem;
                            transition: transform 0.2s ease;
                        }

                        #priceDropdown[aria-expanded="true"] .fa-chevron-down {
                            transform: rotate(180deg);
                        }

                        @media (max-width: 767.98px) {
                            #priceDropdown {
                                text-align: left;
                            }
                        }
                    </style>


                    <section>
                        <div class="wrapper center-block">
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-default">
                                    <div class="panel-heading active" role="tab" id="headingOne">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                Features
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                        aria-labelledby="headingOne">
                                        <div class="panel-body">
                                            <div class="row px-4">
                                                <div class="col-lg-3 col-sm-4 mb-6">
                                                    <div class="media d-flex align-items-center">
                                                        <div class="p-2 shadow-xxs-1 rounded-lg mr-2">
                                                            <i class="fad fa-user-alt fs-32 text-primary"></i>
                                                        </div>
                                                        <div class="media-body ml-2">
                                                            <p class="m-0 fs-13 font-weight-bold text-heading">
                                                                {{ $package->user->name }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-4 mb-6">
                                                    <div class="media d-flex align-items-center">
                                                        <div class="p-2 shadow-xxs-1 rounded-lg mr-2">
                                                            <i class="fad fa-oven fs-32 text-primary"></i>
                                                        </div>
                                                        <div class="media-body ml-2">
                                                            <p class="m-0 fs-13 font-weight-bold text-heading">
                                                                {{ $package->number_of_kitchens }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-4 mb-6">
                                                    <div class="media d-flex align-items-center">
                                                        <div class="p-2 shadow-xxs-1 rounded-lg mr-2">
                                                            <i class="fas fa-loveseat fs-32 text-primary"></i>
                                                        </div>

                                                        <div class="media-body ml-2">
                                                            <p class="m-0 fs-13 font-weight-bold text-heading">
                                                                {{ $package->seating }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 col-sm-4 mb-6">
                                                    <div class="media d-flex align-items-center">

                                                        <div class="p-2 shadow-xxs-1 rounded-lg mr-2">
                                                            <i class="far fa-bed fs-32 text-primary"></i>
                                                        </div>
                                                        <div class="media-body ml-2">
                                                            <p class="m-0 fs-13 font-weight-bold text-heading">
                                                                {{ $package->number_of_rooms }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 col-sm-4 mb-6">
                                                    <div class="media d-flex align-items-center">
                                                        {{-- <div class="p-2 shadow-xxs-1 rounded-lg mr-2">
                                                            <i class="fad fa-toilet fs-32 text-primary"></i>
                                                        </div> --}}
                                                        <div class="p-2 shadow-xxs-1 rounded-lg mr-2">
                                                            <i class="fas fa-bath fs-32 text-primary"></i>
                                                        </div>
                                                        <div class="media-body ml-2">
                                                            <p class="m-0 fs-13 font-weight-bold text-heading">
                                                                {{ $package->common_bathrooms }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-default">
                                    <div class="panel-heading active" role="tab" id="headingTwo">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                                Amenities
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel"
                                        aria-labelledby="headingTwo">
                                        <div class="panel-body">
                                            <div class="px-4">
                                                <ul class="list-unstyled mb-0 row no-gutters">
                                                    @foreach ($package->amenities as $amenity)
                                                    <li class="col-sm-3 col-6 mb-2">
                                                        <i
                                                            class="far fa-check mr-2 text-primary"></i>{{ $amenity->name }}
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            <div class="px-4">
                                                <ul class="list-unstyled mb-0 row no-gutters">
                                                    @foreach ($package->amenities()->wherePivot('is_paid', true)->get() as $amenity)
                                                    <li class="list-group-item">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                wire:model="selectedAmenities.{{ $amenity->id }}"
                                                                value="{{ $amenity->pivot->price }}"
                                                                id="amenity{{ $amenity->id }}">
                                                            <label class="form-check-label"
                                                                for="amenity{{ $amenity->id }}">
                                                                {{ $amenity->name }} -
                                                                ৳{{ $amenity->pivot->price }}
                                                            </label>
                                                        </div>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading active" role="tab" id="headingThree">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                href="#collapseThree" aria-expanded="true"
                                                aria-controls="collapseThree">
                                                Maintains
                                            </a>
                                        </h4>
                                    </div>

                                    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel"
                                        aria-labelledby="headingThree">
                                        <div class="panel-body">
                                            <div class="px-4">

                                                <ul class="list-unstyled mb-0 row no-gutters">
                                                    @foreach ($package->maintains()->wherePivot('is_paid', false)->get() as $maintain)
                                                    <li class="col-sm-3 col-6 mb-2">
                                                        <i
                                                            class="far fa-check mr-2 text-primary"></i>{{ $maintain->name }}
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="px-4">
                                                <ul class="list-unstyled mb-0 row no-gutters">
                                                    @foreach ($package->maintains()->wherePivot('is_paid', true)->get() as $maintain)
                                                    <li class="list-group-item">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                wire:model="selectedMaintains.{{ $maintain->id }}"
                                                                value="{{ $maintain->pivot->price }}"
                                                                id="maintain{{ $maintain->id }}">
                                                            <label class="form-check-label"
                                                                for="maintain{{ $maintain->id }}">
                                                                {{ $maintain->name }} -
                                                                ৳{{ $maintain->pivot->price }}
                                                            </label>
                                                        </div>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </section>
                </article>

                <aside class="col-lg-4 pl-xl-4 primary-sidebar sidebar-sticky" id="sidebar">
                    <div class="primary-sidebar-inner">
                        <div class="card border-0 widget-request-tour">
                            <div class="card-body px-sm-6 shadow-xxs-2 pb-5 pt-0">
                                <form wire:submit.prevent="submit">
                                    <div class="tab-content pt-1 pb-0 px-0 shadow-none">
                                        <div id="signupform" class="tab-pane fade show active" role="tabpanel">
                                            @if (session('error'))
                                            <div class="alert alert-warning">{{ session('error') }}</div>
                                            @endif

                                            @unless (Auth::check())
                                            <div class="text-center mb-4">
                                                <p>Please <a class="text-primary fw-bold" href="#signInModal"
                                                        data-toggle="modal">Sign in</a>
                                                    if not <a class="text-primary fw-bold" href="#signUpModal"
                                                        data-toggle="modal">Sign up</a></p>
                                            </div>
                                            @endunless
                                            <div class="mb-4">
                                                <div class="position-relative">
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn-primary btn-lg w-100 dropdown-toggle d-flex align-items-center justify-content-between"
                                                            type="button" id="roomSelectButton"
                                                            data-toggle="dropdown"
                                                            {{ !Auth::check() ? 'disabled' : '' }}>
                                                            <span>{{ $selectedRoom ? $package->rooms->find($selectedRoom)->name : 'Select Your Place' }}</span>
                                                            <i class="fas fa-chevron-down"></i>
                                                        </button>
                                                        <div class="dropdown-menu w-100"
                                                            aria-labelledby="roomSelectButton">
                                                            @foreach ($package->rooms as $room)
                                                            <a class="dropdown-item {{ $selectedRoom == $room->id ? 'active' : '' }}"
                                                                wire:click="selectRoom({{ $room->id }})">
                                                                {{ $room->name }} • {{ $room->number_of_beds }}
                                                                Beds • {{ $room->number_of_bathrooms }} Baths
                                                            </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @if (!Auth::check())
                                                    <div class="overlay auth-overlay"
                                                        wire:click="showAuthMessage('room')"
                                                        style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;">
                                                    </div>
                                                    @endif
                                                </div>
                                                @if ($showAuthWarning === 'room')
                                                <span class="text-danger">Please sign in or sign up first.</span>
                                                @endif

                                                @if ($selectedRoom)
                                                <div class="selected-room-details mt-3 p-3 bg-light rounded">
                                                    @php $room = $package->rooms->find($selectedRoom) @endphp
                                                    <div class="d-flex align-items-center">
                                                        <span class="mr-3">{{ $room->name }}</span>
                                                        <span class="text-muted">
                                                            <i class="fas fa-bed"></i> {{ $room->number_of_beds }}
                                                            <i class="fas fa-bath ml-2"></i>
                                                            {{ $room->number_of_bathrooms }}
                                                        </span>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>

                                            @if ($selectedRoom && $calendarView)
                                            <div x-data="datePickerComponent({{ json_encode($disabledDates) }})" wire:ignore.self class="mt-4">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="mb-2">Check-in Date</label>
                                                        <input x-ref="checkInPicker" type="text"
                                                            class="form-control"
                                                            placeholder="Select check-in date" readonly
                                                            {{ !Auth::check() ? 'disabled' : '' }}>
                                                        @error('fromDate')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="mb-2">Check-out Date</label>
                                                        <input x-ref="checkOutPicker" type="text"
                                                            class="form-control"
                                                            placeholder="Select check-out date" readonly
                                                            {{ !Auth::check() ? 'disabled' : '' }}>
                                                        @error('toDate')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            <script>
                                                function datePickerComponent(disabledDates) {
                                                    return {
                                                        disabledDates: disabledDates,
                                                        checkInDate: null,
                                                        init() {
                                                            // Initialize check-in picker
                                                            const checkInPicker = flatpickr(this.$refs.checkInPicker, {
                                                                dateFormat: 'Y-m-d',
                                                                minDate: 'today',
                                                                disable: this.disabledDates.map(date => new Date(date)),
                                                                onChange: (selectedDates) => {
                                                                    if (selectedDates.length > 0) {
                                                                        this.checkInDate = selectedDates[0];
                                                                        // Update check-out picker min date
                                                                        checkOutPicker.set('minDate', selectedDates[0]);
                                                                        // Call Livewire method with selected check-in date
                                                                        @this.call('updateCheckInDate', selectedDates[0].toISOString().split('T')[0]);
                                                                    }
                                                                }
                                                            });

                                                            // Initialize check-out picker
                                                            const checkOutPicker = flatpickr(this.$refs.checkOutPicker, {
                                                                dateFormat: 'Y-m-d',
                                                                minDate: 'today',
                                                                disable: this.disabledDates.map(date => new Date(date)),
                                                                onChange: (selectedDates) => {
                                                                    if (selectedDates.length > 0 && this.checkInDate) {
                                                                        // Call Livewire method with selected dates
                                                                        @this.call('selectDates', {
                                                                            start: this.checkInDate.toISOString().split('T')[0],
                                                                            end: selectedDates[0].toISOString().split('T')[0]
                                                                        });
                                                                    }
                                                                }
                                                            });

                                                            // Watch for changes in disabled dates and update both pickers
                                                            this.$watch('disabledDates', (newValue) => {
                                                                const disabledDatesArray = newValue.map(date => new Date(date));
                                                                checkInPicker.set('disable', disabledDatesArray);
                                                                checkOutPicker.set('disable', disabledDatesArray);
                                                            });
                                                        }
                                                    };
                                                }
                                            </script>



                                            <!-- Phone Number -->
                                            <div class="form-group mb-2">
                                                <label for="phone">Phone Number</label><span
                                                    class="text-danger">*</span>
                                                <div class="position-relative">
                                                    <input type="text" id="phone"
                                                        class="form-control form-control-lg border-0"
                                                        wire:model="phone" placeholder="Your Phone" required
                                                        {{ !Auth::check() ? 'disabled' : '' }}>
                                                    @if (!Auth::check())
                                                    <div class="overlay auth-overlay"
                                                        wire:click="showAuthMessage('phone')"
                                                        style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;">
                                                    </div>
                                                    @endif
                                                </div>
                                                @error('phone')
                                                <span class="error text-danger">{{ $message }}</span>
                                                @enderror
                                                @if ($showAuthWarning === 'phone')
                                                <span class="text-danger">Please sign in or sign up
                                                    first.</span>
                                                @endif
                                            </div>

                                            <!-- Terms & Conditions -->
                                            <div class="form-group form-check mt-2 mb-4">
                                                <input type="checkbox" class="form-check-input" id="exampleCheck1"
                                                    wire:model="terms" required
                                                    {{ !Auth::check() ? 'disabled' : '' }}>
                                                <label class="form-check-label fs-13" for="exampleCheck1">I agree
                                                    to
                                                    the</label>
                                                <a href="#" class="text-danger" id="openModal">Terms &
                                                    Conditions</a>
                                                @error('terms')
                                                <span
                                                    class="error text-danger d-block mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Error Messages -->
                                            @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            @endif

                                            <!-- Submit Button -->
                                            <button type="submit" class="btn btn-primary btn-lg btn-block rounded"
                                                {{ !Auth::check() ? 'disabled' : '' }}>
                                                Proceed to Checkout
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <style>
                        .dropdown-toggle::after {
                            display: none;
                        }

                        .dropdown-menu {
                            margin-top: 0;
                            border: 1px solid rgba(0, 0, 0, .1);
                            max-height: 300px;
                            overflow-y: auto;
                        }

                        .dropdown-item {
                            padding: .75rem 1rem;
                            cursor: pointer;
                        }

                        .dropdown-item.active {
                            background-color: #f8f9fa;
                            color: #000;
                        }

                        .selected-room-details {
                            border: 1px solid #dee2e6;
                            background-color: #f8f9fa;
                        }

                        .btn-lg {
                            padding: 0.75rem 1.25rem;
                        }
                    </style>
                </aside>



            </div>
        </div>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @livewire('user.terms-condition-component')
                </div>
            </div>
        </div>
    </div>


    <style>
        .host-avatar img,
        .host-avatar div {
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .host-avatar img:hover,
        .host-avatar div:hover {
            transform: scale(1.05);
        }

        .dropdown-menu {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 0.5rem;
        }

        .badge {
            padding: 0.4em 0.8em;
            font-weight: 500;
        }

        .btn-outline-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .price-details {
            transition: all 0.3s ease;
        }

        .price-details:hover {
            background-color: #f8f9fa;
        }

        .btn-outline-secondary {
            transition: all 0.2s ease;
        }

        .btn-outline-secondary:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 0.5rem;
        }

        .border-left {
            border-left: 1px solid rgba(0, 0, 0, 0.1) !important;
        }

        .text-body {
            color: #4a5568 !important;
        }

        .shadow-sm {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        }
    </style>
</div>
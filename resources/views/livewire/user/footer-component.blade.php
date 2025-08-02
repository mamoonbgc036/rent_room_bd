<div>
    <footer class="bg-dark pt-8 pb-6 footer text-muted">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-6 mb-md-0">
                    <h4 class="text-white fs-16 my-4 font-weight-500">Contact Us</h4>
                    <div class="lh-26 font-weight-500">
                        <div class="d-flex align-items-center">
                            <span class="mr-2">
                                <i class="fas fa-location-circle fa-2x"></i>
                            </span>
                            <span class="mb-0">{{$footer?->address}}</span>
                        </div>

                        <a class="d-flex align-items-center text-muted hover-white mb-2" href="mailto:contact@homeid.com">
                            <span class="mr-2">
                                <i class="fas fa-envelope fa-1x"></i>
                            </span>
                            <span>{{$footer?->email}}</span>
                        </a>

                        @if($footer && $footer?->contact_number)
                        <a class="d-flex align-items-center text-muted hover-white mb-2" href="tel:{{ preg_replace('/[^0-9+]/', '', $footer?->contact_number) }}">
                            <span class="mr-2">
                                <i class="fas fa-phone-alt fa-1x"></i>
                            </span>
                            <span>{{ $footer?->contact_number }}</span>
                        </a>
                        @endif

                        <a href="https://wa.me/+07777955040" class="d-flex align-items-center text-muted hover-white" href="/">
                            <span class="mr-2">
                                <i class="fab fa-whatsapp-square fa-2x"></i>
                            </span>
                            <span>{{$footer?->website}}</span>
                        </a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-2 mb-6 mb-md-0">
                    <h4 class="text-white fs-16 my-4 font-weight-500">{{ $footer?->section2_title ?? 'Popular Links' }}</h4>
                    <ul class="list-group list-group-flush list-group-no-border">
                        @foreach($footerSectionTwos as $link)
                        <li class="list-group-item bg-transparent p-0">
                            <a href="{{ $link->link }}" class="text-muted lh-26 font-weight-500 hover-white">{{ $link->title }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6 col-lg-2 mb-6 mb-md-0">
                    <h4 class="text-white fs-16 my-4 font-weight-500">Quick links</h4>
                    <ul class="list-group list-group-flush list-group-no-border">
                        @foreach($footerSectionThrees as $link)
                        <li class="list-group-item bg-transparent p-0">
                            <a href="{{ $link->link }}" class="text-muted lh-26 font-weight-500 hover-white">{{ $link->title }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6 col-lg-4 mb-6 mb-md-0">
                    <h4 class="text-white fs-16 my-4 font-weight-500">{{ $footerSectionFour->title ?? 'Follow Us' }}</h4>
                    <p class="font-weight-500 text-muted lh-184">{{ $footerSectionFour->description ?? 'Follow us on our social media platforms' }}</p>
                    <ul class="list-inline mb-0">
                        @foreach($socialLinks as $social)
                        <li class="list-inline-item mr-0">
                            <a href="{{ $social->link }}" class="text-white opacity-10 fs-25 px-4 opacity-hover-5">  
                            <i class="{{ $social->icon_class }}"></i></a>
                        </li>
                        @endforeach

                    </ul>
                </div>
            </div>
            <div class="mt-0 mt-md-10 row">
                <ul class="list-inline mb-0 col-md-6 mr-auto">
                    <li class="list-inline-item mr-6">
                        <a href="{{route('terms-condition')}}" class="text-muted lh-26 font-weight-500 hover-white">{{$footer?->terms_title}}</a>
                    </li>
                    <li class="list-inline-item">
                        <a href="{{route('privacy-policy')}}" class="text-muted lh-26 font-weight-500 hover-white">{{$footer?->privacy_title}}</a>
                    </li>
                </ul>
                <p class="col-md-auto mb-0 text-muted">
                    {{$footer?->rights_reserves_text}}
                </p>
            </div>
        </div>
    </footer>
</div>
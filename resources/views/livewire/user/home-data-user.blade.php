<div>
  <section class="bg-accent py-4 bg-patten-04">
    <div class="container">
      <h2 class="text-dark text-center mxw-751 fs-26 lh-184 px-md-8">
        {{ $homeData?->section_title }}
      </h2>
      <span class="heading-divider mx-auto"></span>
      <div class="mt-2 d-block d-md-flex justify-content-center">
        @if(!empty($homeDataItems))
        @forelse($homeDataItems as $item)
        <div class="m-2" data-animate="zoomIn">
          <div class="card border-hover shadow-2 shadow-hover-lg-1 px-6 py-4 h-100 hover-change-image">
            <div class="flex justify-content-center align-items-center no-gutters">
              <div class="">
                <img src="{{ asset('storage/' . $item->item_image) }}" width="50px" alt="{{$item->item_title}}">
              </div>
              <div class="card-body">
                <a class="d-flex align-items-center text-dark hover-secondary">
                  <h4 class="fs-18 mb-1">{{ $item->item_title }}</h4>
                </a>
              </div>
            </div>
            <p class="mb-0 text-center">{{ $item->item_des }}</p>
          </div>
        </div>
        @empty
        @endforelse
        @endif
      </div>
    </div>
  </section>
</div>
<div>
    @if ($footer)
        <div class="d-flex align-items-center">
            <a href="tel:{{ $footer->contact_number }}"
               class="btn btn-light border rounded-lg d-flex align-items-center">
                <i class="fa fa-phone-alt"></i>
                <span class="ml-2 d-inline">{{ $footer->contact_number }}</span>
            </a>
        </div>
    @endif
</div>

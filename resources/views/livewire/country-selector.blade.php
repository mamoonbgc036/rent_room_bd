<div class="d-md-block d-none">
    <div class="mr-2">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="countryFlag">
                    @if ($selectedCountryPhoto)
                        <img src="{{ asset($selectedCountryPhoto) }}" alt="Country Flag" style="width: 40px; height: auto;">
                    @endif
                </span>
            </div>
            <select wire:model.live="selectedCountry" class="form-control " id="countrySelect">
                <option value="">Select Country</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

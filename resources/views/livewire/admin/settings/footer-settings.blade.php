<div class="container mt-5">
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="form-group">
            <label for="footer_logo">Footer Logo</label>
            <input type="file" class="form-control" id="footer_logo" wire:model="footer_logo">
            @if ($footer_logo instanceof \Livewire\TemporaryUploadedFile)
                <img src="{{ $footer_logo->temporaryUrl() }}" class="img-fluid mt-2" width="150">
            @elseif ($existing_footer_logo)
                <img src="{{ asset('storage/' . $existing_footer_logo) }}" class="img-fluid mt-2" width="150">
            @endif
            @error('footer_logo') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="address">Address</label>
            <input type="text" class="form-control" id="address" wire:model="address">
            @error('address') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" wire:model="email">
            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="contact_number">Contact Number</label>
            <input type="text" class="form-control" id="contact_number" wire:model="contact_number">
            @error('contact_number') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="website">Whatsapp Number</label>
            <input type="any" class="form-control" id="website" wire:model="website">
            @error('website') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="terms_title">Terms Title</label>
            <input type="text" class="form-control" id="terms_title" wire:model="terms_title">
            @error('terms_title') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="terms_link">Terms Link</label>
            <input type="url" class="form-control" id="terms_link" wire:model="terms_link">
            @error('terms_link') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="privacy_title">Privacy Title</label>
            <input type="text" class="form-control" id="privacy_title" wire:model="privacy_title">
            @error('privacy_title') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="privacy_link">Privacy Link</label>
            <input type="url" class="form-control" id="privacy_link" wire:model="privacy_link">
            @error('privacy_link') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="rights_reserves_text">Rights Reserves Text</label>
            <input type="text" class="form-control" id="rights_reserves_text" wire:model="rights_reserves_text">
            @error('rights_reserves_text') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3">Save</button>
    </form>
</div>

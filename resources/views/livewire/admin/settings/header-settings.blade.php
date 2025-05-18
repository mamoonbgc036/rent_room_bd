<div class="container mt-4">
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Header Logo Settings</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="mb-3">
                    <label for="logo" class="form-label">Upload Logo</label>
                    <input type="file" id="logo" class="form-control" wire:model="logo">
                    @error('logo') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Save</button>

                <!-- Loading spinner -->
                <div wire:loading.flex class="spinner-border text-primary" role="status">
                    <span class="visually-hidden"><i class="fa-sharp fa-regular fa-circle-notch"></i></span>
                </div>
            </form>
            @if ($existingLogo)
            <div class="mt-3">
                <h6>Current Logo:</h6>
                <img src="{{ asset('storage/' . $existingLogo) }}" alt="Current Logo" class="img-fluid" style="max-width: 300px;">
            </div>
            @endif
        </div>
    </div>
</div>
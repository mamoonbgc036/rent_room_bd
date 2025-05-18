<div class="container mt-5">
    @if (session()->has('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif

    <!-- Create/Edit Form -->
    <div class="mb-4">
        <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}">
            <div class="mb-3">
                <label for="background_image" class="form-label">Background Image</label>
                <input type="file" id="background_image" class="form-control" wire:model="background_image">
                @error('background_image') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="title_small" class="form-label">Title Small</label>
                <input type="text" id="title_small" class="form-control" wire:model="title_small">
                @error('title_small') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="title_big" class="form-label">Title Big</label>
                <input type="text" id="title_big" class="form-control" wire:model="title_big">
                @error('title_big') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ $isEditing ? 'Update' : 'Save' }}</button>
        </form>

    </div>

    <!-- Hero Section List -->
    <div>
        <h3>Hero Sections</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Background Image</th>
                    <th>Title Small</th>
                    <th>Title Big</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($heroSections as $section)
                <tr>
                    <td>
                        @if($section->background_image)
                        <img src="{{ asset('storage/'. $section->background_image ) }}" width="100" alt="Background Image">
                        @else
                        No Image
                        @endif
                    </td>
                    <td>{{ $section->title_small }}</td>
                    <td>{{ $section->title_big }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" wire:click="edit({{ $section->id }})">Edit</button>
                        <button class="btn btn-danger btn-sm" wire:click="delete({{ $section->id }})">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
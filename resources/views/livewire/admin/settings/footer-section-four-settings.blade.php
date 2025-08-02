<div class="container mt-5">
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <h4>Footer Section Four</h4>
    <form wire:submit.prevent="saveFooterSectionFour">
        <div class="form-group">
            <label for="footerId">Footer ID</label>
            <input type="text" class="form-control" id="footerId" wire:model="footerId">
            @error('footerId') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" wire:model="title">
            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" wire:model="description"></textarea>
            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="btn btn-primary mt-3">Save</button>
    </form>

    <h4 class="mt-5">Social Links</h4>
    <ul class="list-group mb-4">
        @foreach ($socialLinks as $index => $socialLink)
            <li class="list-group-item">
                <form wire:submit.prevent="updateSocialLink({{ $index }})">
                    <div class="form-group">
                        <label for="icon_class_{{ $index }}">Icon Class</label>
                        <input type="text" class="form-control" id="icon_class_{{ $index }}" wire:model="socialLinks.{{ $index }}.icon_class">
                        @error('socialLinks.' . $index . '.icon_class') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mt-3">
                        <label for="link_{{ $index }}">Link</label>
                        <input type="url" class="form-control" id="link_{{ $index }}" wire:model="socialLinks.{{ $index }}.link">
                        @error('socialLinks.' . $index . '.link') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Update</button>
                    <button type="button" class="btn btn-danger mt-3" wire:click="deleteSocialLink({{ $index }})">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>

    <h4>Add New Social Link</h4>
    <form wire:submit.prevent="addSocialLink">
        <div class="form-group">
            <label for="newIconClass">Icon Class</label>
            <input type="text" class="form-control" id="newIconClass" wire:model="newIconClass">
            @error('newIconClass') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="newLink">Link</label>
            <input type="url" class="form-control" id="newLink" wire:model="newLink">
            @error('newLink') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3">Add</button>
    </form>
</div>

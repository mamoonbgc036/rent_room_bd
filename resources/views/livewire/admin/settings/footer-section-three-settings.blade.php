<div class="container mt-5">
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <h4>Existing Footer Sections</h4>
    <ul class="list-group mb-4">
        @foreach ($footerSectionThrees as $index => $footerSectionThree)
            <li class="list-group-item">
                <form wire:submit.prevent="updateFooterSectionThree({{ $index }})">
                    <div class="form-group">
                        <label for="title_{{ $index }}">Title</label>
                        <input type="text" class="form-control" id="title_{{ $index }}" wire:model="footerSectionThrees.{{ $index }}.title">
                        @error('footerSectionThrees.' . $index . '.title') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mt-3">
                        <label for="link_{{ $index }}">Link</label>
                        <input type="url" class="form-control" id="link_{{ $index }}" wire:model="footerSectionThrees.{{ $index }}.link">
                        @error('footerSectionThrees.' . $index . '.link') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Update</button>
                    <button type="button" class="btn btn-danger mt-3" wire:click="deleteFooterSectionThree({{ $index }})">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>

    <h4>Add New Footer Section</h4>
    <form wire:submit.prevent="addFooterSectionThree">
         <div class="form-group">
            <label for="footerId">Footer ID</label>
            <input type="text" class="form-control" id="footerId" wire:model="footerId">
            @error('footerId') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label for="newTitle">Title</label>
            <input type="text" class="form-control" id="newTitle" wire:model="newTitle">
            @error('newTitle') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="newLink">Link</label>
            <input type="url" class="form-control" id="newLink" wire:model="newLink">
            @error('newLink') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3">Add</button>
    </form>
</div>

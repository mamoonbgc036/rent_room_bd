<div class="container mt-5">
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" wire:model="title">
            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-3">
            <label for="content">Content</label>
            <textarea class="form-control" id="content" wire:model="content" style="height: 200px;"></textarea>
            @error('content') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        

        <button type="submit" class="btn btn-primary mt-3">{{ $isEdit ? 'Update' : 'Save' }}</button>
        <button type="button" class="btn btn-secondary mt-3" wire:click="resetForm">Cancel</button>
    </form>

    <div class="mt-5">
        <h4>Terms and Conditions</h4>
        <ul class="list-group">
            @foreach ($termsConditions as $termsCondition)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $termsCondition->title }}</strong><br>
                        <small>{{ $termsCondition->content }}</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-warning btn-sm" wire:click="edit({{ $termsCondition->id }})">Edit</button>
                        <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $termsCondition->id }})">Delete</button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>

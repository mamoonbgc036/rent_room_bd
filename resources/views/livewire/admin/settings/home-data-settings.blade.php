<div class="container mt-5">
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="form-group">
        <label for="section_title">Section Title</label>
        <input type="text" class="form-control" id="section_title" wire:model="section_title">
        @error('section_title') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <button type="button" class="btn btn-primary mt-3" wire:click="saveSectionTitle">Save Section Title</button>

    <div class="form-group mt-5">
        <h4>Items</h4>
        <ul class="list-group mb-3">
            @foreach ($items as $item)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        @if($item['item_image'])
                            <img src="{{ asset('storage/'.$item['item_image']) }}" alt="Item Image" width="50" class="mr-3">
                        @endif
                        <strong>{{ $item['item_title'] }}</strong><br>
                        <small>{{ $item['item_des'] }}</small>
                    </div>
                    <button type="button" class="btn btn-danger" wire:click="removeItem({{ $item['id'] }})">Remove</button>
                </li>
            @endforeach
        </ul>
    </div>

    <form wire:submit.prevent="addItem">
        <div class="form-group">
            <label for="new_item_image">Item Image</label>
            <input type="file" class="form-control" id="new_item_image" wire:model="newItem.item_image">
            @error('newItem.item_image') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-2">
            <label for="new_item_title">Item Title</label>
            <input type="text" class="form-control" id="new_item_title" wire:model="newItem.item_title">
            @error('newItem.item_title') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mt-2">
            <label for="new_item_des">Item Description</label>
            <textarea class="form-control" id="new_item_des" wire:model="newItem.item_des"></textarea>
            @error('newItem.item_des') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-secondary mt-3">Add New Item</button>
    </form>
</div>

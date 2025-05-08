<div>
    @if($isOpen)
        @include('livewire.admin.maintain.create-maintain')
    @endif

    <div class="d-flex justify-content-end mb-3">
        <button wire:click="create" class="btn btn-lg btn-primary next-button">Create Maintain</button>
    </div>

    <table class="table table-hover bg-white border rounded-lg">
        <thead class="thead-sm thead-black">
            <tr>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Type</th>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Name</th>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Photo</th>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($maintains as $maintain)
                <tr class="shadow-hover-xs-2 bg-hover-white">
                    <td class="align-middle">{{ $maintain->maintainType->type }}</td>
                    <td class="align-middle">{{ $maintain->name }}</td>
                    <td class="align-middle">
                        @if ($maintain->photo)
                            <img src="{{ asset('storage/' . $maintain->photo) }}" alt="Maintain Photo" class="w-24 h-24 rounded-full">
                        @else
                            <img src="default_image_path" alt="Default Photo" class="w-24 h-24 rounded-full">
                        @endif
                    </td>
                    <td class="align-middle">
                        <button wire:click="edit({{ $maintain->id }})" class="btn btn-lg btn-primary next-button mb-3">Edit</button>
                        <button wire:click="delete({{ $maintain->id }})" class="btn btn-lg btn-primary next-button mb-3">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

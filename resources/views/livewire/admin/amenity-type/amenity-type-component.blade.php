<!-- resources/views/livewire/admin/amenity-type/component.blade.php -->
<div>
    @if($isOpen)
        @include('livewire.admin.amenity-type.create-amenity-type')
    @endif

    <div class="d-flex justify-content-end mb-3">
        <button wire:click="create" class="btn btn-lg btn-primary next-button">Create Amenity Type</button>
    </div>

    <table class="table table-hover bg-white border rounded-lg">
        <thead class="thead-sm thead-black">
            <tr>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Type</th>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($amenityTypes as $amenityType)
                <tr class="shadow-hover-xs-2 bg-hover-white">
                    <td class="align-middle">{{ $amenityType->type }}</td>
                    <td class="align-middle">
                        <button wire:click="edit({{ $amenityType->id }})" class="btn btn-lg btn-primary next-button mb-3">Edit</button>
                        <button wire:click="delete({{ $amenityType->id }})" class="btn btn-lg btn-primary next-button mb-3">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

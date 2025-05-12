<div>
    @if($isOpen)
    @include('livewire.admin.property-type.create-property-type')
    @endif

    <div class="d-flex justify-content-end mb-3">
        <button wire:click="create" class="btn btn-sm btn-primary next-button">Create Property Type</button>
    </div>

    <table class="table table-hover bg-white border rounded-lg">
        <thead class="thead-sm thead-black">
            <tr>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Type</th>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($propertyTypes as $propertyType)
            <tr class="shadow-hover-xs-2 bg-hover-white">
                <td class="align-middle">{{ $propertyType->type }}</td>
                <td class="align-middle">
                    <button wire:click="edit({{ $propertyType->id }})" class="btn btn-sm btn-warning next-button">Edit</button>
                    <button wire:click="delete({{ $propertyType->id }})" class="btn btn-sm btn-danger next-button">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
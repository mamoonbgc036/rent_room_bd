<div>
    @if($isOpen)
        @include('livewire.admin.maintain-type.create-maintain-type')
    @endif

    <div class="d-flex justify-content-end mb-3">
        <button wire:click="create" class="btn btn-lg btn-primary next-button">Create Maintain Type</button>
    </div>

    <table class="table table-hover bg-white border rounded-lg">
        <thead class="thead-sm thead-black">
            <tr>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Type</th>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($maintainTypes as $maintainType)
                <tr class="shadow-hover-xs-2 bg-hover-white">
                    <td class="align-middle">{{ $maintainType->type }}</td>
                    <td class="align-middle">
                        <button wire:click="edit({{ $maintainType->id }})" class="btn btn-lg btn-primary next-button mb-3">Edit</button>
                        <button wire:click="delete({{ $maintainType->id }})" class="btn btn-lg btn-primary next-button mb-3">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div>
    @if($isOpen)
    @include('livewire.admin.area.create-zone')
    @endif

    @if (session()->has('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    <div class="d-flex justify-content-end mb-3">
        <button wire:click="create" class="btn btn-lg btn-primary next-button">Create Local Area</button>
    </div>

    <table class="table table-hover bg-white border rounded-lg">
        <thead class="thead-sm thead-black">
            <tr>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Name</th>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Area</th>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($zones as $zone)
            <tr class="shadow-hover-xs-2 bg-hover-white">
                <td class="align-middle">{{ $zone->zone_name }}</td>
                <td class="align-middle">{{ $zone->areas_name }}</td>
                <td class="align-middle">
                    <button class="btn btn-lg btn-primary next-button mb-3">Edit</button>
                    <button class="btn btn-lg btn-primary next-button mb-3">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
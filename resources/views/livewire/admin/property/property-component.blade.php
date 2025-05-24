<!-- resources/views/livewire/admin/property/property-component.blade.php -->
<div>
    @if($isOpen)
    @include('livewire.admin.property.create-property')
    @endif

    <div class="d-flex justify-content-end mb-3">
        <button wire:click="create" class="btn btn-md btn-success next-button">Create Property</button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle bg-white border rounded shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Country</th>
                    <th class="px-4 py-3">District</th>
                    <th class="px-4 py-3">Property Type</th>
                    <th class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($properties as $property)
                <tr>
                    <td class="px-4 py-3">{{ $property->name }}</td>
                    <td class="px-4 py-3">{{ $property->city->name }}</td>
                    <td class="px-4 py-3">{{ $property->area->name }}</td>
                    <td class="px-4 py-3">{{ $property->propertyType->type }}</td>
                    <td class="px-4 py-3">
                        <div class="d-flex gap-2">
                            <button wire:click="edit({{ $property->id }})" class="btn btn-sm btn-outline-primary me-2">✏️</button>
                            <button wire:click="delete({{ $property->id }})" class="btn btn-sm btn-outline-danger">🗑️</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
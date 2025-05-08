<div>
    <!-- Overlay -->
    <div class="overlay" wire:click="closeModal"></div>

    <div class="fixed inset-0 flex items-center justify-center z-50">
        <div class="w-full p-6 bg-white rounded shadow-lg">
            <h2 class="text-2xl font-semibold mb-6">{{ $booking_id ? 'Edit Booking' : 'Create Booking' }}</h2>

            <form wire:submit.prevent="store">
                <div class="group-2 form-grid mb-4">
                    <div class="mb-4">
                        <input type="text" wire:model="name" class="form-control form-control-lg border-0" placeholder="Name">
                        @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <input type="text" wire:model="phone" class="form-control form-control-lg border-0" placeholder="Phone Number">
                        @error('phone') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <input type="email" wire:model="email" class="form-control form-control-lg border-0" placeholder="Email Address">
                        @error('email') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <select wire:model.live="package" class="form-control form-control-lg border-0">
                            <option value="">Select Package</option>
                            @foreach($packages as $id => $packageName)
                                <option value="{{ $id }}">{{ $packageName }}</option>
                            @endforeach
                        </select>
                        @error('package') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <select wire:model="unit" class="form-control form-control-lg border-0">
                            <option value="">Select Unit</option>
                            <option value="Day">Day</option>
                            <option value="Week">Week</option>
                            <option value="Month">Month</option>
                        </select>
                        @error('unit') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <input type="text" wire:model="unit_amount" class="form-control form-control-lg border-0" placeholder="Unit Amount">
                        @error('unit_amount') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <input type="text" wire:model="deposit_amount" class="form-control form-control-lg border-0" placeholder="Deposit Amount">
                        @error('deposit_amount') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <input type="text" wire:model="package_rent" class="form-control form-control-lg border-0" placeholder="Package Rent">
                        @error('package_rent') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <input type="date" wire:model="from_date" class="form-control form-control-lg border-0">
                        @error('from_date') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <input type="date" wire:model="to_date" class="form-control form-control-lg border-0">
                        @error('to_date') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <select wire:model="payment_status" class="form-control form-control-lg border-0">
                            <option value="">Select Payment Status</option>
                            <option value="Due">Due</option>
                            <option value="Paid">Paid</option>
                        </select>
                        @error('payment_status') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" wire:click="closeModal" class="btn btn-lg btn-secondary next-button mb-3 mr-2">Cancel</button>
                    <button type="submit" class="btn btn-lg btn-primary next-button mb-3">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

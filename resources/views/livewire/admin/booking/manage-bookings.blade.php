<div>
    <form wire:submit.prevent="saveBooking">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" wire:model="name" class="form-control">
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" wire:model="phone" class="form-control">
            @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" wire:model="email" class="form-control">
            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="package_id">Package</label>
            <select id="package_id" wire:model="package_id" class="form-control">
                <option value="">Select Package</option>
                @foreach($packages as $package)
                    <option value="{{ $package->id }}">{{ $package->name }}</option>
                @endforeach
            </select>
            @error('package_id') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        @if($roomPrices)
            <div class="form-group">
                <label for="room_price_type">Room Price Type</label>
                <select id="room_price_type" wire:model="payment_option" class="form-control">
                    <option value="">Select Price Type</option>
                    @foreach($roomPrices as $roomPrice)
                        <option value="{{ $roomPrice->type }}">{{ ucfirst($roomPrice->type) }}</option>
                    @endforeach
                </select>
                @error('payment_option') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            @if($payment_option == 'Booking Amount')
                <div class="form-group">
                    <label for="booking_amount">Booking Amount</label>
                    <input type="number" id="booking_amount" wire:model="booking_amount" class="form-control">
                    @error('booking_amount') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            @endif

            @if($payment_option == 'Full Payment')
                <div class="form-group">
                    <label for="full_payment_amount">Full Payment Amount</label>
                    <input type="number" id="full_payment_amount" wire:model="full_payment_amount" class="form-control">
                    @error('full_payment_amount') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            @endif
        @endif

        <div class="form-group">
            <label for="payment_status">Payment Status</label>
            <select id="payment_status" wire:model="payment_status" class="form-control">
                <option value="Due">Due</option>
                <option value="Paid">Paid</option>
            </select>
            @error('payment_status') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Save Booking</button>
    </form>


</div>

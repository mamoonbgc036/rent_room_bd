<div>
    <!-- Button to open modal -->

    <div class="d-flex justify-content-between align-items-center mb-4 container">
        <!-- User Status Filter -->
        <select class="form-control p-1" style="width:auto;" wire:model.live="stay_status" aria-label="User Status">
            <option value="">All Users</option>
            <option value="staying">Staying</option>
            <option value="want_to">Want to Stay</option>
        </select>

        <!-- Search Bar -->
        <input
            type="text"
            class="form-control me-3"
            style="width:auto;"
            placeholder="Search users..."
            wire:model.live="searchTerm"
            style="max-width: 400px;"
        />

        <!-- Create User Button -->
        <button
            wire:click="openModal"
            class="btn btn-primary">
            Create User
        </button>
    </div>



    <!-- Modal for creating user -->
    @if($isOpen)
    <div class="overlay" wire:click="closeModal"></div>
    <div class="modal fixed inset-0 flex items-center justify-center z-50" tabindex="-1" role="dialog" style="display:block;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create User</h5>
                </div>
                <div class="modal-body">
                    <!-- User creation form -->
                    <form wire:submit.prevent="createUser">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control form-control-lg border-0" id="name" wire:model.defer="name">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control form-control-lg border-0" id="email" wire:model.defer="email">
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control form-control-lg border-0" id="password" wire:model.defer="password">
                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select class="form-control form-control-lg border-0" id="role" wire:model="role">
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name}}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="button" wire:click="closeModal" class="btn btn-lg btn-secondary next-button mb-3 mr-2">Cancel</button>
                            <button type="submit" class="btn btn-lg btn-primary next-button mb-3">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- User list -->
    <table class="table table-hover bg-white border rounded-lg">
        <thead class="thead-sm thead-black">
            <tr>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Name</th>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Email</th>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Role</th>
                <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr class="shadow-hover-xs-2 bg-hover-white {{ $user->bookings->count() > 0 ? 'table-warning' : '' }}">
                <td class="align-middle">{{ $user->name }}</td>
                    <td class="align-middle">{{ $user->email }}</td>
                    <td class="border border-gray-200 px-4 py-2">
                        @foreach($user->roles as $role)
                            {{ $role->name }}@if(!$loop->last),@endif
                        @endforeach
                    </td>
                    <td class="align-middle">
                        <!-- View User Button -->
                        <a href="{{ route('admin.users.view', ['userId' => $user->id]) }}"
                           class="btn btn-sm btn-secondary next-button mb-3"
                           title="View User">
                           <i class="fas fa-user"></i>
                        </a>

                        {{-- <button
                            class="btn btn-sm btn-success next-button mb-3"
                            title="View Message"
                            wire:click="loadMessages({{ $user->id }})">
                            <i class="fas fa-envelope"></i>
                        </button> --}}


                        <!-- Delete Button -->
                        <button class="btn btn-sm btn-primary next-button mb-3 mr-2"
                                data-user-id="{{ $user->id }}"
                                title="Delete User" id="deleteUserButton">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


    @if($isMessageModalOpen)
    <div class="overlay" wire:click="$set('isMessageModalOpen', false)"></div>
    <div class="modal fixed inset-0 flex items-center justify-center z-50" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Messages for User ID: {{ $selectedUserId }}</h5>
                    <button type="button" wire:click="$set('isMessageModalOpen', false)" class="close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Message</th>
                                    <th>Sent On</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($messages->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center">No messages available for this user.</td>
                                    </tr>
                                @else
                                    @foreach($messages as $message)
                                        <tr class="{{ $message->is_read ? 'table-mute' : 'table-dark' }}">
                                            <td>{{ $message->message }}</td>
                                            <td>{{ $message->created_at->format('d M, Y H:i') }}</td>
                                            <td>{{ $message->is_read ? 'Read' : 'Unread' }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif


<script>
    document.querySelectorAll('#deleteUserButton').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');

            // Show confirmation dialog
            if (confirm('Are you sure you want to delete this user?')) {
                // If confirmed, trigger the Livewire delete method
                @this.deleteUser(userId);
            }
        });
    });
</script>

</div>

<div>
    <div class="container mt-5">
        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Email Composition Section -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="font-weight-bold mb-0 text-light">Compose Email</h2>
            </div>
            <div class="card-body">
                <div class="form-check">
                    <input type="checkbox" id="selectAll" class="form-check-input"
                        wire:model.live="allUsersSelected">
                    <label for="selectAll" class="form-check-label font-weight-bold">Select All Users</label>
                </div>
                <div class="position-relative 300px">
                    <div class="dropdown w-100">
                        <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Select Users ({{ count($selectedUsers) }} selected)
                        </button>
                        <div class="dropdown-menu w-100 p-3" aria-labelledby="dropdownMenuButton"
                             style="max-height: 400px; overflow-y: auto; display: block; position: absolute; z-index: 1000;">
                            <div class="form-group">
                                <input type="text"
                                       wire:model.live="searchUsers"
                                       placeholder="Search users..."
                                       class="form-control mb-3"
                                       id="userSearchInput"
                                >
                            </div>

                            <div class="user-list" style="max-height: 300px; overflow-y: auto;">
                                @foreach ($filteredUsers as $user)
                                    <div class="form-check">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               wire:model.live="selectedUsers"
                                               value="{{ $user->id }}"
                                               id="user{{ $user->id }}"
                                        >
                                        <label class="form-check-label" for="user{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->email }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                @if (count($selectedUsers) > 0)
                    <div class="form-group mt-4">
                        <label for="selectedUsersList" class="form-label">Selected Users:</label>
                        <div id="selectedUsersList" class="d-flex flex-wrap">
                            @foreach ($selectedUsers as $userId)
                                @php $selectedUser = $users->find($userId); @endphp
                                @if ($selectedUser)
                                    <span class="badge badge-primary mr-2 mb-2">
                                        {{ $selectedUser->name }}
                                        <button type="button" class="close"
                                            wire:click="removeUser({{ $userId }})" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="form-group mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea wire:model="message" class="form-control" id="message" rows="5" placeholder="Write your message here"></textarea>
                    @error('message')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <button wire:click="sendEmails" class="btn btn-primary" {{ $loading ? 'disabled' : '' }}>
                        <span wire:loading.remove>Send Email</span>
                        <span wire:loading><i class="fas fa-spinner fa-spin"></i> Sending...</span>
                    </button>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Custom dropdown behavior
                $(document).ready(function() {
                    // Prevent dropdown from closing
                    $('.dropdown-menu').on('click', function(e) {
                        e.stopPropagation();
                    });

                    // Keep dropdown open when interacting with search input
                    $('#userSearchInput').on('click', function(e) {
                        e.stopPropagation();
                    });

                    // Manually manage dropdown visibility
                    $('#dropdownMenuButton').on('click', function() {
                        $('.dropdown-menu').toggle();
                    });

                    // Close dropdown when clicking outside
                    $(document).on('click', function(e) {
                        if (!$(e.target).closest('.dropdown').length) {
                            $('.dropdown-menu').hide();
                        }
                    });
                });
            });
            </script>
        </div>

        <!-- Sent Messages Section -->
        <div class="card shadow mt-4">
            <div class="card-header bg-secondary text-light">
                <h3 class="font-weight-bold mb-0 text-light">Sent Box</h3>
            </div>
            <div class="card-body">
                @if ($sentMessages->isEmpty())
                    <p>No messages sent yet.</p>
                @else
                    <ul class="list-group">
                        @foreach ($sentMessages as $sentMessage)
                            <li class="list-group-item">
                                <strong>To:</strong> {{ $sentMessage->recipient->name }} <br>
                                <strong>Message:</strong> {{ $sentMessage->message }} <br>
                                <small class="text-muted">Sent on:
                                    {{ $sentMessage->created_at->format('d M Y, H:i') }}</small>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.getElementById('selectButton').addEventListener('click', function() {
            // Close the dropdown
            const dropdown = document.querySelector('.dropdown-toggle');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            dropdown.classList.remove('show');
            dropdownMenu.classList.remove('show');

            // Get selected checkboxes
            const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
            const selectedUsers = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);

            // Update the Livewire component with the selected users
            @this.set('selectedUsers', selectedUsers);
        });

        // Allow clicking checkboxes without closing the dropdown
        document.querySelectorAll('.user-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Optionally, you can add behavior when a checkbox is changed
                // This section is currently empty to maintain dropdown visibility
            });
        });

        // Close the dropdown if clicked outside
        document.addEventListener('click', function(event) {
            const dropdownMenu = document.querySelector('.dropdown-menu');
            const dropdownButton = document.querySelector('.dropdown-toggle');
            if (!dropdownMenu.contains(event.target) && !dropdownButton.contains(event.target)) {
                dropdownButton.classList.remove('show');
                dropdownMenu.classList.remove('show');
            }
        });
    </script>


    <style>
        .card {
            border: none;
            border-radius: 0.5rem;
        }

        .card-header {
            font-size: 1.25rem;
        }

        .form-check-label {
            font-weight: 600;
        }

        .badge {
            display: flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
        }

        .close {
            margin-left: 0.5rem;
        }
    </style>

</div>

<div>
    <div class="modal-header border-0 px-8">
        <h5 class="modal-title">Sign Up</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body p-4 py-sm-7 px-sm-8">
        @if (session()->has('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        <form wire:submit.prevent="register" class="form">
            <div class="form-group mb-4">
                <label for="role" class="sr-only">Role</label>
                <div class="input-group input-group-lg">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                            <i class="far fa-user"></i>
                        </span>
                    </div>
                    <select wire:model.live="role" class="form-control border-0 shadow-none fs-13" id="role" required>
                        <option value="">Select Type</option>
                        <option value="User">User</option>
                        <option value="Partner">Partner</option>
                    </select>
                </div>
                @error('role') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group mb-4">
                <label for="name" class="sr-only">Full name</label>
                <div class="input-group input-group-lg">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                            <i class="far fa-address-card"></i>
                        </span>
                    </div>
                    <input type="text" wire:model.defer="name" class="form-control border-0 shadow-none fs-13" id="name" required placeholder="Full name">
                </div>
                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group mb-4">
                <label for="email" class="sr-only">Email</label>
                <div class="input-group input-group-lg">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                            <i class="far fa-user"></i>
                        </span>
                    </div>
                    <input type="email" wire:model.defer="email" class="form-control border-0 shadow-none fs-13" id="email" required placeholder="Your email">
                </div>
                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group mb-4">
                <label for="phone" class="sr-only">Phone Number</label>
                <div class="input-group input-group-lg">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                            <i class="far fa-phone"></i>
                        </span>
                    </div>
                    <input type="text" wire:model.defer="phone" class="form-control border-0 shadow-none fs-13" id="phone" required placeholder="Phone Number">
                </div>
                @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group mb-4">
                <label for="password" class="sr-only">Password</label>
                <div class="input-group input-group-lg">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                            <i class="far fa-lock"></i>
                        </span>
                    </div>
                    <input type="password" wire:model.defer="password" class="form-control border-0 shadow-none fs-13" id="password" required placeholder="Password">
                </div>
                <p class="form-text">Minimum 8 characters
                </p>
                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="d-flex justify-content-evenly button-login">
                <button type="submit" class="btn btn-primary btn-lg btn-block">Sign Up</button>
            </div>
            @if($role === 'User')
                <div class="form-group mt-2 d-flex align-items-center">
                    <div class="form-check d-flex align-items-center">
                        <input wire:model="agreeUserTerms" type="checkbox" class="form-check-input mr-2" id="agreeUserTerms" required>
                        <label class="form-check-label" for="agreeUserTerms">
                            By creating an account, you agree to RentsandRooms User
                            <a class="text-heading" href="#" data-toggle="modal" data-target="#userPrivacyPolicyModal">
                                <u>Terms of Use</u>
                            </a>
                        </label>
                    </div>
                    @error('agreeUserTerms') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            @elseif($role === 'Partner')
                <div class="form-group mt-2 d-flex align-items-center">
                    <div class="form-check d-flex align-items-center">
                        <input wire:model="agreePartnerTerms" type="checkbox" class="form-check-input mr-2" id="agreePartnerTerms" required>
                        <label class="form-check-label" for="agreePartnerTerms">
                            By creating an account, you agree to RentsandRooms Partner
                            <a class="text-heading" href="#" data-toggle="modal" data-target="#partnerPrivacyPolicyModal">
                                <u>Terms of Use</u>
                            </a>
                        </label>
                    </div>
                    @error('agreePartnerTerms') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            @endif
        </form>
    </div>

            @if($role === 'User')
                <div class="modal fade" id="userPrivacyPolicyModal" tabindex="-1" role="dialog" aria-labelledby="userPrivacyPolicyModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
                        <div class="modal-content term-modal">
                            <div class="modal-body">
                                @livewire('user.terms-condition-component')
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="modal fade" id="partnerPrivacyPolicyModal" tabindex="-1" role="dialog" aria-labelledby="partnerPrivacyPolicyModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
                        <div class="modal-content term-modal">
                            <div class="modal-body">
                                @livewire('user.partner-terms-condition-component')
                            </div>
                        </div>
                    </div>
                </div>
                @endif
</div>

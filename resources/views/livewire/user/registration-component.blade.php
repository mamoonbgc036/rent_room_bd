<div>
    <!-- Modal Header -->
    <div class="modal-header border-0 px-4 pt-4 pb-0">
        <h5 class="modal-title fw-bold text-center">Create an Account</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">X</button>
    </div>

    <!-- Modal Body -->
    <div class="modal-body p-4 pt-3">
        @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        <form wire:submit.prevent="register" class="form">

            <!-- Role Selection -->
            <div class="form-group mb-4">
                <label class="fw-semibold mb-3 d-block">Register As</label>
                <div class="row g-3">
                    <!-- Host Option -->
                    <div class="col-6">
                        <label class="w-100 border rounded-4 text-center p-3 shadow-sm position-relative cursor-pointer role-card">
                            <div>
                                <img src="{{ asset('images/customer.jpg') }}" alt="Guest" class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                            </div>
                            <div>
                                <input type="radio" wire required wire:model.live="role" style="width: 35px; height: 35px;" value="User" />
                            </div>
                            <div class="fw-bold text-dark">Guest</div>
                        </label>
                    </div>
                    <!-- Guest Option -->
                    <div class="col-6">
                        <label class="w-100 border rounded-4 text-center p-3 shadow-sm position-relative cursor-pointer role-card">
                            <div>
                                <img src="{{ asset('images/home_logo.avif') }}" alt="Guest" class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                            </div>
                            <div>
                                <input type="radio" required wire:model.live="role" style="width: 35px; height: 35px;" value="Partner" />
                            </div>
                            <div class="fw-bold text-dark">Host</div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Phone Number -->
            <div class="form-group mb-4">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-light border-0 text-muted">
                        <i class="far fa-phone"></i>
                    </span>
                    <input type="text" wire:model.defer="phone" class="form-control border-0 shadow-none fs-6" placeholder="Phone Number" required />
                </div>
                @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Password -->
            <div class="form-group mb-4">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-light border-0 text-muted">
                        <i class="far fa-lock"></i>
                    </span>
                    <input type="password" wire:model.defer="password" class="form-control border-0 shadow-none fs-6" placeholder="Password" required />
                </div>
                <small class="form-text text-muted">Minimum 8 characters</small>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Submit Button -->
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-semibold shadow">Sign Up</button>
            </div>

            <!-- Divider -->
            <div class="text-center my-3 text-muted fw-semibold">OR</div>

            <!-- Facebook Login -->
            <div class="d-flex justify-content-center">
                <a href="{{ url('auth/facebook') }}" class="btn d-flex align-items-center gap-2 px-4 text-white fw-semibold shadow"
                    style="background-color: #3b5998; border-color: #3b5998;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="white" class="bi bi-facebook" viewBox="0 0 16 16">
                        <path d="M8.94 6.56H7.75V5.69c0-.32.21-.39.35-.39h.79V3.68L7.77 3.5c-1.36 0-1.72.65-1.72 1.64v1.42H5V8.44h1.05v4.06h1.7V8.44h1.15l.18-1.88z" />
                    </svg>
                    <span>Signup with Facebook</span>
                </a>
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
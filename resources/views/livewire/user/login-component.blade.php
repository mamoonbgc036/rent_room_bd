<div>
    <div class="modal-header border-0 px-8">
        <h5 class="modal-title">Sign In</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body p-4 py-sm-7 px-sm-8">
        @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form wire:submit.prevent="login" class="form">
            <div class="form-group mb-4">
                <label for="phone" class="sr-only">Phone</label>
                <div class="input-group input-group-lg">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                            <i class="far fa-user"></i>
                        </span>
                    </div>
                    <input wire:model="phone" type="phone" class="form-control border-0 shadow-none fs-13" id="phone" required placeholder="Your phone">
                </div>
            </div>
            <div class="form-group mb-4">
                <label for="password" class="sr-only">Password</label>
                <div class="input-group input-group-lg">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                            <i class="far fa-lock"></i>
                        </span>
                    </div>
                    <input wire:model="password" type="password" class="form-control border-0 shadow-none fs-13" id="password" required placeholder="Password">
                    <div class="input-group-append">
                        <span class="input-group-text bg-gray-01 border-0 text-body fs-18" id="togglePassword" style="cursor: pointer;">
                            <i class="far fa-eye-slash"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex mb-4">
                <div class="form-check">
                    <input wire:model="remember" class="form-check-input" type="checkbox" value="1" id="remember-me">
                    <label class="form-check-label" for="remember-me">
                        Remember me
                    </label>
                </div>
                <a href="/forgot-password" class="d-inline-block ml-auto text-dark fs-15">
                    Lost password?
                </a>
            </div>
            <div class="d-flex justify-content-evenly button-login">
                <button type="submit" class="btn btn-primary btn-lg flex-grow-1 gap-4">Sign In</button>

            </div>
        </form>
    </div>
</div>
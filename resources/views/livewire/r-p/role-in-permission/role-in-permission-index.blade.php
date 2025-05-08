<div class="container mt-5">
    <!-- Add Role In Permission Button -->
    <div class="mb-4 text-right">
        <a href="{{ route('role-permission.create') }}" class="btn btn-success">
            Add Role In Permission
        </a>
    </div>

    <!-- List of Roles with Permissions -->
    <div class="row">
        @foreach($roles as $role)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $role->name }}</h5>
                        <a href="{{ route('role-permission.edit', ['role_id' => $role->id]) }}" class="btn btn-link text-primary">
                            Edit
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            @foreach($role->permissions as $permission)
                                <span class="badge badge-primary mr-2 mb-2">{{ $permission->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
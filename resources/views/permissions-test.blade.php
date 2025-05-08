    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Permissions Test</div>

                    <div class="card-body">
                        <!-- Section 1: Test content accessible to all users -->
                        <div class="mb-4">
                            <h3>Section 1: Public Content</h3>
                            <p>This section is accessible to all users.</p>
                        </div>

                        <!-- Section 2: Test content accessible only to users with 'create-post' permission -->
                        @can('create-post')
                        <div class="mb-4">
                            <h3>Section 2: Content for Users with 'create-post' Permission</h3>
                            <p>This section is only accessible to users with the 'create-post' permission.</p>
                        </div>
                        @endcan

                        <!-- Section 3: Test content accessible only to users with 'edit-post' permission -->
                        @can('edit-post')
                        <div class="mb-4">
                            <h3>Section 3: Content for Users with 'edit-post' Permission</h3>
                            <p>This section is only accessible to users with the 'edit-post' permission.</p>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

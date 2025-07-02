<?php
use App\Models\Payment;
use App\Livewire\PaymentForm;
use App\Livewire\Actions\Logout;
use App\Livewire\RP\RoleManager;
use App\Livewire\RP\UserManager;
use App\Livewire\User\BookingList;
use App\Livewire\User\PackageList;
use App\Livewire\User\PackageShow;
use App\Livewire\User\ShowBooking;
use Illuminate\Support\Facades\DB;
use App\Livewire\User\UserMessages;
use App\Livewire\User\HomeComponent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Area\LocalArea;
use App\Livewire\RP\PermissionManager;
use App\Livewire\User\BookingComplete;
use App\Http\Controllers\SSOController;
use Illuminate\Support\Facades\Artisan;
use App\Livewire\Admin\BookingComponent;
use App\Livewire\Admin\PaymentComponent;
use App\Livewire\Admin\ProfileComponent;
use App\Livewire\User\CheckoutComponent;
use App\Livewire\Admin\SendMailComponent;
use App\Livewire\Admin\SettingsComponent;
use App\Livewire\Admin\UserViewComponent;
use App\Http\Controllers\StripeController;
use App\Livewire\Admin\Area\AreaComponent;
use App\Livewire\Admin\City\CityComponent;
use App\Livewire\Admin\BookingShowComponent;
use App\Livewire\Admin\AdminBookingComponent;
use App\Livewire\Admin\Settings\HeroSettings;
use App\Livewire\User\PrivacyPolicyComponent;
use App\Livewire\Admin\Booking\ManageBookings;
use App\Livewire\User\TermsConditionComponent;
use App\Livewire\Admin\Settings\FooterSettings;
use App\Livewire\Admin\Settings\HeaderSettings;
use App\Livewire\Admin\Amenity\AmenityComponent;
use App\Livewire\Admin\Country\CountryComponent;
use App\Livewire\Admin\Package\PackageComponent;
use App\Livewire\Admin\User\ManageUserComponent;
use App\Http\Controllers\Auth\FacebookController;
use App\Livewire\Admin\AdminBookingEditComponent;
use App\Livewire\Admin\Settings\HomeDataSettings;
use App\Livewire\Admin\Maintain\MaintainComponent;
use App\Livewire\Admin\Property\PropertyComponent;
use App\Livewire\Admin\Dashboard\DashboardComponent;
use App\Livewire\Admin\Package\EditPackageComponent;
use App\Livewire\Admin\Package\ShowPackageComponent;
use App\Http\Controllers\SslCommerzPaymentController;
use App\Livewire\User\PartnerTermsConditionComponent;
use App\Livewire\Admin\Package\CreatePackageComponent;
use App\Livewire\Admin\Settings\PrivacyPolicySettings;
use App\Livewire\Admin\Settings\TermsConditionSettings;
use App\Livewire\Admin\AmenityType\AmenityTypeComponent;
use App\Livewire\Admin\Settings\FooterSectionTwoSettings;
use App\Livewire\Admin\MaintainType\MaintainTypeComponent;
use App\Livewire\Admin\PropertyType\PropertyTypeComponent;
use App\Livewire\Admin\Settings\FooterSectionFourSettings;
use App\Livewire\RP\RoleInPermission\RoleInPermissionEdit;
use App\Livewire\Admin\Settings\FooterSectionThreeSettings;
use App\Livewire\RP\RoleInPermission\RoleInPermissionIndex;
use App\Livewire\RP\RoleInPermission\RoleInPermissionCreate;
use App\Livewire\Admin\Settings\ParetnerTermsConditionSettings;

Route::get('/payment', PaymentForm::class);
Route::post('stripe', [StripeController::class, 'stripe'])->name('stripe');
Route::get('sucsess', [CheckoutComponent::class, 'sucsess'])->name('sucsess');
Route::get('cancel', [CheckoutComponent::class, 'cancel'])->name('cancel');

Route::get('/storage-link', function () {
    try {
        Artisan::call('storage:link');
        return "The [public/storage] directory has been linked.";
    } catch (\Exception $e) {
        return "There was an error: " . $e->getMessage();
    }
})->name('storage.link');

// Route::view('/', 'welcome');

Route::get('/', HomeComponent::class)->name('home');
Route::post('/logout', [Logout::class, 'logout'])->name('logout');


Route::get('/packages', PackageList::class)->name('package.list');
Route::get('booking-complete/{bookingId}', BookingComplete::class)->name('booking.complete');
Route::get('/package/{partnerSlug}/{packageSlug}', PackageShow::class)->name('package.show');
Route::get('/partner/{partnerSlug}/packages', PackageList::class)->name('partner.packages');


Route::get('/seeder', function () {
    Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RolesAndSuperAdminSeeder']);
    return 'Roles and Super Admin seeder ran successfully!';
});

Route::get('/migrate', function () {
    Artisan::call('migrate');
});


Route::get('auth/facebook', [FacebookController::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [FacebookController::class, 'handleFacebookCallback']);



Route::get('/checkout/success', function () {
    return view('checkout.success');
})->name('checkout.success');

// SSLCOMMERZ Start
Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);

Route::post('/pay', [SslCommerzPaymentController::class, 'index']);
Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);

Route::post('/success', [SslCommerzPaymentController::class, 'success']);
Route::post('/fail', [SslCommerzPaymentController::class, 'fail']);
Route::post('/cancel', [SslCommerzPaymentController::class, 'cancel']);

Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);

Route::get('/checkout/cancel', function () {
    return view('checkout.cancel');
})->name('checkout.cancel');
Route::get('/stripe/return', [StripeController::class, 'handleStripeReturn'])->name('stripe.return');
Route::get('/stripe/success/{booking}', function (App\Models\Booking $booking) {
    if (!request()->has('session_id')) {
        return redirect()->route('home');
    }

    $stripe = new \Stripe\StripeClient(config('stripe.stripe_sk'));
    $session = $stripe->checkout->sessions->retrieve(request()->get('session_id'));

    if ($session->payment_status === 'paid') {
        DB::transaction(function () use ($booking, $session) {
            $booking->update(['payment_status' => 'completed']);

            // Update or create payment record
            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'payment_method' => 'card',
                    'amount' => $booking->total_amount,
                    'status' => 'completed',
                    'transaction_id' => $session->payment_intent,
                    'payment_option' => $booking->payment_option
                ]
            );
        });

        session()->flash('success', 'Payment completed successfully!');
        return redirect()->route('booking.complete', $booking->id);
    }

    return redirect()->route('checkout')->with('error', 'Payment failed');
})->name('stripe.success');

Route::get('/stripe/cancel/{booking}', function (App\Models\Booking $booking) {
    DB::transaction(function () use ($booking) {
        $booking->delete();
        Payment::where('booking_id', $booking->id)->delete();
    });

    return redirect()->route('checkout')->with('error', 'Payment cancelled');
})->name('stripe.cancel');

Route::get('/privacy-policy', PrivacyPolicyComponent::class)->name('privacy-policy');
Route::get('/terms-condition', TermsConditionComponent::class)->name('terms-condition');
Route::get('/partner-terms-condition', PartnerTermsConditionComponent::class)->name('partner-terms-condition');



Route::get('/payment/{uniqueId}', PaymentComponent::class)->name('payment.page');
Route::get('/payment/success', function (Request $request) {
    // Handle successful payment
    if ($request->session_id && $request->payment_link) {
        $paymentLink = PaymentLink::where('unique_id', $request->payment_link)->first();
        if ($paymentLink) {
            Payment::create([
                'booking_id' => $paymentLink->booking_id,
                'payment_method' => 'card',
                'amount' => $paymentLink->package_price + $paymentLink->security_amount,
                'transaction_id' => $request->session_id,
                'status' => 'success'
            ]);

            $paymentLink->update(['status' => 'completed']);
        }
    }
    return view('payment.success');
})->name('payment.success');

Route::get('/payment/cancel', function () {
    return view('payment.cancel');
})->name('payment.cancel');


Route::middleware(['auth'])->group(function () {
    // session()->forget('checkout_data');
    Route::get('/roles', RoleManager::class)->name('roles');
    Route::get('/permissions', PermissionManager::class)->name('permissions');
    Route::get('/role-in-permission', RoleInPermissionIndex::class)->name('role.in.permission');
    Route::get('/role-in-permission-create', RoleInPermissionCreate::class)->name('role-permission.create');
    Route::get('/role-permission/edit/{role_id}', RoleInPermissionEdit::class)->name('role-permission.edit');

    Route::get('/sso/ghorermenu', [SSOController::class, 'redirectToGhorerMenu'])
    ->name('sso.ghorermenu');

    Route::get('/users', UserManager::class)->name('users');
    Route::get('/profile', ProfileComponent::class)->name('profile');

    Route::get('/admin/countries', CountryComponent::class)->name('countries')->middleware('can:package.setup');
    Route::get('/admin/cities', CityComponent::class)->name('cities')->middleware('can:package.setup');
    Route::get('/admin/areas', AreaComponent::class)->name('areas')->middleware('can:package.setup');
    Route::get('/admin/local-area', LocalArea::class)->name('local.area')->middleware('can:package.setup');
    Route::get('/admin/property-types', PropertyTypeComponent::class)->name('property-types')->middleware('can:package.setup');
    Route::get('/admin/properties', PropertyComponent::class)->name('properties')->middleware('can:package.setup');
    Route::get('/admin/maintain-type', MaintainTypeComponent::class)->name('maintain-type')->middleware('can:package.setup');
    Route::get('/admin/maintain', MaintainComponent::class)->name('maintain')->middleware('can:package.setup');
    Route::get('/admin/amenity-type', AmenityTypeComponent::class)->name('amenity-type')->middleware('can:package.setup');
    Route::get('/admin/amenities', AmenityComponent::class)->name('amenities')->middleware('can:package.setup');
    Route::get('/admin/packages', PackageComponent::class)->name('admin.packages');
    Route::get('/admin/packages/create', CreatePackageComponent::class)->name('admin.packages.create');
    Route::get('/admin/package/edit/{packageId}', EditPackageComponent::class)->name('admin.package.edit');
    Route::get('/packages/{packageId}/show', ShowPackageComponent::class)->name('packages.show');
    Route::get('/admin/bookings', ManageBookings::class)->name('admin.bookings');
    Route::get('/admin/users/manage', ManageUserComponent::class)->name('users.manage');
    Route::get('/admin/users/{userId}', action: UserViewComponent::class)->name('admin.users.view');
    Route::post('user/documents/store', [UserViewComponent::class, 'saveDocuments'])->name('user.documents.store');
    Route::get('/admin/bookings/create', AdminBookingComponent::class)
        ->name('admin.bookings.create');
    Route::get('/admin/bookings/{booking}/edit', AdminBookingEditComponent::class)
        ->name('admin.bookings.edit');

    Route::get('/dashboard/main', DashboardComponent::class)->name('dashboard')->middleware('can:dashboard');
    Route::get('/checkout', CheckoutComponent::class)->name('checkout');

    Route::get('/user/bookings', BookingList::class)->name('user.bookings.index');
    Route::get('/bookings/{id}', ShowBooking::class)->name('bookings.show');

    Route::get('admin/bookings', BookingComponent::class)->name('admin.bookings.index');
    Route::get('admin/bookings/{id}', BookingShowComponent::class)->name('admin.bookings.show');

    Route::get('/admin/settings', SettingsComponent::class)->name('site.settings');

    Route::post('/profile/update', [ProfileComponent::class, 'update'])->name('profile.update');

    Route::get('/send-mail', SendMailComponent::class)->name('send.mail')->middleware('can:send-emails');

    Route::get('/user/messages', UserMessages::class)->name('user.messages')->middleware('can:massage');


    Route::put('/packages/{package}/documents', [ProfileComponent::class, 'updateDocuments'])
        ->name('partner.package.documents.update');



    Route::prefix('admin/packages/{package}/documents')->name('admin.packages.documents.')->group(function () {
        Route::post('/update', [UserViewComponent::class, 'updateDocuments'])->name('update');
        Route::delete('/{type}', [UserViewComponent::class, 'deletePartnerDocument'])->name('delete');
    });


    Route::get('/admin/logo', HeaderSettings::class)->name('logo');
    Route::get('/admin/hero-section', HeroSettings::class)->name('hero-section');
    Route::get('/admin/home-data', HomeDataSettings::class)->name('home-data');
    Route::get('/admin/privacy-policy', PrivacyPolicySettings::class)->name('admin.privacy-policy');
    Route::get('/admin/terms-condition', TermsConditionSettings::class)->name('admin-terms-condition');
    Route::get('/admin/partner-terms-condition', ParetnerTermsConditionSettings::class)->name('admin-partner-terms-condition');
    Route::get('/admin/footer-main', FooterSettings::class)->name('footer-main');
    Route::get('/admin/footer-two', FooterSectionTwoSettings::class)->name('footer-two');
    Route::get('/admin/footer-three', FooterSectionThreeSettings::class)->name('footer-three');
    Route::get('/admin/footer-four', FooterSectionFourSettings::class)->name('footer-four');
});

Route::get('/test-email', function () {
    Mail::raw('This is a test email', function ($message) {
        $message->to('designfic.com@gmail.com')
            ->subject('Test Email');
    });

    return 'Email sent successfully';
});

Route::get('/test', function () {
    return "You have permission to access this test content!";
})->middleware('can:create-post');
Route::get('/permissions-test', function () {
    return view('permissions-test');
})->middleware('auth');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboards');

// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');

require __DIR__ . '/auth.php';

<?php

namespace App\Livewire\Admin;

use App\Models\AgreementDetail;
use App\Models\BankDetail;
use App\Models\User;
use App\Models\UserDocument;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Package;
use App\Models\PackagePayment;
use App\Models\Payment;
use App\Models\PaymentLink;
use App\Models\UserDetail;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use SendGrid\Mail\Mail as SendGridMail;


class UserViewComponent extends Component
{
    use WithFileUploads;

    public $bookings;
    public $userId;
    public $proof_type_1;
    public $proof_path_1;
    public $proof_type_2;
    public $proof_path_2;
    public $proof_type_3;
    public $proof_path_3;
    public $proof_type_4;
    public $proof_path_4;
    public $user;
    public $documents = [];
    public $bankDetail = ['name' => '', 'sort_code' => '', 'account' => ''];
    public $agreementDetail = ['agreement_type' => '', 'duration' => '', 'amount' => '', 'deposit' => ''];
    public $userDetail = ['phone' => '', 'occupied_address' => '', 'package' => '', 'booking_type' => '', 'duration_type' => '', 'payment_status' => 'Pending', 'package_price' => '', 'security_amount' => '', 'entry_date' => '', 'stay_status' => 'Pending', 'package_id' => null,];
    public $editingDocumentId;
    public $editPersonName;
    public $editPassport;
    public $editNidOrOther;
    public $showEditModal = false;
    public $isDetailEditing = false;
    public $showForm = false;
    public $packages = [];

    public $userData = [];
    public $isEditModalOpen = false;

    public $paymentLink;
    public $paymentDetail = [
        'amount' => '',
        'payment_date' => '',
        'duration_type' => '',
        'payment_status' => 'Pending'
    ];
    public $payments = [];
    public $isPaymentEditing = false;
    public $editingPaymentId = null;
    public $showMilestoneSelectionModal = false;
    public $currentBookingId;
    public $selectedMilestoneAmount;
    public $milestoneOptions = [];
    public array $paymentLinks = [];
    public $showDeletePaymentModal = false;
    public $paymentToDelete = null;

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->user = User::with([
            'documents',
            'bankDetail',
            'agreementDetail',
            'userDetail',
            'bookings.payments',
            'bookings.bookingPayments',
            'bookings.package',
            'bookings.paymentLinks'
        ])->findOrFail($userId);

        $this->initializeUserData();

        // Load bookings if user has the User role
        if ($this->user->hasRole('User')) {
            $this->loadBookings();
            $this->loadPaymentLinks();
        }

        $this->packages = Package::all();


        $this->refreshPackages();
    }

    private function refreshPackages()
    {
        $user = $this->user;


        $with = ['creator', 'assignedPartner', 'assignedBy', 'country', 'city', 'area', 'property'];

            $this->packages = Package::with($with)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('assigned_to', $user->id);
                })
                ->get();

    }


    private function loadPaymentLinks()
    {
        // Initialize payment links array
        $this->paymentLinks = [];

        foreach ($this->user->bookings as $booking) {
            // Get the latest active payment link for each booking
            $latestLink = PaymentLink::where('booking_id', $booking->id)
                ->whereIn('status', ['pending', 'pending_bank_transfer'])
                ->latest()
                ->first();

            if ($latestLink) {
                $this->paymentLinks[$booking->id] = $latestLink->unique_id;
            }
        }
    }

    public function updatePaymentStatusForPayment($paymentId, $status)
    {
        try {
            DB::beginTransaction();

            $payment = Payment::with(['booking'])->findOrFail($paymentId);
            $booking = $payment->booking;

            // Update payment status
            $payment->update([
                'status' => $status,
                'paid_at' => $status === 'Paid' ? now() : null
            ]);

            // Find corresponding booking payment
            $bookingPayment = BookingPayment::where('booking_id', $booking->id)
                ->where('amount', $payment->amount)
                ->orderBy('due_date', 'asc')
                ->first();

            if ($bookingPayment) {
                if ($status === 'Paid') {
                    // Update booking payment for paid status
                    $bookingPayment->update([
                        'payment_status' => 'paid',
                        'payment_id' => $payment->id,
                        'paid_at' => now(),
                        'payment_method' => $payment->payment_method,
                        'transaction_reference' => $payment->transaction_id
                    ]);

                    // Invalidate any existing payment links
                    PaymentLink::where('booking_payment_id', $bookingPayment->id)
                        ->update(['status' => 'completed']);
                } else {
                    // For pending status, reset the booking payment
                    $bookingPayment->update([
                        'payment_status' => 'pending',
                        'payment_id' => null,
                        'paid_at' => null,
                        'payment_method' => null,
                        'transaction_reference' => null
                    ]);

                    // Invalidate any existing payment links to allow new ones
                    PaymentLink::where('booking_payment_id', $bookingPayment->id)
                        ->where('status', '!=', 'completed')
                        ->update(['status' => 'expired']);
                }

                // If there was a previous payment, update its status
                if ($bookingPayment->payment_id && $bookingPayment->payment_id !== $payment->id) {
                    Payment::where('id', $bookingPayment->payment_id)
                        ->update(['status' => 'cancelled']);
                }
            }

            // Recalculate booking status
            $this->updateBookingStatus($booking);

            DB::commit();
            $this->loadBookings();

            $statusText = $status === 'Paid' ? 'marked as paid' : 'reset to pending';
            flash()->success("Payment successfully {$statusText}!");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment status update failed', [
                'payment_id' => $paymentId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            flash()->error('Failed to update payment status: ' . $e->getMessage());
        }
    }

    /**
     * Update the overall booking status based on payments
     */
    protected function updateBookingStatus($booking)
    {
        $totalAmount = (float) $booking->price + (float) $booking->booking_price;
        $totalPaid = $booking->payments()
            ->where('status', 'Paid')
            ->sum('amount');

        $allMilestonesCount = $booking->bookingPayments()->count();
        $paidMilestonesCount = $booking->bookingPayments()
            ->where('payment_status', 'paid')
            ->count();

        if ($totalPaid >= $totalAmount && $paidMilestonesCount === $allMilestonesCount) {
            $status = 'paid';
        } elseif ($totalPaid > 0) {
            $status = 'partially_paid';
        } else {
            $status = 'pending';
        }

        $booking->update([
            'payment_status' => $status,
            'last_payment_date' => $paidMilestonesCount > 0 ? now() : null
        ]);
    }



    private function updateBookingPaymentStatus($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $totalPaid = $booking->payments->where('status', 'Paid')->sum('amount');
        $totalPrice = $booking->price + $booking->booking_price;

        $booking->payment_status = $totalPaid >= $totalPrice ? 'Paid' : 'Pending';
        $booking->save();
    }

    private function loadBookings()
    {
        $this->bookings = $this->user->bookings->map(function ($booking) {
            $totalPrice = (float) $booking->price + (float) $booking->booking_price;
            $totalPaid = $booking->payments->where('status', 'Paid')->sum('amount');
            $remainingBalance = $totalPrice - $totalPaid;
            $paymentPercentage = $totalPrice > 0 ? ($totalPaid / $totalPrice * 100) : 0;

            return array_merge($booking->toArray(), [
                'payments' => $booking->payments,
                'package' => $booking->package,
                'bookingPayments' => $booking->bookingPayments, // Add this
                'payment_summary' => [
                    'total_price' => $totalPrice,
                    'total_paid' => $totalPaid,
                    'remaining_balance' => $remainingBalance,
                    'payment_percentage' => $paymentPercentage
                ]
            ]);
        });
    }

    public function confirmDeletePayment($paymentId)
    {
        $this->paymentToDelete = $paymentId;
        $this->showDeletePaymentModal = true;
    }

    public function deletePayment()
    {
        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($this->paymentToDelete);
            $booking = $payment->booking;

            // Find corresponding booking payment
            $bookingPayment = BookingPayment::where('booking_id', $booking->id)
                ->where('amount', $payment->amount)
                ->orderBy('due_date', 'asc')
                ->first();

            if ($bookingPayment) {
                // Update only the status for booking payment
                $bookingPayment->update([
                    'payment_status' => 'pending'
                ]);

                // Invalidate any related payment links
                PaymentLink::where('booking_payment_id', $bookingPayment->id)
                    ->where('status', '!=', 'completed')
                    ->update(['status' => 'expired']);
            }

            // Delete the payment
            $payment->delete();

            // Recalculate booking status
            $this->updateBookingStatus($booking);

            DB::commit();

            $this->showDeletePaymentModal = false;
            $this->paymentToDelete = null;
            $this->loadBookings();

            flash()->success('Payment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Delete payment failed', [
                'payment_id' => $this->paymentToDelete,
                'error' => $e->getMessage()
            ]);
            flash()->error('Failed to delete payment: ' . $e->getMessage());
        }
    }

    private function initializeUserData()
    {
        $this->userData = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
        ];

        $this->documents = $this->user->documents->map(function ($doc) {
            return [
                'person_name' => $doc->person_name,
                'passport' => $doc->passport,
                'nid_or_other' => $doc->nid_or_other,
                'payslip' => $doc->payslip,
                'student_card' => $doc->student_card,
            ];
        })->toArray();

        $this->bankDetail = $this->user->bankDetail?->toArray() ?? [];
        $this->agreementDetail = $this->user->agreementDetail?->toArray() ?? [];
        $this->userDetail = $this->user->userDetail?->toArray() ?? [];
        $this->showForm = $this->user->documents->isEmpty();
    }


    private function calculatePaymentSummary($booking)
    {
        $totalPrice = $booking->price + $booking->booking_price;
        $totalPaid = $booking->payments->sum('amount');

        return [
            'total_price' => $totalPrice,
            'total_paid' => $totalPaid,
            'remaining_balance' => $totalPrice - $totalPaid,
            'payment_percentage' => $totalPaid > 0 ? ($totalPaid / $totalPrice * 100) : 0,
        ];
    }

    private function getBookingStatus($booking)
    {
        return [
            'class' => $this->getStatusClass($booking->payment_status),
            'text' => ucfirst($booking->payment_status)
        ];
    }

    private function getStatusClass($status)
    {
        return [
            'pending' => 'warning',
            'paid' => 'success',
            'partial' => 'info',
            'cancelled' => 'danger',
        ][$status] ?? 'secondary';
    }

    public function enableDetailEditing()
    {
        $this->isDetailEditing = true;
    }
    public function enablePaymentEditing()
    {
        $this->isPaymentEditing = true;
    }

    public function toggleForm()
    {
        // Toggle the form visibility
        $this->showForm = !$this->showForm;
    }

    private function loadUserData()
    {
        $user = User::with('package')->find($this->userId);

        if ($user) {
            $this->proof_type_1 = $user->proof_type_1;
            $this->proof_path_1 = $user->proof_path_1;
            $this->proof_type_2 = $user->proof_type_2;
            $this->proof_path_2 = $user->proof_path_2;
            $this->proof_type_3 = $user->proof_type_3;
            $this->proof_path_3 = $user->proof_path_3;
            $this->proof_type_4 = $user->proof_type_4;
            $this->proof_path_4 = $user->proof_path_4;
        }
    }

    public function openEditModal()
    {
        $this->isEditModalOpen = true;
    }

    public function closeEditModal()
    {
        $this->isEditModalOpen = false;
    }

    public function updatePartner()
    {


        if ($this->proof_path_1 && $this->proof_path_1 instanceof \Illuminate\Http\UploadedFile) {
            $this->user->proof_path_1 = $this->proof_path_1->store('documents', 'public');
        }
        if ($this->proof_path_2 && $this->proof_path_2 instanceof \Illuminate\Http\UploadedFile) {
            $this->user->proof_path_2 = $this->proof_path_2->store('documents', 'public');
        }
        if ($this->proof_path_3 && $this->proof_path_3 instanceof \Illuminate\Http\UploadedFile) {
            $this->user->proof_path_3 = $this->proof_path_3->store('documents', 'public');
        }
        if ($this->proof_path_4 && $this->proof_path_4 instanceof \Illuminate\Http\UploadedFile) {
            $this->user->proof_path_4 = $this->proof_path_4->store('documents', 'public');
        }

        // Update proof types
        $this->user->proof_type_1 = "Gas Certificate";
        $this->user->proof_type_2 = "Electric Certificate";
        $this->user->proof_type_3 = "Landlord Certificate (HMO/Other)";
        $this->user->proof_type_4 = "Building Insurance Certificate";

        $this->user->save();
        // Flash success message
        flash()->success('Partner documents is saved');
    }

    public function addPerson()
    {
        $this->documents[] = ['person_name' => '', 'passport' => null, 'nid_or_other' => null, 'payslip' => null, 'student_card' => null];
    }

    public function removePerson($index)
    {
        unset($this->documents[$index]);
        $this->documents = array_values($this->documents); // Reindex the array
    }

    public function saveDocuments()
    {

        // Clear existing documents for the user
        $this->user->documents()->delete();

        foreach ($this->documents as $document) {
            // Handle passport file upload
            $passportPath = $document['passport'];
            if (isset($document['passport']) && $document['passport'] instanceof \Illuminate\Http\UploadedFile) {
                $passportPath = $document['passport']->store('documents', 'public');
            }

            // Handle NID or other file upload
            $nidPath = $document['nid_or_other'];
            if (isset($document['nid_or_other']) && $document['nid_or_other'] instanceof \Illuminate\Http\UploadedFile) {
                $nidPath = $document['nid_or_other']->store('documents', 'public');
            }

            // Handle payslip file upload
            $payslipPath = $document['payslip'];
            if (isset($document['payslip']) && $document['payslip'] instanceof \Illuminate\Http\UploadedFile) {
                $payslipPath = $document['payslip']->store('documents', 'public');
            }

            // Handle student_card file upload
            $studentCardPath = $document['student_card'];
            if (isset($document['student_card']) && $document['student_card'] instanceof \Illuminate\Http\UploadedFile) {
                $studentCardPath = $document['student_card']->store('documents', 'public');
            }

            // Create new document entry
            $this->user->documents()->create([
                'person_name' => $document['person_name'] ?? null,
                'passport' => $passportPath,
                'nid_or_other' => $nidPath,
                'payslip' => $payslipPath,
                'student_card' => $studentCardPath,
            ]);
        }
        $this->showForm = false;
        $this->user->load('documents');
        flash()->success('User document is saved.');
        return redirect()->back();
    }

    public function editDocument($documentId)
    {
        $document = $this->user->documents()->findOrFail($documentId);
        $this->editingDocumentId = $documentId;
        $this->editPersonName = $document->person_name;
        $this->editPassport = null;  // Reset file inputs
        $this->editNidOrOther = null;
        $this->showEditModal = true;  // Show the modal
    }

    public function updateDocument()
    {
        $this->validate([
            'editPersonName' => 'nullable|string|max:255',
            'editPassport' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'editNidOrOther' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $document = $this->user->documents()->findOrFail($this->editingDocumentId);
        $document->person_name = $this->editPersonName;

        if ($this->editPassport) {
            $document->passport = $this->editPassport->store('documents', 'public');
        }

        if ($this->editNidOrOther) {
            $document->nid_or_other = $this->editNidOrOther->store('documents', 'public');
        }

        $document->save();

        $this->resetEditForm();
        $this->showEditModal = false;
        $this->showForm = false;
        $this->user->load('documents');
        flash()->success('Partner documet is saved.');
    }
    public function deleteDocument($documentId)
    {
        $document = $this->user->documents()->findOrFail($documentId);
        $document->delete();
        $this->user->load('documents');
        if (empty($this->user->documents)) {
            $this->showForm = true;
        }
    }


    private function resetEditForm()
    {
        $this->editPersonName = '';
        $this->editPassport = null;
        $this->editNidOrOther = null;
        $this->editingDocumentId = null;
    }


    public function updateDocuments(Request $request, Package $package)
    {
        $request->validate([
            'documents.gas_certificate' => 'nullable|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.electric_certificate' => 'nullable|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.landlord_certificate' => 'nullable|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.building_insurance' => 'nullable|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.pat_certificate' => 'nullable|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.epc_certificate' => 'nullable|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        try {
            DB::beginTransaction();

            // Check if package belongs to user OR is assigned to user
            if ($package->user_id !== $this->user->id && $package->assigned_to !== $this->user->id) {
                throw new \Exception('Package does not belong to this user');
            }

            foreach ($request->file('documents', []) as $type => $file) {
                if ($file) {
                    $existingDoc = $package->documents()
                        ->where('type', $type)
                        ->first();

                    if ($existingDoc && Storage::exists($existingDoc->path)) {
                        Storage::delete($existingDoc->path);
                    }

                    $path = $file->store('package-documents', 'public');

                    $package->documents()->updateOrCreate(
                        ['type' => $type],
                        [
                            'path' => $path,
                            'expires_at' => now()->addYear(),
                            'updated_by' => auth()->id(),
                            'status' => 'active'
                        ]
                    );
                }
            }

            DB::commit();
            flash()->success('Package documents updated successfully');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating package documents', [
                'user_id' => $this->user->id,
                'package_id' => $package->id,
                'error' => $e->getMessage()
            ]);
            flash()->error('Error updating documents: ' . $e->getMessage());
            return redirect()->back();
        }
    }
/**
 * Delete a document
 */
public function deletePartnerDocument(User $user, Package $package, $type)
{
    try {
        DB::beginTransaction();

        if ($package->user_id !== $user->id) {
            throw new \Exception('Package does not belong to this user');
        }

        $document = $package->documents()
            ->where('type', $type)
            ->first();

        if ($document) {
            if (Storage::exists($document->path)) {
                Storage::delete($document->path);
            }
            $document->delete();
        }

        DB::commit();
        flash()->success('Document deleted successfully');
        return redirect()->back();

    } catch (\Exception $e) {
        DB::rollBack();
        flash()->error('Error deleting document: ' . $e->getMessage());
        return redirect()->back();
    }
}

    public function saveBankDetails()
    {
        $this->validate([
            'bankDetail.name' => 'required|string',
            'bankDetail.sort_code' => 'required|string',
            'bankDetail.account' => 'required|string',
        ]);

        // Include user_id in the data array
        $bankDetailData = $this->bankDetail;
        $bankDetailData['user_id'] = $this->user->id;

        // Update or create the bank details directly in the BankDetail model
        $bankDetail = \App\Models\BankDetail::updateOrCreate(
            ['user_id' => $this->user->id],  // Condition to match the record
            $bankDetailData                  // Data to update or create
        );

        flash()->success('Bank Details is saved.');
    }

    public function saveAgreement()
    {
        $this->validate([
            'agreementDetail.agreement_type' => 'required|string',
            'agreementDetail.duration' => 'required|string',
            'agreementDetail.amount' => 'required|numeric',
            'agreementDetail.deposit' => 'required|numeric',
        ]);

        // Ensure the data array contains user_id for updating or creating
        $agreementDetailData = $this->agreementDetail;
        $agreementDetailData['user_id'] = $this->user->id;

        // Update or create agreement details directly in the AgreementDetail model
        $this->user->agreementDetail()->updateOrCreate(
            ['user_id' => $this->user->id],  // Condition to match the record
            $agreementDetailData              // Data to update or create
        );

        flash()->success(message: 'Agreement is saved.');
    }

    public function createPaymentLinkForMilestone($milestoneId)
    {
        try {
            $milestone = BookingPayment::findOrFail($milestoneId);

            // Prevent creating a link for already paid milestones
            if ($milestone->payment_status === 'paid') {
                session()->flash('error', 'This milestone has already been paid.');
                return null;
            }

            // Check for existing payment link
            $existingLink = PaymentLink::where('booking_payment_id', $milestoneId)
                ->where('status', 'pending')
                ->first();

            if ($existingLink) {
                // Update existing link
                $existingLink->update([
                    'amount' => $milestone->amount,
                    'updated_at' => now()
                ]);

                $paymentLink = $existingLink;
            } else {
                // Create new payment link if none exists
                $paymentLink = PaymentLink::create([
                    'unique_id' => Str::uuid(),
                    'user_id' => $this->user->id,
                    'booking_id' => $this->currentBookingId,
                    'booking_payment_id' => $milestoneId,
                    'amount' => $milestone->amount,
                    'status' => 'pending'
                ]);
            }

            // Refresh the milestones to update the view
            $this->generatePaymentLink($this->currentBookingId);

            session()->flash('message', $existingLink ?
                'Payment link updated successfully!' :
                'Payment link generated successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Error managing payment link: ' . $e->getMessage());
        }
    }

    public function generatePaymentLink($bookingId)
    {
        try {
            $this->currentBookingId = $bookingId;
            $booking = Booking::with('payments', 'bookingPayments')->findOrFail($bookingId);

            // Check if milestone payments exist, if not create them
            if ($booking->bookingPayments()->count() === 0) {
                $this->createInitialMilestonePayments($booking);
                $booking->load('bookingPayments');
            }

            // Get all milestones, including paid ones
            $milestones = $booking->bookingPayments()
                ->orderBy('due_date')
                ->get()
                ->map(function ($payment) {
                    // Get the most recent pending payment link
                    $existingPaymentLink = PaymentLink::where('booking_payment_id', $payment->id)
                        ->where('status', 'pending')
                        ->latest()
                        ->first();

                    return [
                        'id' => $payment->id,
                        'description' => $payment->is_booking_fee
                            ? 'Booking Fee'
                            : $this->getMilestoneDescription($payment),
                        'amount' => $payment->amount,
                        'due_date' => Carbon::parse($payment->due_date)->format('d M Y'),
                        'status' => $payment->payment_status,
                        'is_booking_fee' => $payment->is_booking_fee,
                        'payment_link' => $existingPaymentLink ? route('payment.page', $existingPaymentLink->unique_id) : null,
                        'last_updated' => $existingPaymentLink ? $existingPaymentLink->updated_at->format('d M Y H:i') : null
                    ];
                })->toArray();

            if (empty($milestones)) {
                session()->flash('error', 'No milestones found.');
                return null;
            }

            $this->milestoneOptions = $milestones;
            $this->showMilestoneSelectionModal = true;

        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
            return null;
        }
    }

    public function getMilestoneStatus($milestone)
    {
        // If milestone is already paid
        if ($milestone['status'] === 'paid') {
            return [
                'status' => 'paid',
                'badge_class' => 'success',
                'can_generate_link' => false,
                'message' => 'Paid'
            ];
        }

        // Check for existing payment
        $existingPayment = Payment::where('booking_payment_id', $milestone['id'])
            ->where('status', '!=', 'failed')
            ->first();

        if ($existingPayment) {
            return [
                'status' => 'processing',
                'badge_class' => 'info',
                'can_generate_link' => false,
                'message' => 'Payment Processing'
            ];
        }

        // Check for existing payment link
        $existingLink = PaymentLink::where('booking_payment_id', $milestone['id'])
            ->where('status', 'pending')
            ->first();

        if ($existingLink) {
            return [
                'status' => 'pending',
                'badge_class' => 'warning',
                'can_generate_link' => true,
                'has_link' => true,
                'link' => route('payment.page', $existingLink->unique_id),
                'message' => 'Payment Link Generated'
            ];
        }

        // Default status
        return [
            'status' => 'pending',
            'badge_class' => 'secondary',
            'can_generate_link' => true,
            'has_link' => false,
            'message' => 'Awaiting Payment'
        ];
    }

    private function getMilestoneDescription($payment)
    {
        // Format the due_date to 'Day AbbreviatedMonth Year' (e.g., '17 Dec 2024')
        $formattedDate = Carbon::parse($payment->due_date)->format('d M Y'); // d = day, M = abbreviated month name, Y = year

        return match ($payment->milestone_type) {
            'Month' => "Month {$formattedDate} Payment",
            'Week' => "Week {$formattedDate} Payment",
            'Day' => "Day {$formattedDate} Payment",
            'Booking Fee' => "Booking Fee {$formattedDate} Payment",
            default => "Payment {$formattedDate}",
        };
    }

    private function createInitialMilestonePayments($booking)
    {
        $startDate = Carbon::parse($booking->from_date);
        $priceType = $booking->price_type;

        // Calculate milestones based on price type
        $numberOfPayments = match ($priceType) {
            'Month' => Carbon::parse($booking->from_date)->diffInMonths(Carbon::parse($booking->to_date)),
            'Week' => ceil(Carbon::parse($booking->from_date)->diffInDays(Carbon::parse($booking->to_date)) / 7),
            'Day' => Carbon::parse($booking->from_date)->diffInDays(Carbon::parse($booking->to_date))
        };

        // Ensure at least one payment
        $numberOfPayments = max(1, $numberOfPayments);

        // Calculate amount per milestone
        $baseAmount = $booking->price / $numberOfPayments;

        // Create a separate booking fee milestone
        $booking->bookingPayments()->create([
            'milestone_type' => 'Booking',
            'milestone_number' => 0,
            'due_date' => $startDate,
            'amount' => $booking->booking_price,
            'payment_status' => 'pending',
            'is_booking_fee' => true
        ]);

        for ($i = 0; $i < $numberOfPayments; $i++) {
            $dueDate = match ($priceType) {
                'Month' => $startDate->copy()->addMonths($i),
                'Week' => $startDate->copy()->addWeeks($i),
                'Day' => $startDate->copy()->addDays($i)
            };

            $booking->bookingPayments()->create([
                'milestone_type' => $priceType,
                'milestone_number' => $i + 1,
                'due_date' => $dueDate,
                'amount' => $baseAmount,
                'payment_status' => 'pending',
                'is_booking_fee' => false
            ]);
        }
    }

    public function generateInvoice($bookingId)
    {
        $booking = Booking::with(['user', 'package', 'payments'])->findOrFail($bookingId);
        $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);

        $invoiceData = [
            'invoice_number' => $invoiceNumber,
            'date' => now()->format('Y-m-d'),
            'booking' => $booking,
            'payments' => $booking->payments,
            'total_paid' => $booking->payments->where('status', 'completed')->sum('amount'),
            'remaining' => $booking->total_amount - $booking->payments->where('status', 'completed')->sum('amount'),
            'next_payment_date' => $this->calculateNextPaymentDate($booking),
            'company' => [
                'name' => 'Rent and Rooms',
                'address' => '60 Sceptre Street, Newcastle, NE4 6PR',
                'phone' => '03301339494',
                'email' => 'rentandrooms@gmail.com'
            ]
        ];

        $pdf = app('dompdf.wrapper')->loadView('livewire.admin.invoice-template', $invoiceData);
        $pdfContent = $pdf->output();

        // Send email with invoice
        $this->sendInvoiceEmail($pdfContent, $invoiceData);

        return response()->streamDownload(
            fn() => print($pdfContent),
            $invoiceNumber . '.pdf'
        );
    }

    public function downloadInvoice($bookingId)
    {
        try {
            $booking = Booking::with(['user', 'package', 'payments'])->findOrFail($bookingId);

            $invoiceData = $this->prepareInvoiceData($booking);

            // Correctly instantiate DomPDF
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('livewire.admin.invoice-template', $invoiceData);

            return response()->streamDownload(
                fn() => print($pdf->output()),
                "invoice-{$booking->id}.pdf"
            );
        } catch (\Exception $e) {
            session()->flash('error', 'Error generating invoice: ' . $e->getMessage());
        }
    }
    public function emailInvoice($bookingId)
    {
        try {
            // Debug Step 1: Check booking data
            $booking = Booking::with(['user', 'package', 'payments'])->findOrFail($bookingId);
            // dd('Booking Data:', $booking->toArray());

            // Check if user has email
            if (!$booking->user->email) {
                session()->flash('error', 'Cannot send invoice: User has no email address.');
                return;
            }

            // Debug Step 2: Check invoice data
            $invoiceData = $this->prepareInvoiceData($booking);
            // dd('Invoice Data:', $invoiceData);

            // Debug Step 3: Check PDF generation
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('livewire.admin.invoice-template', $invoiceData);
            $pdfContent = $pdf->output();
            // dd('PDF Generated Successfully');

            // Debug Step 4: Check email setup
            $email = new SendGridMail();
            $email->setFrom("rentandrooms@gmail.com", "Rent and Rooms");
            $email->setSubject("Your Invoice from Rent and Rooms - Booking #{$booking->id}");
            $email->addTo($booking->user->email, $booking->user->name);

            // Debug Step 5: Check email content
            $emailContent = $this->getInvoiceEmailContent($booking);
            // dd('Email Content:', $emailContent);

            $email->addContent("text/html", $emailContent);

            // Debug Step 6: Check attachment
            $attachment = base64_encode($pdfContent);
            $email->addAttachment(
                $attachment,
                'application/pdf',
                "invoice-{$booking->id}.pdf",
                'attachment'
            );
            // dd('Email Prepared with Attachment');

            // Debug Step 7: Check SendGrid setup
            $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
            // dd('SendGrid API Key:', env('SENDGRID_API_KEY'));

            // Debug Step 8: Check response
            $response = $sendgrid->send($email);


            // Check response status code
            if ($response->statusCode() === 202 || $response->statusCode() === 200) {
                flash()->success('Invoice has been successfully emailed to ' . $booking->user->email);
            } else {
                flash()->error('Failed to send email. Status code: ' . $response->statusCode());
            }
        } catch (\Exception $e) {
            dd([
                'Error Message' => $e->getMessage(),
                'Error Code' => $e->getCode(),
                'Error File' => $e->getFile(),
                'Error Line' => $e->getLine(),
                'Stack Trace' => $e->getTraceAsString()
            ]);

            \Log::error('Invoice email error: ' . $e->getMessage(), [
                'booking_id' => $bookingId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Failed to send invoice email. Please try again later.');
        }
    }

    private function prepareInvoiceData($booking)
    {
        $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);

        return [
            'invoice_number' => $invoiceNumber,
            'date' => now()->format('d/m/Y'),
            'due_date' => now()->addDays(7)->format('d/m/Y'),
            'booking' => $booking,
            'customer' => [
                'name' => $booking->user->name,
                'email' => $booking->user->email,
                'phone' => $booking->user->phone ?? 'N/A',
            ],
            'company' => [
                'name' => 'Rent and Rooms',
                'address' => '60 Sceptre Street, Newcastle, NE4 6PR',
                'phone' => '03301339494',
                'email' => 'rentandrooms@gmail.com'
            ],
            'items' => [
                [
                    'description' => $booking->package->name . ' Package',
                    'amount' => $booking->price,
                    'type' => 'Package Price'
                ],
                [
                    'description' => 'Booking Fee',
                    'amount' => $booking->booking_price,
                    'type' => 'Booking Fee'
                ]
            ],
            'payments' => $booking->payments,
            'summary' => [
                'total_price' => $booking->price + $booking->booking_price,
                'total_paid' => $booking->payments->where('status', 'Paid')->sum('amount'),
                'remaining_balance' => ($booking->price + $booking->booking_price) - $booking->payments->where('status', 'Paid')->sum('amount')
            ]
        ];
    }

    private function getInvoiceEmailContent($booking)
    {
        try {
            return view('emails.invoice', [
                'booking' => $booking,
                'userName' => $booking->user->name,
                'invoiceNumber' => 'INV-' . date('Y') . '-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT),
                'totalAmount' => number_format($booking->price + $booking->booking_price, 2),
                'dueDate' => now()->addDays(7)->format('d/m/Y')
            ])->render();
        } catch (\Exception $e) {
            \Log::error('Error rendering invoice email template: ' . $e->getMessage());
            throw new \Exception('Failed to generate email content');
        }
    }


    public function updateUser()
    {
        $this->validate([
            'userData.name' => 'required|string|max:255',
            'userData.email' => 'required|email|max:255',
            'userData.phone' => 'required|string|max:15',
        ]);

        $this->user->update($this->userData);
        $this->isEditModalOpen = false;

        flash()->success('User information updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.user-view-component', [
            'user' => $this->user,
            'bookings' => $this->bookings,
        ]);
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\AgreementDetail;
use App\Models\BankDetail;
use App\Models\Package;
use App\Models\UserDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileComponent extends Component
{
    use WithFileUploads;

    public $bookings;
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
    public $editingDocumentId;
    public $editPersonName;
    public $editPassport;
    public $editNidOrOther;
    public $editPayslip;
    public $editStudentCard;
    public $isDetailEditing = false;
    public $showDeleteModal = false;



    public $agreementDetail = [
        'agreement_type' => '',
        'duration' => '',
        'amount' => '',
        'desposit' => '',
    ];

    public $userDetail = [
        'phone' => '',
        'occupied_address' => '',
        'package' => '',
        'booking_type' => '',
        'entry_date' => '',
        'package_price' => '',
        'security_amount' => '',
        'stay_status' => '',
        'package_id' => null,
    ];

    public $userDetailId;
    public $agreementDetailId;

    public $bankDetail = [
        'name' => '',
        'sort_code' => '',
        'account' => '',
    ];

    public $bankDetailId;
    public $packages = [];

    public $showEditModal = false;
    public $payments = [];

    protected $rules = [
        'documents.*.person_name' => 'nullable|string',
        'documents.*.passport' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
        'documents.*.nid_or_other' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
        'documents.*.payslip' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
        'documents.*.student_card' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->user = $user;
        $this->documents = [['person_name' => '', 'passport' => null, 'nid_or_other' => null, 'payslip' => null, 'student_card' => null]];
        $this->loadAgreementDetail();
        $this->loadUserDetail();
        $this->loadBankDetail();
        $this->refreshPackages();
        $this->loadBookings();
    }

    private function refreshPackages()
    {
        $user = Auth::user();
        $with = ['creator', 'assignedPartner', 'assignedBy', 'country', 'city', 'area', 'property'];

        if ($user->roles->pluck('name')->contains('Super Admin')) {
            $this->packages = Package::with($with)->get();
        } else {
            $this->packages = Package::with($with)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('assigned_to', $user->id);
                })
                ->get();
        }
    }


    public function toggleEdit()
    {
        $this->isDetailEditing = !$this->isDetailEditing;
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

    public function loadAgreementDetail()
    {
        $detail = $this->user->agreementDetail;
        if ($detail) {
            $this->agreementDetailId = $detail->id;
            $this->agreementDetail = [
                'agreement_type' => $detail->agreement_type,
                'duration' => $detail->duration,
                'amount' => $detail->amount,
                'deposit' => $detail->deposit,
            ];
        }
    }

    public function loadUserDetail()
    {
        $this->packages = Package::all(); // Load all packages

        $detail = $this->user->userDetail;
        if ($detail) {
            $this->userDetailId = $detail->id;
            $this->userDetail = [
                'phone' => $detail->phone,
                'occupied_address' => $detail->occupied_address,
                'package_id' => $detail->package_id, // Make sure package_id is loaded
                'booking_type' => $detail->booking_type, // Make sure package_id is loaded
                'entry_date' => $detail->entry_date,
                'package_price' => $detail->package_price,
                'security_amount' => $detail->security_amount,
                'stay_status' => $detail->stay_status ?? '', // Set default for stay_status
            ];
        }
    }


    public function loadBankDetail()
    {
        $detail = $this->user->bankDetail;
        if ($detail) {
            $this->bankDetailId = $detail->id;
            $this->bankDetail = [
                'name' => $detail->name,
                'sort_code' => $detail->sort_code,
                'account' => $detail->account,
            ];
        }
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
        $this->validate();

        // Check if the user instance is set correctly
        if ($this->user) {
            foreach ($this->documents as $document) {
                $passportPath = $document['passport'] ? $document['passport']->store('documents', 'public') : null;
                $nidPath = $document['nid_or_other'] ? $document['nid_or_other']->store('documents', 'public') : null;
                $payslipPath = $document['payslip'] ? $document['payslip']->store('documents', 'public') : null;
                $studentCardPath = $document['student_card'] ? $document['student_card']->store('documents', 'public') : null;
                UserDocument::create([
                    'user_id' => $this->user->id,  // Use the user ID from the instance
                    'person_name' => $document['person_name'],
                    'passport' => $passportPath,
                    'nid_or_other' => $nidPath,
                    'payslip' => $payslipPath,
                    'studentCardPath' => $studentCardPath,
                ]);
            }
            flash()->success('Document updated successfully.');

            return redirect()->back()->with('message', 'Document updated successfully.'); // Redirect to profile or relevant route
        } else {
            flash()->error('User not found. Please try again.');
        }
    }
    public function editDocument($documentId)
    {
        $document = UserDocument::findOrFail($documentId);
        $this->editingDocumentId = $documentId;
        $this->editPersonName = $document->person_name;
        $this->editPassport = null;  // Reset file inputs
        $this->editNidOrOther = null;
        $this->editPayslip = null;
        $this->editStudentCard = null;
        $this->showEditModal = true;  // Show the modal
    }

    public function updateDocuments(Request $request, Package $package)
    {
        $request->validate([
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        foreach ($request->file('documents') as $type => $file) {
            $path = $file->store('package-documents', 'public');

            $package->documents()->updateOrCreate(
                ['type' => $type],
                [
                    'path' => $path,
                    'expires_at' => Carbon::now()->addYear(), // Adjust expiry as needed
                ]
            );
        }

        return back()->with('success', 'Documents updated successfully');
    }

    public function updateDocument()
    {
        $this->validate([
            'editPersonName' => 'nullable|string|max:255',
            'editPassport' => 'nullable|mimes:pdf,jpg,jpeg,png|max:1024',  // Example max size 1MB
            'editNidOrOther' => 'nullable|mimes:pdf,jpg,jpeg,png|max:1024',
            'editPayslip' => 'nullable|mimes:pdf,jpg,jpeg,png|max:1024',  // Validation for payslip
            'editStudentCard' => 'nullable|mimes:pdf,jpg,jpeg,png|max:1024',  // Validation for student card
        ]);

        $document = UserDocument::findOrFail($this->editingDocumentId);
        $document->person_name = $this->editPersonName;

        // Handle passport file upload
        if ($this->editPassport) {
            $document->passport = $this->editPassport->store('documents', 'public');
        }

        // Handle NID or other file upload
        if ($this->editNidOrOther) {
            $document->nid_or_other = $this->editNidOrOther->store('documents', 'public');
        }

        // Handle payslip file upload
        if ($this->editPayslip) {
            $document->payslip = $this->editPayslip->store('documents', 'public');
        }

        // Handle student card file upload
        if ($this->editStudentCard) {
            $document->student_card = $this->editStudentCard->store('documents', 'public');
        }

        $document->save();

        // Reset the form and state
        $this->resetEditForm();
        $this->showEditModal = false;  // Hide the modal
        $this->user->load('documents');  // Reload user's documents
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;  // Hide the modal
    }
    private function resetEditForm()
    {
        $this->editPersonName = '';
        $this->editPassport = null;
        $this->editNidOrOther = null;
        $this->editingDocumentId = null;
    }
    // Method to delete a document
    public function deleteDocument($documentId)
    {
        $document = UserDocument::findOrFail($documentId);
        $document->delete();

        // Reload user's documents
        $this->user->load('documents');
    }

    public function saveAgreement()
    {

        AgreementDetail::updateOrCreate(
            ['user_id' => $this->user->id],
            [
                'agreement_type' => $this->agreementDetail['agreement_type'],
                'duration' => $this->agreementDetail['duration'],
                'amount' => $this->agreementDetail['amount'],
                'deposit' => $this->agreementDetail['deposit'],
            ]
        );

        flash()->success('Agreement details saved');
        return redirect()->back();
    }
    public function saveBankDetails()
    {

        BankDetail::updateOrCreate(
            ['user_id' => $this->user->id],
            [
                'name' => $this->bankDetail['name'],
                'sort_code' => $this->bankDetail['sort_code'],
                'account' => $this->bankDetail['account'],
            ]
        );

        flash()->success('Bank details saved');
        $this->loadBankDetail();
    }
    public function deleteAgreementDetail()
    {
        if ($this->agreementDetailId) {
            AgreementDetail::find($this->agreementDetailId)->delete();
            $this->agreementDetail = ['agreement_type' => '', 'duration' => '', 'amount' => ''];
            $this->agreementDetailId = null;
        }
    }

    public function saveUserDetail()
    {

        $userDetailData = [
            'stay_status' => $this->userDetail['stay_status'],
            'package_id' => $this->userDetail['stay_status'] === 'staying' ? $this->userDetail['package_id'] : null,
            'booking_type' => $this->userDetail['booking_type'],
            'phone' => $this->userDetail['phone'],
            'occupied_address' => $this->userDetail['occupied_address'],
            'entry_date' => $this->userDetail['entry_date'],
            'package_price' => $this->userDetail['package_price'],
            'security_amount' => $this->userDetail['security_amount'],
        ];

        // Save or update the user details
        $this->user->userDetail()->updateOrCreate(
            ['user_id' => $this->user->id],
            $userDetailData
        );

        // Reset form state and show success message
        $this->isDetailEditing = false;
        flash()->success('User details saved.');

        return redirect()->back();
    }



    public function update(Request $request)
    {
        $request->validate([
            'proof_type_1' => 'nullable|string|max:50',
            'proof_type_2' => 'nullable|string|max:50',
            'proof_type_3' => 'nullable|string|max:50',
            'proof_type_4' => 'nullable|string|max:50',
            'proof_path_1' => 'nullable|file|mimes:pdf,jpeg,png,jpg',
            'proof_path_2' => 'nullable|file|mimes:pdf,jpeg,png,jpg',
            'proof_path_3' => 'nullable|file|mimes:pdf,jpeg,png,jpg',
            'proof_path_4' => 'nullable|file|mimes:pdf,jpeg,png,jpg',
        ]);

        $user = Auth::user();

        if ($request->hasFile('proof_path_1')) {
            $user->proof_path_1 = $request->file('proof_path_1')->store('documents', 'public');
        }
        if ($request->hasFile('proof_path_2')) {
            $user->proof_path_2 = $request->file('proof_path_2')->store('documents', 'public');
        }
        if ($request->hasFile('proof_path_3')) {
            $user->proof_path_3 = $request->file('proof_path_3')->store('documents', 'public');
        }
        if ($request->hasFile('proof_path_4')) {
            $user->proof_path_4 = $request->file('proof_path_4')->store('documents', 'public');
        }

        $user->proof_type_1 = $request->proof_type_1;
        $user->proof_type_2 = $request->proof_type_2;
        $user->proof_type_3 = $request->proof_type_3;
        $user->proof_type_4 = $request->proof_type_4;

        $user->save();

        flash()->success('Id proof updated successfully');
        return redirect()->back()->with('message', 'Profile updated successfully.');
    }
    public function removePackage()
    {
        // Clear the package_id and reset the related fields
        $this->userDetail['package_id'] = null;
        $this->userDetail['package_price'] = null;
        $this->userDetail['security_amount'] = null;

        // Update the user detail in the database
        $this->user->userDetail()->updateOrCreate(
            ['user_id' => $this->user->id],
            $this->userDetail
        );

        // Set a success message
        flash()->success('Package removed successfully.');
    }


    public function render()
    {
        return view('livewire.admin.profile-component', [
            'user' => Auth::user(),
        ]);
    }
}

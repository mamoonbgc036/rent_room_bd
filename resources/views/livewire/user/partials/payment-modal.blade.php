<div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Method</h5>
                <button type="button" class="close" wire:click="$set('showPaymentModal', false)">
                    <span>&times;</span>
                </button>
            </div>
            <form wire:submit.prevent="proceedPayment">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Payment Method</label>
                        <select class="form-control" wire:model.live="paymentMethod">
                            <option value="cash">Cash Payment</option>
                            <option value="card">Card Payment (Stripe)</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>

                    @if($paymentMethod === 'bank_transfer')
                        <div class="alert alert-info">
                            <p class="mb-2"><strong>Bank Details:</strong></p>
                            <p class="mb-3">{{ $bankDetails }}</p>
                            <div class="form-group mb-0">
                                <label>Transfer Reference</label>
                                <input type="text" class="form-control" wire:model="bankTransferReference" required>
                                @error('bankTransferReference')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endif

                    @if($paymentMethod === 'cash')
                        <div class="alert alert-info mb-0">
                            Please bring exact cash amount to the venue.
                        </div>
                    @endif

                    <div class="alert alert-primary mt-3">
                        <strong>Amount to Pay: </strong>
                        ৳{{ number_format($this->calculateTotalAmount(), 2) }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showPaymentModal', false)">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

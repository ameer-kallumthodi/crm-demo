<form action="{{ route('admin.invoices.update-amount', $invoice->id) }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-12">
            <div class="p-2 border rounded bg-light">
                <p class="mb-1"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                <p class="mb-1"><strong>Student:</strong> {{ $invoice->student->name }}</p>
                <p class="mb-0"><strong>Current Total:</strong> ₹ {{ number_format($invoice->total_amount, 2) }}</p>
                <p class="mb-0"><strong>Paid Amount:</strong> ₹ {{ number_format($invoice->paid_amount, 2) }}</p>
                <p class="mb-0"><strong>Pending Amount:</strong> ₹ {{ number_format($invoice->pending_amount, 2) }}</p>
            </div>
        </div>

        <div class="col-12">
            <label for="total_amount" class="form-label">Invoice Amount <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">₹</span>
                <input type="number" name="total_amount" id="total_amount" class="form-control" step="0.01" min="0" value="{{ old('total_amount', $invoice->total_amount) }}" required>
            </div>
            <small class="text-muted">Paid amount will remain ₹ {{ number_format($invoice->paid_amount, 2) }}. Status will auto-adjust.</small>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100">Update Amount</button>
        </div>
    </div>
</form>


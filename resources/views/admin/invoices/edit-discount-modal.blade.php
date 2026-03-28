<form action="{{ route('admin.invoices.update-discount', $invoice->id) }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-12">
            <div class="p-2 border rounded bg-light">
                <p class="mb-1"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                <p class="mb-1"><strong>Student:</strong> {{ $invoice->student->name }}</p>
                <p class="mb-1"><strong>Gross total:</strong> ₹ {{ number_format(round($invoice->total_amount)) }}</p>
                <p class="mb-1"><strong>Paid:</strong> ₹ {{ number_format(round($invoice->paid_amount)) }}</p>
                <p class="mb-0"><strong>Max discount allowed:</strong> ₹ {{ number_format(round(max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount)), 2) }}</p>
            </div>
        </div>

        <div class="col-12">
            <label for="discount_amount" class="form-label">Discount amount <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">₹</span>
                <input type="number" name="discount_amount" id="discount_amount" class="form-control" step="0.01" min="0"
                    max="{{ max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount) }}"
                    value="{{ old('discount_amount', $invoice->discount_amount ?? 0) }}" required>
            </div>
            <small class="text-muted">Net payable becomes gross total minus this discount. Tax invoices and receipts show amounts after discount without a separate discount line.</small>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100">Save discount</button>
        </div>
    </div>
</form>

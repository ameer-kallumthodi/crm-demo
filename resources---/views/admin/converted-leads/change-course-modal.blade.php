@php
    $formatCurrency = function ($amount) {
        return '₹' . number_format((float) $amount, 2);
    };
@endphp

<form id="changeCourseForm" method="POST" action="{{ route('admin.converted-leads.change-course', $convertedLead->id) }}">
    @csrf
    <div class="row g-3">
        <div class="col-12">
            <div class="card mb-0 border shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-3">Student Summary</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-1">
                                <span><strong>Name:</strong> {{ $convertedLead->name }}</span>
                                <span><strong>Phone:</strong> {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span>
                                <span><strong>Email:</strong> {{ $convertedLead->email ?? 'N/A' }}</span>
                                <span><strong>Register Number:</strong> {{ $convertedLead->register_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-1">
                                <span><strong>Current Course:</strong> {{ $convertedLead->course?->title ?? 'Not Assigned' }}</span>
                                <span><strong>Current Batch:</strong> {{ $convertedLead->batch?->title ?? 'Not Assigned' }}</span>
                                <span><strong>Admission Date:</strong> {{ optional($convertedLead->created_at)->format('d-m-Y') }}</span>
                                <span><strong>Lead ID:</strong> {{ $convertedLead->lead_id }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($currentInvoice)
        <div class="col-12">
            <div class="alert alert-info mb-0">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-2">
                    <div>
                        <strong>Existing Invoice:</strong> {{ $currentInvoice->invoice_number }}
                    </div>
                    <div class="d-flex flex-wrap gap-3">
                        <span>Total: <strong>{{ $formatCurrency($currentInvoice->total_amount) }}</strong></span>
                        <span>Paid: <strong>{{ $formatCurrency($currentInvoice->paid_amount) }}</strong></span>
                        <span>Pending: <strong>{{ $formatCurrency($currentInvoice->total_amount - $currentInvoice->paid_amount) }}</strong></span>
                        <span>Payments: <strong>{{ $currentInvoice->payments->count() }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="col-md-6">
            <label for="change_course_id" class="form-label">New Course <span class="text-danger">*</span></label>
            <select class="form-select" id="change_course_id" name="course_id" required>
                <option value="">Select Course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ $course->id == $convertedLead->course_id ? 'selected' : '' }}>
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" data-error-for="course_id"></div>
        </div>

        <div class="col-md-6">
            <label for="change_batch_id" class="form-label">New Batch <span class="text-danger">*</span></label>
            <select class="form-select" id="change_batch_id" name="batch_id" required data-selected-batch="{{ $convertedLead->batch_id ?? '' }}">
                <option value="">Select a course first</option>
            </select>
            <div class="invalid-feedback" data-error-for="batch_id"></div>
        </div>

        <div class="col-md-6">
            <label for="change_remark" class="form-label">Remark</label>
            <textarea class="form-control" id="change_remark" name="remark" rows="2" placeholder="Add internal remark (optional)"></textarea>
            <div class="invalid-feedback" data-error-for="remark"></div>
        </div>

        <div class="col-md-6">
            <label for="change_description" class="form-label">Activity Description</label>
            <textarea class="form-control" id="change_description" name="description" rows="2" placeholder="Activity notes (optional)"></textarea>
            <div class="invalid-feedback" data-error-for="description"></div>
        </div>

        <div class="col-12">
            <div class="border rounded p-3 bg-light">
                <h6 class="mb-3 text-primary">New Course Pricing Summary</h6>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Course Amount</small>
                        <strong id="courseAmountDisplay">{{ $currentPricing ? $formatCurrency($currentPricing['course_amount']) : '₹0.00' }}</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Batch Amount</small>
                        <strong id="batchAmountDisplay">{{ $currentPricing ? $formatCurrency($currentPricing['batch_amount']) : '₹0.00' }}</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Additional</small>
                        <strong id="additionalAmountDisplay">{{ $currentPricing ? $formatCurrency(($currentPricing['extra_amount'] ?? 0) + ($currentPricing['university_amount'] ?? 0)) : '₹0.00' }}</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Total Payable</small>
                        <strong id="totalAmountDisplay">{{ $currentPricing ? $formatCurrency($currentPricing['total_amount']) : '₹0.00' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 text-end">
            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="changeCourseSubmitBtn">
                <span class="btn-text">Update Course</span>
                <span class="btn-loading d-none"><i class="ti ti-loader-2 spin me-1"></i>Processing...</span>
            </button>
        </div>
    </div>
</form>

<script>
    (function ($) {
        const form = $('#changeCourseForm');
        const courseSelect = $('#change_course_id');
        const batchSelect = $('#change_batch_id');
        const submitBtn = $('#changeCourseSubmitBtn');
        const btnText = submitBtn.find('.btn-text');
        const btnLoading = submitBtn.find('.btn-loading');
        const pricingUrl = '{{ route('admin.converted-leads.course-pricing', $convertedLead->id) }}';
        const batchesEndpoint = '/api/batches/by-course/';
        const selectedBatchId = batchSelect.data('selected-batch') ? String(batchSelect.data('selected-batch')) : '';

        const amountDisplays = {
            course: $('#courseAmountDisplay'),
            batch: $('#batchAmountDisplay'),
            additional: $('#additionalAmountDisplay'),
            total: $('#totalAmountDisplay'),
        };

        function setButtonLoading(isLoading) {
            submitBtn.prop('disabled', isLoading);
            btnText.toggleClass('d-none', isLoading);
            btnLoading.toggleClass('d-none', !isLoading);
        }

        function clearErrors() {
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
        }

        function setFieldError(field, message) {
            const input = form.find(`[name="${field}"]`);
            const feedback = form.find(`.invalid-feedback[data-error-for="${field}"]`);
            if (input.length) {
                input.addClass('is-invalid');
            }
            if (feedback.length) {
                feedback.text(message);
            }
        }

        function updatePricingDisplays(data) {
            if (!data) return;
            amountDisplays.course.text(data.formatted_course ?? formatCurrency(data.course_amount ?? 0));
            amountDisplays.batch.text(data.formatted_batch ?? formatCurrency(data.batch_amount ?? 0));
            const additional = (parseFloat(data.extra_amount ?? 0) || 0) + (parseFloat(data.university_amount ?? 0) || 0);
            amountDisplays.additional.text(formatCurrency(additional));
            amountDisplays.total.text(data.formatted_total ?? formatCurrency(data.total_amount ?? 0));
        }

        function formatCurrency(amount) {
            const value = parseFloat(amount) || 0;
            return '₹' + value.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function loadBatches(courseId, selectedId = '') {
            if (!courseId) {
                batchSelect.html('<option value="">Select a course first</option>');
                return;
            }

            batchSelect.html('<option value="">Loading...</option>');
            $.get(`${batchesEndpoint}${courseId}`)
                .done(function (response) {
                    const batches = (response && response.batches) ? response.batches : [];
                    if (!batches.length) {
                        batchSelect.html('<option value="">No batches found for this course</option>');
                        return;
                    }

                    const options = batches.map(batch => {
                        const selected = selectedId && String(selectedId) === String(batch.id) ? 'selected' : '';
                        return `<option value="${batch.id}" ${selected}>${batch.title}</option>`;
                    });
                    batchSelect.html('<option value="">Select Batch</option>' + options.join(''));
                })
                .fail(function () {
                    batchSelect.html('<option value="">Failed to load batches</option>');
                });
        }

        function fetchPricing() {
            const courseId = courseSelect.val();
            if (!courseId) {
                updatePricingDisplays({ course_amount: 0, batch_amount: 0, extra_amount: 0, university_amount: 0, total_amount: 0 });
                return;
            }

            const params = {
                course_id: courseId,
            };
            const batchId = batchSelect.val();
            if (batchId) {
                params.batch_id = batchId;
            }

            $.get(pricingUrl, params)
                .done(function (response) {
                    if (response && response.success && response.data) {
                        updatePricingDisplays(response.data);
                    }
                })
                .fail(function () {
                    updatePricingDisplays({ course_amount: 0, batch_amount: 0, extra_amount: 0, university_amount: 0, total_amount: 0 });
                });
        }

        form.on('submit', function (event) {
            event.preventDefault();
            clearErrors();

            const formData = new FormData(form[0]);
            setButtonLoading(true);

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response && response.success) {
                        toast_success(response.message || 'Course updated successfully.');
                        setTimeout(function () {
                            location.reload();
                        }, 1200);
                    } else {
                        toast_error(response.message || 'Failed to update course.');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function (field) {
                            setFieldError(field, errors[field][0]);
                        });
                        toast_error(xhr.responseJSON.message || 'Please check the highlighted fields.');
                    } else if (xhr.status === 403) {
                        toast_error('You do not have permission to change the course.');
                    } else {
                        toast_error('Failed to update course. Please try again.');
                    }
                },
                complete: function () {
                    setButtonLoading(false);
                }
            });
        });

        courseSelect.on('change', function () {
            const courseId = $(this).val();
            loadBatches(courseId);
            fetchPricing();
        });

        batchSelect.on('change', function () {
            fetchPricing();
        });

        // Initial load
        if (courseSelect.val()) {
            loadBatches(courseSelect.val(), selectedBatchId);
            fetchPricing();
        }
    })(jQuery);
</script>


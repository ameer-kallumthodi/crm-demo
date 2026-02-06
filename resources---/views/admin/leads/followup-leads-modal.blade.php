<div class="modal-body">
    <div class="alert alert-info d-flex align-items-center mb-3">
        <i class="ti ti-calendar-event me-2"></i>
        <div>
            <strong>Followup Leads</strong>
            <div class="small text-muted">Filter leads that require followup and review their latest notes.</div>
        </div>
    </div>

    <form id="followupLeadsFilterForm" action="{{ route('admin.leads.followup') }}" method="GET" class="mb-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="followup_lead_date" class="form-label">Followup Date</label>
                <input type="date" id="followup_lead_date" name="followup_date" class="form-control"
                    value="{{ $filters['followup_date'] ?? date('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label for="followup_lead_telecaller" class="form-label">Telecaller</label>
                <select id="followup_lead_telecaller" name="telecaller_id" class="form-select">
                    <option value="">All Telecallers</option>
                    @foreach($telecallers as $telecaller)
                        <option value="{{ $telecaller->id }}" {{ ($filters['telecaller_id'] ?? '') == $telecaller->id ? 'selected' : '' }}>
                            {{ $telecaller->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="followup_lead_source" class="form-label">Lead Source</label>
                <select id="followup_lead_source" name="lead_source_id" class="form-select">
                    <option value="">All Sources</option>
                    @foreach($leadSources as $source)
                        <option value="{{ $source->id }}" {{ ($filters['lead_source_id'] ?? '') == $source->id ? 'selected' : '' }}>
                            {{ $source->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="ti ti-filter me-1"></i> Apply Filters
            </button>
            <button type="button" id="followupLeadsFilterReset" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-refresh me-1"></i> Reset
            </button>
        </div>
    </form>

    <div id="followupLeadsResults">
        @include('admin.leads.partials.followup-leads-table', [
            'leads' => $leads,
            'followupStatusIds' => $followupStatusIds,
            'filtersApplied' => $filtersApplied,
        ])
    </div>
</div>

<script>
    (function() {
        const form = $('#followupLeadsFilterForm');
        const resultsContainer = $('#followupLeadsResults');
        const resetBtn = $('#followupLeadsFilterReset');

        function renderLoading() {
            return '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Fetching followup leads...</p></div>';
        }

        function loadFollowupLeads() {
            resultsContainer.html(renderLoading());

            const serialized = form.serialize();
            const requestData = serialized ? serialized + '&refresh=1' : 'refresh=1';

            $.ajax({
                url: form.attr('action'),
                method: 'GET',
                data: requestData,
                success: function(response) {
                    if (response.success) {
                        resultsContainer.html(response.html);
                        if (typeof showToast === 'function') {
                            showToast('Loaded ' + response.count + ' followup lead' + (response.count === 1 ? '' : 's') + '.', 'success');
                        }
                    } else {
                        resultsContainer.html('<div class="alert alert-warning">Unable to load followup leads. Please try again.</div>');
                    }
                },
                error: function() {
                    resultsContainer.html('<div class="alert alert-danger">An error occurred while loading followup leads.</div>');
                }
            });
        }

        form.on('submit', function(event) {
            event.preventDefault();
            loadFollowupLeads();
        });

        form.find('select').on('change', function() {
            form.trigger('submit');
        });

        resetBtn.on('click', function() {
            form[0].reset();
            loadFollowupLeads();
        });
    })();
</script>


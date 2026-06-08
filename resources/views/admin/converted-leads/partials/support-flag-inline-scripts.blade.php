@once
@push('styles')
<style>
    td .support-flag-field {
        position: relative;
        min-width: 100px;
        overflow: visible;
    }

    td .support-flag-field.editing > .edit-btn {
        display: none !important;
    }

    .support-flag-field.inline-edit .flag-edit-form {
        display: none !important;
        position: absolute;
        top: 100%;
        left: 0 !important;
        margin-top: 6px;
        z-index: 1060;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 10px;
        min-width: 320px;
        max-width: 420px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        overflow: visible;
    }

    .support-flag-field.inline-edit.editing .flag-edit-form {
        display: block !important;
    }

    .support-flag-field .flag-edit-form .flag-edit-row {
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .support-flag-field .flag-edit-form .flag-select-wrap {
        flex: 1;
        min-width: 0;
    }

    .support-flag-field .flag-edit-form .select2-container {
        width: 100% !important;
        display: block;
    }

    .support-flag-field .flag-edit-form .select2-selection--single {
        height: 36px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    .support-flag-field .flag-edit-form .select2-selection__rendered {
        line-height: 34px;
        padding-left: 8px;
        padding-right: 28px;
    }

    .support-flag-field .flag-edit-form .select2-selection__arrow {
        height: 34px;
    }

    .support-flag-field .flag-edit-form .flag-edit-actions {
        display: flex;
        flex-direction: row;
        flex-shrink: 0;
        align-items: center;
        gap: 6px;
        padding-top: 2px;
    }

    .support-flag-field .flag-edit-form .flag-edit-actions .btn {
        padding: 5px 12px;
        font-size: 12px;
        white-space: nowrap;
    }

    .support-flag-field .flag-edit-form .select2-dropdown {
        border-radius: 4px;
    }

    .support-flag-option .support-flag-option-title {
        font-weight: 600;
        font-size: 13px;
        line-height: 1.3;
        color: #212529;
    }

    .support-flag-option .support-flag-option-desc {
        font-size: 12px;
        line-height: 1.4;
        color: #6c757d;
        white-space: normal;
        word-break: break-word;
        margin-top: 3px;
    }

    .select2-container--default .select2-results__option--highlighted .support-flag-option .support-flag-option-title,
    .select2-container--default .select2-results__option--highlighted .support-flag-option .support-flag-option-desc {
        color: #fff !important;
    }

    .select2-container--default .select2-results__option {
        padding: 8px 10px;
    }

    .support-flag-field .flag-edit-form .select2-container--open .select2-dropdown {
        width: 100% !important;
        left: 0 !important;
        min-width: 0;
    }

    .support-flag-field .flag-edit-form .select2-container--open .select2-dropdown--below {
        margin-top: 4px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }
</style>
@endpush
@push('scripts')
<script>
(function() {
    const supportUpdateUrlTemplate = @json($supportUpdateUrl ?? '');

    function getSupportFlagUpdateUrl(id) {
        if (supportUpdateUrlTemplate) {
            return supportUpdateUrlTemplate.replace('__ID__', id);
        }

        return `/admin/support-converted-leads/${id}/update-support-details`;
    }

    function escapeHtml(text) {
        return $('<div>').text(text || '').html();
    }

    function destroyFlagSelect2($select) {
        if ($select.length && $select.hasClass('select2-hidden-accessible') && $.fn.select2) {
            $select.select2('destroy');
        }
    }

    function formatFlagOption(state) {
        if (!state.id) {
            return state.text;
        }

        const $el = $(state.element);
        const title = $el.data('title') || state.text;
        const description = $el.data('description') || '';
        const color = $el.data('color') || '#ccc';

        return $(
            '<div class="support-flag-option">'
            + '<div class="d-flex align-items-start gap-2">'
            + `<span class="rounded border flex-shrink-0" style="width:14px;height:14px;background-color:${escapeHtml(color)};margin-top:3px;"></span>`
            + '<div class="flex-grow-1 min-w-0">'
            + `<div class="support-flag-option-title">${escapeHtml(title)}</div>`
            + (description ? `<div class="support-flag-option-desc">${escapeHtml(description)}</div>` : '')
            + '</div></div></div>'
        );
    }

    function formatFlagSelection(state) {
        if (!state.id) {
            return state.text;
        }

        const $el = $(state.element);
        const title = $el.data('title') || state.text;
        const color = $el.data('color') || '#ccc';

        return $(
            '<span class="d-inline-flex align-items-center gap-2">'
            + `<span class="rounded border flex-shrink-0" style="width:14px;height:14px;background-color:${escapeHtml(color)};"></span>`
            + `<span class="fw-medium">${escapeHtml(title)}</span>`
            + '</span>'
        );
    }

    function getFlagDropdownParent() {
        const $tableWrap = $('.table-responsive:visible').first();
        return $tableWrap.length ? $tableWrap : $(document.body);
    }

    function initFlagSelect2($select) {
        if (!$.fn.select2) {
            return;
        }

        const $form = $select.closest('.flag-edit-form');
        const dropdownParent = $form.length ? $form : getFlagDropdownParent();

        $select.select2({
            dropdownParent: dropdownParent,
            width: '100%',
            placeholder: 'Select Support Flag',
            allowClear: true,
            templateResult: formatFlagOption,
            templateSelection: formatFlagSelection,
            escapeMarkup: function(markup) {
                return markup;
            }
        });
    }

    function createFlagEditForm() {
        return `
            <div class="edit-form flag-edit-form">
                <div class="flag-edit-row">
                    <div class="flag-select-wrap">
                        <select class="form-select form-select-sm flag-select-edit">
                            <option value="">Loading support flags...</option>
                        </select>
                    </div>
                    <div class="flag-edit-actions">
                        <button type="button" class="btn btn-success btn-sm support-flag-save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm support-flag-cancel-edit">Cancel</button>
                    </div>
                </div>
            </div>
        `;
    }

    function loadFlagsIntoSelect($select, currentId) {
        $.get('/api/support-flags').done(function(flags) {
            let options = '<option value=""></option>';
            flags.forEach(function(flag) {
                const selected = currentId && String(currentId) === String(flag.id) ? 'selected' : '';
                const title = escapeHtml(flag.title);
                const description = escapeHtml(flag.description);
                const color = escapeHtml(flag.color);
                options += `<option value="${flag.id}" data-color="${color}" data-title="${title}" data-description="${description}" ${selected}>${title}</option>`;
            });
            $select.html(options);
            destroyFlagSelect2($select);
            initFlagSelect2($select);
            if (currentId) {
                $select.val(String(currentId)).trigger('change');
            }
        }).fail(function() {
            $select.html('<option value="">Error loading support flags</option>');
            destroyFlagSelect2($select);
            initFlagSelect2($select);
        });
    }

    function closeFlagEdit(container) {
        destroyFlagSelect2(container.find('.flag-select-edit'));
        container.removeClass('editing');
        container.find('.edit-form').remove();
    }

    $(document).on('click', '.support-flag-field .edit-btn', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const container = $(this).closest('.support-flag-field');
        if (container.hasClass('editing')) {
            return;
        }

        $('.support-flag-field.editing').not(container).each(function() {
            closeFlagEdit($(this));
        });

        const currentId = container.data('current-id') !== undefined ? String(container.data('current-id')).trim() : '';
        container.addClass('editing');
        container.append(createFlagEditForm());
        loadFlagsIntoSelect(container.find('.flag-select-edit'), currentId);
    });

    $(document).on('click', '.support-flag-cancel-edit', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        closeFlagEdit($(this).closest('.support-flag-field'));
    });

    $(document).on('click', '.support-flag-save-edit', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const container = $(this).closest('.support-flag-field');
        const id = container.data('id');
        const value = container.find('.flag-select-edit').val();
        const btn = $(this);

        if (btn.data('busy')) return;
        btn.data('busy', true).prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');

        $.ajax({
            url: getSupportFlagUpdateUrl(id),
            method: 'POST',
            data: {
                field: 'support_flag_id',
                value: value,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    if (response.display_html) {
                        container.find('.display-value').html(response.display_html);
                    } else {
                        container.find('.display-value').text(response.value || 'N/A');
                    }
                    container.data('current-id', value || '');
                    if (typeof toast_success === 'function') {
                        toast_success(response.message || 'Updated successfully');
                    }
                } else if (typeof toast_error === 'function') {
                    toast_error(response.error || 'Update failed');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Update failed';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                if (typeof toast_error === 'function') {
                    toast_error(errorMessage);
                }
            },
            complete: function() {
                btn.data('busy', false).prop('disabled', false).html('Save');
                closeFlagEdit(container);
            }
        });
    });
})();
</script>
@endpush
@endonce

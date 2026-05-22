@include('admin.partials.tinymce-init')

@once
@push('scripts')
<script>
(function() {
    const SUPPORT_BOSSE_MAIL_BASE = @json(url('/admin/support-bosse-converted-leads'));
    let courseMailFetchUrl = null;
    let courseMailSendUrl = null;
    const contentSelector = '#support_course_mail_content';

    function resolveCourseMailButton(target) {
        return $(target).closest('.js-send-support-course-mail');
    }

    function buildCourseMailUrls(leadId) {
        const id = String(leadId || '').trim();
        if (!id) {
            return null;
        }
        const base = String(SUPPORT_BOSSE_MAIL_BASE || '').replace(/\/$/, '');
        return {
            load: base + '/' + id + '/course-mail',
            send: base + '/' + id + '/send-course-mail',
        };
    }

    function setCourseMailState(state) {
        $('#supportCourseMailLoading').toggleClass('d-none', state !== 'loading');
        $('#supportCourseMailError').addClass('d-none');
        $('#supportCourseMailForm').toggleClass('d-none', state !== 'ready');
        $('#confirmSupportCourseMailBtn').toggleClass('d-none', state !== 'ready');
    }

    function showCourseMailError(message) {
        setCourseMailState('error');
        $('#supportCourseMailLoading').addClass('d-none');
        $('#supportCourseMailForm').addClass('d-none');
        $('#confirmSupportCourseMailBtn').addClass('d-none');
        $('#supportCourseMailError').removeClass('d-none').text(message);
    }

    function resetCourseMailModal() {
        courseMailFetchUrl = null;
        courseMailSendUrl = null;
        if (typeof destroyCrmTinyMCE === 'function') {
            destroyCrmTinyMCE(contentSelector);
        }
        $('#supportCourseMailForm')[0].reset();
        $('#supportCourseMailError').addClass('d-none').text('');
        setCourseMailState('idle');
    }

    $(document).on('click', '.js-send-support-course-mail', function(e) {
        e.preventDefault();

        const btn = resolveCourseMailButton(this);
        if (!btn.length || btn.prop('disabled')) {
            return;
        }

        const leadId = btn.attr('data-lead-id');
        const urls = buildCourseMailUrls(leadId);
        const studentName = btn.attr('data-name') || 'Student';

        $('#supportCourseMailModalLabel').text('Send Mail — ' + studentName);
        resetCourseMailModal();
        $('#supportCourseMailModal').modal('show');

        if (!urls) {
            showCourseMailError('Could not determine student ID for this mail action.');
            return;
        }

        courseMailFetchUrl = urls.load;
        courseMailSendUrl = urls.send;

        setCourseMailState('loading');

        $.ajax({
            url: courseMailFetchUrl,
            method: 'GET',
            dataType: 'json',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .done(function(res) {
                if (!res || typeof res !== 'object' || !res.success) {
                    showCourseMailError((res && res.error) ? res.error : 'Could not load mail template.');
                    return;
                }

                $('#supportCourseMailRecipient').text(res.recipient_email || '—');
                $('#supportCourseMailContext').text(res.context || '');
                $('#support_course_mail_subject').val(res.subject || '');
                $('#support_course_mail_content').val(res.content || '');

                setCourseMailState('ready');

                if (typeof initCrmTinyMCE === 'function') {
                    initCrmTinyMCE(contentSelector, { height: 320 });
                }
            })
            .fail(function(xhr) {
                let msg = 'Could not load mail template.';
                if (xhr.responseJSON) {
                    msg = xhr.responseJSON.error || xhr.responseJSON.message || msg;
                } else if (xhr.responseText) {
                    try {
                        const parsed = JSON.parse(xhr.responseText);
                        msg = parsed.error || parsed.message || msg;
                    } catch (err) {
                        if (xhr.status === 404) {
                            msg = 'Mail template endpoint not found. Run: php artisan route:clear';
                        } else if (xhr.status === 403 || xhr.status === 401) {
                            msg = 'Session expired or access denied. Please refresh and log in again.';
                        }
                    }
                }
                showCourseMailError(msg);
            });
    });

    $('#confirmSupportCourseMailBtn').on('click', function() {
        if (!courseMailSendUrl) {
            return;
        }

        if (typeof saveCrmTinyMCE === 'function') {
            saveCrmTinyMCE(contentSelector);
        }

        const subject = $('#support_course_mail_subject').val().trim();
        const content = $('#support_course_mail_content').val().trim();

        if (!subject) {
            if (typeof toast_danger === 'function') {
                toast_danger('Subject is required.');
            }
            return;
        }

        if (!content) {
            if (typeof toast_danger === 'function') {
                toast_danger('Content is required.');
            }
            return;
        }

        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i> Sending...');

        $.ajax({
            url: courseMailSendUrl,
            method: 'POST',
            dataType: 'json',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                subject: subject,
                content: content
            },
            success: function(res) {
                if (res && res.success) {
                    $('#supportCourseMailModal').modal('hide');
                    if (typeof toast_success === 'function') {
                        toast_success(res.message || 'Mail sent successfully.');
                    } else if (typeof show_alert === 'function') {
                        show_alert('success', res.message || 'Mail sent successfully.');
                    }
                } else if (typeof toast_danger === 'function') {
                    toast_danger((res && res.error) ? res.error : 'Failed to send mail.');
                } else if (typeof show_alert === 'function') {
                    show_alert('error', (res && res.error) ? res.error : 'Failed to send mail.');
                }
            },
            error: function(xhr) {
                let msg = 'Failed to send mail.';
                if (xhr.responseJSON) {
                    msg = xhr.responseJSON.error || xhr.responseJSON.message || msg;
                    if (xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                    }
                }
                if (typeof toast_danger === 'function') {
                    toast_danger(msg);
                } else if (typeof show_alert === 'function') {
                    show_alert('error', msg);
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    $('#supportCourseMailModal').on('hidden.bs.modal', function() {
        resetCourseMailModal();
    });
})();
</script>
@endpush
@endonce

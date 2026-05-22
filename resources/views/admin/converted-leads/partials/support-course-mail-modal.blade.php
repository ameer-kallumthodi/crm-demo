<div class="modal fade" id="supportCourseMailModal" tabindex="-1" aria-labelledby="supportCourseMailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supportCourseMailModalLabel">Send Mail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="supportCourseMailLoading" class="text-center py-4 d-none">
                    <i class="ti ti-loader-2 spin fs-3"></i>
                    <p class="mb-0 mt-2 text-muted">Loading mail template...</p>
                </div>
                <div id="supportCourseMailError" class="alert alert-danger d-none mb-0"></div>
                <form id="supportCourseMailForm" class="d-none">
                    <p class="mb-3 text-muted small">
                        To: <strong id="supportCourseMailRecipient"></strong>
                        <span class="d-block mt-1" id="supportCourseMailContext"></span>
                    </p>
                    <div class="mb-3">
                        <label class="form-label" for="support_course_mail_subject">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="support_course_mail_subject" name="subject" required maxlength="255">
                    </div>
                    <div class="mb-0">
                        <label class="form-label" for="support_course_mail_content">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="support_course_mail_content" name="content" rows="10"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary d-none" id="confirmSupportCourseMailBtn">
                    <i class="ti ti-mail"></i> Send Mail
                </button>
            </div>
        </div>
    </div>
</div>

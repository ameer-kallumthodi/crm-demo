<div class="container p-2">
    <form id="termsAndConditionsForm" action="{{ route('admin.teams.update-terms-and-conditions', $team->id) }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="terms_and_conditions" class="form-label">Terms and Conditions</label>
                    <textarea class="form-control" id="terms_and_conditions" name="terms_and_conditions" rows="12">{{ $termsAndConditions }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">
                <i class="ti ti-device-floppy"></i> Update Terms
            </button>
        </div>
    </form>
</div>

<script>
(function() {
    function initTermsTinyMCE() {
        if (typeof tinymce === 'undefined') {
            var script = document.createElement('script');
            script.src = '{{ asset("assets/mantis/js/plugins/tinymce/tinymce.min.js") }}';
            script.onload = function() {
                initializeTermsEditor();
            };
            document.head.appendChild(script);
        } else {
            initializeTermsEditor();
        }
    }

    function initializeTermsEditor() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setupTinyMCE();
            });
        } else {
            setTimeout(setupTinyMCE, 150);
        }
    }

    function setupTinyMCE() {
        var field = document.getElementById('terms_and_conditions');
        if (!field) return;

        if (tinymce.get('terms_and_conditions')) {
            tinymce.remove('#terms_and_conditions');
        }

        tinymce.init({
            selector: '#terms_and_conditions',
            height: 350,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
    }

    initTermsTinyMCE();
})();

$(document).ready(function() {
    $('#termsAndConditionsForm').on('submit', function(e) {
        e.preventDefault();

        if (typeof tinymce !== 'undefined' && tinymce.get('terms_and_conditions')) {
            tinymce.get('terms_and_conditions').save();
        }

        var form = $(this);
        var formData = new FormData(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();

        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="ti ti-loader-2 spin"></i> Updating...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#large_modal').modal('hide');
                if (typeof toast_success === 'function') {
                    toast_success(response.message || 'Terms and conditions updated successfully!');
                } else {
                    alert(response.message || 'Terms and conditions updated successfully!');
                }
            },
            error: function(xhr) {
                var errorMessage = 'An error occurred while updating.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                }
                if (typeof toast_danger === 'function') {
                    toast_danger(errorMessage);
                } else {
                    alert(errorMessage);
                }
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
});
</script>

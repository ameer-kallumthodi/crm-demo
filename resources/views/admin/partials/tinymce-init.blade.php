@once
@push('scripts')
<script>
(function() {
    function loadTinyMceScript(callback) {
        if (typeof tinymce !== 'undefined') {
            callback();
            return;
        }

        const existing = document.querySelector('script[data-crm-tinymce]');
        if (existing) {
            existing.addEventListener('load', callback);
            return;
        }

        const script = document.createElement('script');
        script.src = '{{ asset("assets/mantis/js/plugins/tinymce/tinymce.min.js") }}';
        script.setAttribute('data-crm-tinymce', '1');
        script.onload = callback;
        document.head.appendChild(script);
    }

    window.initCrmTinyMCE = function(selector, options) {
        options = options || {};
        const height = options.height || 350;

        loadTinyMceScript(function() {
            const run = function() {
                const field = document.querySelector(selector);
                if (!field) {
                    return;
                }

                const editorId = field.id || selector.replace('#', '');
                if (tinymce.get(editorId)) {
                    tinymce.remove(selector);
                }

                tinymce.init({
                    selector: selector,
                    height: height,
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
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(run, 150);
                });
            } else {
                setTimeout(run, 150);
            }
        });
    };

    window.destroyCrmTinyMCE = function(selector) {
        if (typeof tinymce === 'undefined') {
            return;
        }

        const field = document.querySelector(selector);
        if (!field) {
            return;
        }

        const editorId = field.id || selector.replace('#', '');
        if (tinymce.get(editorId)) {
            tinymce.remove(selector);
        }
    };

    window.saveCrmTinyMCE = function(selector) {
        if (typeof tinymce === 'undefined') {
            return;
        }

        const field = document.querySelector(selector);
        if (!field) {
            return;
        }

        const editorId = field.id || selector.replace('#', '');
        const editor = tinymce.get(editorId);
        if (editor) {
            editor.save();
        }
    };
})();
</script>
@endpush
@endonce

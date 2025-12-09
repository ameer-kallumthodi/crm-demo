import re, pathlib
pattern = re.compile(r'(\s*<a href="{{ route\(\'admin\.gmvss-mentor-converted-leads\.index\'\) }}" class="btn btn-outline-primary">\s*<i class="ti ti-user-star"></i> GMVSS Mentor List\s*</a>\s*)(?:<a href="{{ route\(\'admin\.digital-marketing-mentor-converted-leads\.index\'\) }}" class="btn btn-outline-primary">\s*<i class="ti ti-user-star"></i> Digital Marketing Mentor List\s*</a>\s*)?', re.MULTILINE)
replacement = """\1                    <a href=\"{{ route('admin.digital-marketing-mentor-converted-leads.index') }}\" class=\"btn btn-outline-primary\">\n                        <i class=\"ti ti-user-star\"></i> Digital Marketing Mentor List\n                    </a>\n                    """
base = pathlib.Path('resources/views/admin/converted-leads')
for path in base.rglob('*.blade.php'):
    text = path.read_text(encoding='utf-8')
    new_text = pattern.sub(replacement, text)
    if new_text != text:
        path.write_text(new_text, encoding='utf-8')

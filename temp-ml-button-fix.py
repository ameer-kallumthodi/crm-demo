from pathlib import Path
import re

root = Path('resources/views/admin/converted-leads')
pattern = re.compile(r'(?P<indent>\s*)<a href="{{ route\(\'admin\.digital-marketing-mentor-converted-leads\.index\'\) }}" class="btn btn-outline-primary(?P<active>\s+active)?">\s*<i class="ti ti-user-star"></i> Digital Marketing Mentor List\s*</a>', re.MULTILINE)
insert_template = "{indent}<a href=\"{{ route('admin.machine-learning-mentor-converted-leads.index') }}\" class=\"btn btn-outline-primary\">\n{indent}    <i class=\"ti ti-user-star\"></i> Machine Learning Mentor List\n{indent}</a>"
updated = []
for path in root.rglob('*.blade.php'):
    text = path.read_text()
    if 'machine-learning-mentor-converted-leads' in text or 'digital-marketing-mentor-converted-leads' not in text:
        continue
    def repl(match):
        indent = match.group('indent')
        return match.group(0) + "\n" + insert_template.format(indent=indent)
    new_text, count = pattern.subn(repl, text)
    if count:
        path.write_text(new_text)
        updated.append((path, count))
for p, c in updated:
    print(f"updated {p} ({c})")
print(f"total updated: {len(updated)}")

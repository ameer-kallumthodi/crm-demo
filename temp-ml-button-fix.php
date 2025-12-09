<?php
$root = __DIR__ . '/resources/views/admin/converted-leads';
$pattern = '/(?P<indent>^\s*)<a href="{{ route\\(\'admin\\.digital-marketing-mentor-converted-leads\\.index\'\) }}" class="btn btn-outline-primary(?: active)?">\s*<i class="ti ti-user-star"><\\/i> Digital Marketing Mentor List\s*<\\/a>/m';
$insertTpl = "<a href=\\\"{{ route('admin.machine-learning-mentor-converted-leads.index') }}\\\" class=\\\"btn btn-outline-primary\\\">\n%indent%    <i class=\\\"ti ti-user-star\\\"></i> Machine Learning Mentor List\n%indent%</a>";
$updated = [];
$dir = new RecursiveDirectoryIterator($root);
$it = new RecursiveIteratorIterator($dir);
foreach ($it as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') continue;
    $path = $file->getPathname();
    $contents = file_get_contents($path);
    if (strpos($contents, 'machine-learning-mentor-converted-leads') !== false || strpos($contents, 'digital-marketing-mentor-converted-leads') === false) {
        continue;
    }
    $new = preg_replace_callback($pattern, function($m) use ($insertTpl) {
        $indent = $m['indent'];
        $insert = str_replace('%indent%', $indent, $insertTpl);
        return $m[0] . "\n" . $indent . $insert;
    }, $contents, -1, $count);
    if ($count > 0) {
        file_put_contents($path, $new);
        $updated[] = [$path, $count];
    }
}
foreach ($updated as [$p, $c]) {
    echo "updated {$p} ({$c})\n";
}
echo "total updated: " . count($updated) . "\n";
?>

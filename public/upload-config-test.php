<?php
// Upload Configuration Test
echo "<h2>PHP Upload Configuration Test</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Setting</th><th>Current Value</th><th>Required</th><th>Status</th></tr>";

$settings = [
    'upload_max_filesize' => ['2M', '2M'],
    'post_max_size' => ['4M', '4M'],
    'max_file_uploads' => ['20', '20'],
    'max_execution_time' => ['300', '300'],
    'memory_limit' => ['256M', '256M'],
    'max_input_time' => ['300', '300']
];

foreach ($settings as $setting => $values) {
    $current = ini_get($setting);
    $required = $values[1];
    $status = ($current >= $required) ? '✅ OK' : '❌ Needs Update';
    echo "<tr><td>{$setting}</td><td>{$current}</td><td>{$required}</td><td>{$status}</td></tr>";
}

echo "</table>";

echo "<h3>Test File Upload (Max 2MB)</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>";
    echo "<strong>Upload Test Results:</strong><br>";
    echo "File name: " . $_FILES['test_file']['name'] . "<br>";
    echo "File size: " . number_format($_FILES['test_file']['size'] / 1024 / 1024, 2) . " MB<br>";
    echo "File type: " . $_FILES['test_file']['type'] . "<br>";
    echo "Upload error: " . $_FILES['test_file']['error'] . "<br>";
    
    if ($_FILES['test_file']['error'] === 0) {
        echo "<span style='color: green; font-weight: bold;'>✅ Upload successful!</span>";
    } else {
        $errors = [
            0 => 'No error',
            1 => 'File exceeds upload_max_filesize',
            2 => 'File exceeds MAX_FILE_SIZE',
            3 => 'File only partially uploaded',
            4 => 'No file uploaded',
            6 => 'Missing temporary folder',
            7 => 'Failed to write file to disk',
            8 => 'File upload stopped by extension'
        ];
        echo "<span style='color: red; font-weight: bold;'>❌ Upload failed: " . ($errors[$_FILES['test_file']['error']] ?? 'Unknown error') . "</span>";
    }
    echo "</div>";
}
?>

<form method="post" enctype="multipart/form-data" style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd;">
    <h4>Test File Upload:</h4>
    <input type="file" name="test_file" accept=".xlsx,.xls" style="margin: 10px 0;">
    <br>
    <button type="submit" style="background: #007cba; color: white; padding: 8px 16px; border: none; cursor: pointer;">Test Upload</button>
</form>

<p><strong>Note:</strong> If upload_max_filesize shows less than 2M, you may need to contact your hosting provider or update your server's PHP configuration.</p>

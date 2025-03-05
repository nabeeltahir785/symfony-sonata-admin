<?php
// Save as debug_admin_class.php in your project root

// Check the ProductAdmin file
$adminFile = __DIR__ . '/src/Admin/Product/ProductAdmin.php';
echo "Checking ProductAdmin file:\n";
echo "File path: $adminFile\n";
echo "File exists: " . (file_exists($adminFile) ? 'Yes' : 'No') . "\n";

if (file_exists($adminFile)) {
    $content = file_get_contents($adminFile);

    // Check namespace
    preg_match('/namespace\s+([^;]+);/', $content, $matches);
    $namespace = $matches[1] ?? 'Not found';
    echo "Namespace: $namespace\n";

    // Check class name
    preg_match('/class\s+(\w+)/', $content, $matches);
    $className = $matches[1] ?? 'Not found';
    echo "Class name: $className\n";

    // Check fully qualified class name
    $fullyQualifiedClass = "$namespace\\$className";
    echo "Fully qualified class name: $fullyQualifiedClass\n";

    // Check services.yaml entry
    $servicesYaml = file_get_contents(__DIR__ . '/config/services.yaml');
    preg_match('/app\.admin\.product:\s+class:\s+([^\s]+)/', $servicesYaml, $matches);
    $configuredClass = $matches[1] ?? 'Not found';
    echo "Configured class in services.yaml: $configuredClass\n";

    echo "Match between code and config: " . ($fullyQualifiedClass === $configuredClass ? 'Yes' : 'No') . "\n";
}

echo "\nCheck auto-loading:\n";

// Try to autoload the class
try {
    require_once __DIR__ . '/vendor/autoload.php';

    $expectedClass = 'App\\Admin\\Product\\ProductAdmin';
    echo "Class $expectedClass exists: " . (class_exists($expectedClass) ? 'Yes' : 'No') . "\n";

    $alternateClass = 'App\\Admin\\ProductAdmin';
    echo "Class $alternateClass exists: " . (class_exists($alternateClass) ? 'Yes' : 'No') . "\n";
} catch (\Exception $e) {
    echo "Autoload error: " . $e->getMessage() . "\n";
}
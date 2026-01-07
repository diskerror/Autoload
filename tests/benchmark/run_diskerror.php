<?php
$start = microtime(true);
$startMem = memory_get_usage();

// Use the local path we patched in Autoloader.php to detect vendor correctly
require __DIR__ . '/../../autoload.php';

$initTime = microtime(true) - $start;

$loadStart = microtime(true);
for ($i = 0; $i < 2000; $i++) {
    $class = "Bench\\Classes\\Class$i";
    new $class();
}
$loadTime = microtime(true) - $loadStart;
$endMem = memory_get_usage();

echo json_encode([
    'type' => 'Diskerror',
    'init_time' => $initTime,
    'load_time' => $loadTime,
    'total_time' => $initTime + $loadTime,
    'memory_bytes' => $endMem - $startMem
]);


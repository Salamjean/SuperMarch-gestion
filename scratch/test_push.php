<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting PUSH test...\n";
try {
    $controller = app(\App\Http\Controllers\Local\LocalSyncController::class);
    $response = $controller->pushPending();
    echo "Result: " . json_encode($response->getData(), JSON_PRETTY_PRINT) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

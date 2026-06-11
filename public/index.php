<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Définir un répertoire temporaire local valide pour éviter le warning tempnam() sous Windows
$localTempDir = realpath(__DIR__ . '/../storage/framework/cache');
if ($localTempDir) {
    putenv("TMPDIR={$localTempDir}");
    putenv("TMP={$localTempDir}");
    putenv("TEMP={$localTempDir}");
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Masquer les warnings, notices et deprecations après le boot pour éviter les crashs de tempnam() sous Windows
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

$app->handleRequest(Request::capture());

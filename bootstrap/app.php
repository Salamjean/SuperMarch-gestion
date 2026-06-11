<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'local/sync/*',
        ]);

        $middleware->alias([
            'admin'    => \App\Http\Middleware\AdminMiddleware::class,
            'employee' => \App\Http\Middleware\EmployeeMiddleware::class,
            'magasinier' => \App\Http\Middleware\MagasinierMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Configuration dynamique du dossier de stockage et de la DB SQLite si le dossier par défaut n'est pas inscriptible
$defaultStorage = $app->storagePath();
if (!is_writable($defaultStorage) || !is_writable($defaultStorage . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views')) {
    $appData = getenv('APPDATA') ?: (getenv('USERPROFILE') ? getenv('USERPROFILE') . DIRECTORY_SEPARATOR . 'AppData' . DIRECTORY_SEPARATOR . 'Roaming' : null);
    if ($appData) {
        $appDataDir = $appData . DIRECTORY_SEPARATOR . 'SuperMarche';
        $newStorage = $appDataDir . DIRECTORY_SEPARATOR . 'storage';
        
        // Créer les sous-dossiers nécessaires s'ils n'existent pas
        $subDirs = [
            '',
            DIRECTORY_SEPARATOR . 'app',
            DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public',
            DIRECTORY_SEPARATOR . 'framework',
            DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache',
            DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'data',
            DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'sessions',
            DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views',
            DIRECTORY_SEPARATOR . 'logs',
        ];
        
        foreach ($subDirs as $sub) {
            $dirPath = $newStorage . $sub;
            if (!file_exists($dirPath)) {
                @mkdir($dirPath, 0755, true);
            }
        }
        
        if (is_writable($newStorage)) {
            $app->useStoragePath($newStorage);
            
            // Rediriger également la base de données SQLite vers AppData
            $dbPath = $appDataDir . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database_local.sqlite';
            // Créer le dossier database s'il n'existe pas
            $dbDir = dirname($dbPath);
            if (!file_exists($dbDir)) {
                @mkdir($dbDir, 0755, true);
            }
            
            putenv("DB_DATABASE={$dbPath}");
            $_ENV['DB_DATABASE'] = $dbPath;
            $_SERVER['DB_DATABASE'] = $dbPath;
        }
    }
}

return $app;

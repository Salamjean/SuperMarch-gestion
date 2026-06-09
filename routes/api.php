<?php

use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Synchronisation SQLite ↔ MySQL
|--------------------------------------------------------------------------
| Ces routes sont utilisées par l'application Electron (main.js) pour
| synchroniser la base SQLite locale avec la base MySQL en ligne.
| Sécurisées par la clé X-Sync-Key dans l'en-tête HTTP.
|--------------------------------------------------------------------------
*/

// Pull : télécharger toutes les données MySQL → SQLite
Route::get('/sync/pull', [SyncController::class, 'pull']);

// Push : envoyer les opérations hors-ligne SQLite → MySQL
Route::post('/sync/push', [SyncController::class, 'push']);

// Ping pour vérifier la connectivité
Route::get('/sync/ping', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]);
});

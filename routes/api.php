<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Agence\CreneauRendezVousController;

Route::prefix('search')->group(function () {
    Route::get('/quartiers', [SearchController::class, 'quartiers'])->name('api.search.quartiers');
    Route::get('/besoins', [SearchController::class, 'besoins'])->name('api.search.besoins');
    Route::get('/biens', [SearchController::class, 'biens'])->name('api.search.biens');
    Route::get('/autocomplete', [SearchController::class, 'autocomplete'])->name('api.search.autocomplete');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('api.notifications.count');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.read-all');
});

// AJOUTER CETTE ROUTE (sans middleware auth pour que les particuliers puissent y accéder)
Route::get('/creneaux/disponibles', [CreneauRendezVousController::class, 'getDisponibles'])->name('api.creneaux.disponibles');
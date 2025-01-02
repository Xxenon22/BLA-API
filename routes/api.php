<?php

use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\MaterialTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Prefix untuk Folder
Route::prefix('folders')->group(function () {
    // Folder CRUD
    Route::get('/', [FolderController::class, 'index']); // Ambil semua folder
    Route::get('/{id}', [FolderController::class, 'show']); // Ambil detail folder
    Route::post('/', [FolderController::class, 'store']); // Tambah folder
    Route::put('/{id}', [FolderController::class, 'update']); // Update folder
    Route::delete('/{id}', [FolderController::class, 'destroy']); // Hapus folder

    // Sub-folder dalam folder
    Route::get('/{folderId}/subfolders', [FolderController::class, 'getChildren']); // Ambil sub-folder
    // Route::get('/{folderId}/subfolders/files/{fileId}', [FolderController::class, 'getFilesBySubFolders']); //untuk melihat files berdasarkan Subfolder
    Route::post('/{folderId}/subfolders', [FolderController::class, 'createSubFolder']); // Tambah sub-folder


    // Files dalam folder
    Route::prefix('{folderId}/files')->group(function () {
        // File CRUD
        Route::get('/', [FileController::class, 'index']); // Ambil semua file
        Route::get('/{fileId}', [FileController::class, 'show']); // Ambil detail file
        Route::post('/', [FileController::class, 'store']); // Tambah file
        Route::put('/{fileId}', [FileController::class, 'update']); // Update file
        Route::delete('/{fileId}', [FileController::class, 'destroy']); // Hapus file
    });
});

// Material Type Routes
Route::prefix('material-types')->group(function () {
    Route::get('/', [MaterialTypeController::class, 'index']);
    Route::post('/', [MaterialTypeController::class, 'store']);
    Route::get('/{id}', [MaterialTypeController::class, 'show']);
    Route::put('/{id}', [MaterialTypeController::class, 'update']);
    Route::delete('/{id}', [MaterialTypeController::class, 'destroy']);
});

Route::get('/files/filter-by-material-type/{materialTypeId}', [FileController::class, 'getFilesByMaterialType']);
Route::get('/files/{id}', [FileController::class, 'showDetailFileByMaterialType']);

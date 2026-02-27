<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KycController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AgreementController;
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/test', function () {
    return view('test');
});
Route::get('/check-ip', function () {
    return response()->json([
        'ipv4' => file_get_contents('https://api.ipify.org'),
        'ipv6' => file_get_contents('https://api64.ipify.org')
    ]);
});

// Temporary route to clear cache - DELETE AFTER USE
Route::get('/clear-cache', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return response()->json(['status' => 'success', 'message' => 'Cache cleared successfully']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
});
Route::get('/test/{clientId}', function ($clientId) {
    $client = \App\Models\Client::with('invoice')->findOrFail($clientId);
    return view('test', compact('client'));
});
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/clients', [ClientController::class, 'index'])
        ->name('clients.index');

    Route::get('/clients/create', [ClientController::class, 'show'])
        ->name('clients.create');

    Route::get('/clients/{id}/edit', [ClientController::class, 'show'])
        ->name('clients.edit');

    Route::post('/clients', [ClientController::class, 'store'])
        ->name('clients.store');

    Route::put('/clients/{id}', [ClientController::class, 'update'])
        ->name('clients.update');

    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])
        ->name('clients.destroy');

    Route::post('/clients/import', [ClientController::class, 'import'])
        ->name('clients.import');

    Route::get('/clients/export', [ClientController::class, 'export'])
        ->name('clients.export');

    /*
    |--------------------------------------------------------------------------
    | Invoice
    |--------------------------------------------------------------------------
    */
    Route::get('/invoice/pdf/{clientId}', [InvoiceController::class, 'pdf'])
        ->name('invoice.pdf');

    Route::get('/invoice/word/{clientId}', [InvoiceController::class, 'word'])
        ->name('invoice.word');

    /*
    |--------------------------------------------------------------------------
    | Agreement
    |--------------------------------------------------------------------------
    */
    Route::get('/agreement/pdf/{clientId}', [AgreementController::class, 'pdf'])
        ->name('agreement.pdf');

    Route::get('/agreement/word/{clientId}', [AgreementController::class, 'word'])
        ->name('agreement.word');
        Route::get('/invoice/email/{id}', [InvoiceController::class, 'sendEmail'])
    ->name('invoice.email');

Route::get('/agreement/email/{id}', [AgreementController::class, 'sendEmail'])
    ->name('agreement.email');

});

/*
|--------------------------------------------------------------------------
| CVL KRA KYC Routes
|--------------------------------------------------------------------------
*/
Route::prefix('kyc')->middleware('auth')->group(function () {
    // Web UI routes
    Route::get('/', [KycController::class, 'index'])->name('kyc.index');
    Route::get('/check', [KycController::class, 'check'])->name('kyc.check');
    Route::post('/check', [KycController::class, 'checkStore'])->name('kyc.check.store');
    Route::get('/check/{pan}', [KycController::class, 'checkShow'])->name('kyc.check.show');
    Route::get('/docs', [KycController::class, 'docs'])->name('kyc.docs');
    Route::get('/download/{pan}', [KycController::class, 'downloadZipByPan'])->name('kyc.download.zip');
    
    // Records Management
    Route::get('/records', [KycController::class, 'manualIndex'])->name('kyc.records');
    Route::get('/records/export', [KycController::class, 'export'])->name('kyc.export');
    Route::get('/records/{id}', [KycController::class, 'recordShow'])->name('kyc.record.show');
    Route::delete('/records/{id}', [KycController::class, 'deleteRecord'])->name('kyc.record.delete');
    
    // Manual KYC Entry
    Route::get('/manual', [KycController::class, 'manualIndex'])->name('kyc.manual');
    Route::post('/manual', [KycController::class, 'manualStore'])->name('kyc.manual.store');
    Route::get('/manual/{id}/edit', [KycController::class, 'manualEdit'])->name('kyc.manual.edit');
    Route::put('/manual/{id}', [KycController::class, 'manualUpdate'])->name('kyc.manual.update');
    Route::get('/manual/{id}/documents', [KycController::class, 'manualDocuments'])->name('kyc.manual.documents');
    Route::get('/manual/{id}/documents/{filename}', [KycController::class, 'downloadRecordDocument'])->name('kyc.record.document.download');
    Route::delete('/manual/{id}/documents/{filename}', [KycController::class, 'deleteDocument'])->name('kyc.record.document.delete');
    Route::post('/manual/{id}/documents', [KycController::class, 'manualUploadDocument'])->name('kyc.manual.upload');
    
    // API routes
    Route::post('/verify', [KycController::class, 'verify'])->name('kyc.verify');
    Route::get('/status', [KycController::class, 'getStatus'])->name('kyc.status');
    Route::get('/details', [KycController::class, 'getDetails'])->name('kyc.details');
    Route::get('/documents/download', [KycController::class, 'downloadDocuments'])->name('kyc.documents.download');
    Route::get('/document/{filename}', [KycController::class, 'downloadDocument'])->name('kyc.document.download');
    Route::get('/environment', [KycController::class, 'environment'])->name('kyc.environment');
});

/*
|--------------------------------------------------------------------------
| Bulk KYC Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\BulkKycController;
Route::prefix('kyc/bulk')->middleware('auth')->group(function () {
    Route::get('/', [BulkKycController::class, 'index'])->name('kyc.bulk.index');
    Route::post('/upload', [BulkKycController::class, 'upload'])->name('kyc.bulk.upload');
    Route::get('/{batch}', [BulkKycController::class, 'show'])->name('kyc.bulk.show');
    Route::post('/{batch}/process', [BulkKycController::class, 'process'])->name('kyc.bulk.process');
    Route::get('/{batch}/download', [BulkKycController::class, 'downloadZip'])->name('kyc.bulk.download');
    Route::get('/{batch}/error-report', [BulkKycController::class, 'downloadErrorReport'])->name('kyc.bulk.error-report');
    Route::get('/{batch}/status', [BulkKycController::class, 'status'])->name('kyc.bulk.status');
    Route::delete('/{batch}', [BulkKycController::class, 'destroy'])->name('kyc.bulk.destroy');
});

require __DIR__.'/auth.php';
Route::get('/_test-mail', function(){
    \Illuminate\Support\Facades\Mail::raw('SMTP test', function($m){
        $m->to('np4375@gmail.com')->subject('SMTP test');
    });
    return 'sent';
});
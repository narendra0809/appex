<?php

namespace App\Http\Controllers;

use App\Models\BulkKycBatch;
use App\Models\BulkKycRecord;
use App\Models\KycRecord;
use App\Services\CvlKraService;
use App\Imports\BulkKycImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BulkKycController extends Controller
{
    private CvlKraService $cvlKraService;

    public function __construct(CvlKraService $cvlKraService)
    {
        $this->cvlKraService = $cvlKraService;
    }

    /**
     * Show bulk KYC upload form
     */
    public function index()
    {
        $batches = BulkKycBatch::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('kyc.bulk.index', compact('batches'));
    }

    /**
     * Upload and process Excel file
     */
    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
            'batch_name' => 'nullable|string|max:255',
        ]);

        try {
            // Create batch record
            $batch = BulkKycBatch::create([
                'batch_name' => $request->batch_name ?? 'Batch ' . now()->format('Y-m-d H:i'),
                'original_filename' => $request->file('excel_file')->getClientOriginalName(),
                'status' => BulkKycBatch::STATUS_PENDING,
                'user_id' => auth()->id(),
            ]);

            // Store the uploaded file
            $filePath = $request->file('excel_file')->store('bulk_kyc_uploads');
            
            // Import records from Excel
            Excel::import(new BulkKycImport($batch), $request->file('excel_file'));

            // Check if any records were imported
            if ($batch->total_records === 0) {
                $batch->delete();
                return redirect()->back()->with('error', 'No valid records found in the Excel file. Please ensure the file contains PAN Number and Date of Birth columns.');
            }

            Log::info('Bulk KYC batch created', [
                'batch_id' => $batch->id,
                'total_records' => $batch->total_records,
            ]);

            return redirect()->route('kyc.bulk.show', $batch->id)
                ->with('success', "Excel file uploaded successfully. {$batch->total_records} records found.");

        } catch (\Exception $e) {
            Log::error('Bulk KYC upload failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to process Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Show batch details and processing status
     */
    public function show(BulkKycBatch $batch)
    {
        $batch->load(['records.kycRecord']);
        
        return view('kyc.bulk.show', compact('batch'));
    }

    /**
     * Process KYC for all records in a batch
     */
    public function process(BulkKycBatch $batch)
    {
        if ($batch->status === BulkKycBatch::STATUS_PROCESSING) {
            return redirect()->back()->with('error', 'Batch is already being processed.');
        }

        if ($batch->status === BulkKycBatch::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'Batch has already been processed.');
        }

        // Update batch status
        $batch->update([
            'status' => BulkKycBatch::STATUS_PROCESSING,
            'started_at' => now(),
        ]);

        // Get pending records
        $records = $batch->records()->where('status', BulkKycRecord::STATUS_PENDING)->get();
        
        $errors = [];
        $successCount = 0;
        $failedCount = 0;

        foreach ($records as $record) {
            // Update record status to processing
            $record->update(['status' => BulkKycRecord::STATUS_PROCESSING]);

            try {
                // Call KYC verification
                $result = $this->cvlKraService->verifyKyc($record->pan, $record->dob);

                if ($result['success']) {
                    // Save KYC record
                    $kycRecord = $this->saveKycRecord($record->pan, $record->dob, $result);
                    
                    // Update bulk record
                    $record->update([
                        'status' => BulkKycRecord::STATUS_SUCCESS,
                        'kyc_record_id' => $kycRecord->id,
                        'document_path' => $kycRecord->zip_path,
                    ]);
                    
                    $successCount++;
                } else {
                    $errorMsg = $result['error'] ?? 'KYC verification failed';
                    $record->update([
                        'status' => BulkKycRecord::STATUS_FAILED,
                        'error_message' => $errorMsg,
                    ]);
                    $errors[] = "PAN {$record->pan}: {$errorMsg}";
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                $record->update([
                    'status' => BulkKycRecord::STATUS_FAILED,
                    'error_message' => $errorMsg,
                ]);
                $errors[] = "PAN {$record->pan}: {$errorMsg}";
                $failedCount++;
            }

            // Update batch progress
            $batch->update([
                'processed_records' => $batch->processed_records + 1,
                'success_count' => $successCount,
                'failed_count' => $failedCount,
            ]);
        }

        // Create combined ZIP file
        $zipPath = $this->createCombinedZip($batch);

        // Update batch status
        $batch->update([
            'status' => BulkKycBatch::STATUS_COMPLETED,
            'completed_at' => now(),
            'error_log' => !empty($errors) ? implode("\n", $errors) : null,
            'result_zip_path' => $zipPath,
        ]);

        Log::info('Bulk KYC batch processed', [
            'batch_id' => $batch->id,
            'success' => $successCount,
            'failed' => $failedCount,
        ]);

        return redirect()->route('kyc.bulk.show', $batch->id)
            ->with('success', "Batch processed. Success: {$successCount}, Failed: {$failedCount}");
    }

    /**
     * Save KYC record from API response
     */
    private function saveKycRecord(string $pan, string $dob, array $result): KycRecord
    {
        $kyc = KycRecord::firstOrNew(['pan' => $pan]);
        
        $kyc->dob = $dob;
        $kyc->status = $this->mapApiStatusToRecordStatus($result['kyc_status'] ?? 'Unknown');
        $kyc->verified_at = now();
        $kyc->kyc_json = json_encode($result['kyc_data'] ?? $result);
        
        // Save extracted data from KYC response
        $extractedData = $result['extracted_data'] ?? [];
        $kyc->name = $extractedData['name'] ?? null;
        $kyc->father_name = $extractedData['father_name'] ?? null;
        $kyc->address = $extractedData['address'] ?? null;
        $kyc->state = $extractedData['state'] ?? null;
        $kyc->city = $extractedData['city'] ?? null;
        $kyc->pincode = $extractedData['pincode'] ?? null;
        
        // Save ZIP path if downloaded
        if (!empty($result['zip_path'])) {
            $kyc->zip_path = $result['zip_path'];
            $kyc->document_path = $result['zip_path'];
        }
        
        // Save REF_NO
        if (!empty($result['ref_no'])) {
            $kyc->ref_no = $result['ref_no'];
        }
        
        // Save API raw response
        if (isset($result['kyc_data'])) {
            $kyc->api_raw_response = json_encode($result['kyc_data']);
        }
        
        $kyc->save();
        
        return $kyc;
    }

    /**
     * Map API status to record status
     */
    private function mapApiStatusToRecordStatus(string $apiStatus): string
    {
        return match(strtolower($apiStatus)) {
            'verified', 'match', 'success' => KycRecord::STATUS_VERIFIED,
            'notfound', 'not_found', 'n' => KycRecord::STATUS_NOT_FOUND,
            default => KycRecord::STATUS_PENDING,
        };
    }

    /**
     * Create combined ZIP file with all documents
     */
    private function createCombinedZip(BulkKycBatch $batch): ?string
    {
        $successRecords = $batch->records()
            ->where('status', BulkKycRecord::STATUS_SUCCESS)
            ->whereNotNull('document_path')
            ->get();

        if ($successRecords->isEmpty()) {
            return null;
        }

        $zipFileName = "bulk_kyc_batch_{$batch->id}_" . now()->format('YmdHis') . '.zip';
        $zipPath = 'bulk_kyc_zips/' . $zipFileName;
        $fullZipPath = Storage::path($zipPath);

        // Ensure directory exists
        Storage::makeDirectory('bulk_kyc_zips');

        $zip = new ZipArchive();
        if ($zip->open($fullZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            Log::error('Failed to create ZIP file', ['path' => $fullZipPath]);
            return null;
        }

        foreach ($successRecords as $record) {
            if ($record->kycRecord && $record->kycRecord->zip_path) {
                $kycZipPath = Storage::path($record->kycRecord->zip_path);
                
                if (file_exists($kycZipPath)) {
                    // Create a folder for each PAN
                    $panFolder = $record->pan . '/';
                    
                    // Extract and add files from individual KYC ZIP
                    $tempZip = new ZipArchive();
                    if ($tempZip->open($kycZipPath) === TRUE) {
                        for ($i = 0; $i < $tempZip->numFiles; $i++) {
                            $filename = $tempZip->getNameIndex($i);
                            $fileContent = $tempZip->getFromIndex($i);
                            if ($fileContent !== false) {
                                $zip->addFromString($panFolder . $filename, $fileContent);
                            }
                        }
                        $tempZip->close();
                    }
                }
            }
        }

        $zip->close();

        return $zipPath;
    }

    /**
     * Download combined ZIP file
     */
    public function downloadZip(BulkKycBatch $batch): StreamedResponse
    {
        if (!$batch->result_zip_path || !Storage::exists($batch->result_zip_path)) {
            abort(404, 'ZIP file not found for this batch.');
        }

        $filename = "KYC_Batch_{$batch->id}_" . now()->format('Ymd') . '.zip';
        
        return Storage::download($batch->result_zip_path, $filename);
    }

    /**
     * Download error report as Excel
     */
    public function downloadErrorReport(BulkKycBatch $batch)
    {
        $failedRecords = $batch->records()
            ->where('status', BulkKycRecord::STATUS_FAILED)
            ->get();

        if ($failedRecords->isEmpty()) {
            return redirect()->back()->with('info', 'No failed records to export.');
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="error_report_batch_' . $batch->id . '.csv"',
        ];

        $callback = function() use ($failedRecords) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['PAN Number', 'DOB', 'Error Message']);

            foreach ($failedRecords as $record) {
                fputcsv($file, [
                    $record->pan,
                    $record->dob,
                    $record->error_message,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete a batch and its records
     */
    public function destroy(BulkKycBatch $batch)
    {
        // Delete associated files
        if ($batch->result_zip_path && Storage::exists($batch->result_zip_path)) {
            Storage::delete($batch->result_zip_path);
        }

        // Delete batch (cascade will delete records)
        $batch->delete();

        return redirect()->route('kyc.bulk.index')
            ->with('success', 'Batch deleted successfully.');
    }

    /**
     * Get batch status (for AJAX polling)
     */
    public function status(BulkKycBatch $batch)
    {
        return response()->json([
            'status' => $batch->status,
            'total_records' => $batch->total_records,
            'processed_records' => $batch->processed_records,
            'success_count' => $batch->success_count,
            'failed_count' => $batch->failed_count,
            'progress' => $batch->progress,
        ]);
    }
}

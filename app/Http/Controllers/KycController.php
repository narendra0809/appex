<?php

namespace App\Http\Controllers;

use App\Models\KycRecord;
use App\Services\CvlKraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KycController extends Controller
{
    private CvlKraService $cvlKraService;

    public function __construct(CvlKraService $cvlKraService)
    {
        $this->cvlKraService = $cvlKraService;
    }

    /**
     * Show KYC verification form
     */
    public function index()
    {
        $envInfo = $this->cvlKraService->getEnvironmentInfo();
        return view('kyc.index', compact('envInfo'));
    }

    /**
     * Show KYC Check form
     */
    public function check()
    {
        $recentRecords = KycRecord::orderBy('created_at', 'desc')->take(10)->get();
        return view('kyc.check', compact('recentRecords'));
    }

    /**
     * Process KYC Check - API call, save to DB + server, ZIP downloads to browser
     */
    public function checkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pan' => 'required|string|size:10|regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/',
            'dob' => ['required', 'string', 'regex:/^\d{1,2}[-\/]\d{1,2}[-\/]\d{4}$/', function ($attr, $value, $fail) {
                $d = \DateTime::createFromFormat('d-m-Y', str_replace('/', '-', $value));
                if (!$d) $d = \DateTime::createFromFormat('j-n-Y', str_replace('/', '-', $value));
                if (!$d || $d >= now()) $fail('DOB must be a valid past date (e.g. 21-5-1997 or 21-05-1997)');
            }],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $pan = strtoupper($request->pan);
        $dob = $request->dob;

        Log::info('KYC Check Started', ['pan' => $pan, 'dob' => $dob]);

        // Use verifyKyc - gets KYC data + ZIP, saves JSON to storage
        $result = $this->cvlKraService->verifyKyc($pan, $dob);

        if ($result['success']) {
            $this->saveKycRecord($pan, $dob, $result);
            $request->session()->flash('success', 'KYC verified for PAN: ' . $pan . '. ZIP downloaded.');
            $request->session()->flash('download_pan', $pan);
            return redirect()->route('kyc.records');
        }

        Log::error('KYC Check Failed', ['pan' => $pan, 'error' => $result['error'] ?? 'Unknown error']);
        return redirect()->back()->withErrors(['error' => $result['error'] ?? 'KYC verification failed'])->withInput();
    }

    /**
     * Save KYC record from API response
     */
    private function saveKycRecordFromApi(string $pan, string $dob, array $result): KycRecord
    {
        $kyc = KycRecord::firstOrNew(['pan' => $pan]);
        
        $kyc->dob = $dob;
        $kyc->status = $this->mapApiStatusToRecordStatus($result['kyc_status'] ?? 'Unknown');
        $kyc->verified_at = now();
        $kyc->kyc_json = json_encode($result);
        
        // Save KYC status
        $kyc->kyc_status = $result['kyc_status'] ?? null;
        
        // Save documents info
        if (isset($result['documents']['success']) && $result['documents']['success']) {
            $kyc->document_count = $result['documents']['count'] ?? 0;
            $kyc->document_path = $this->saveDocumentsFromApi($pan, $result['documents']);
        }
        
        // Extract and save name from response if available
        $kycData = $result['data'] ?? [];
        if (isset($kycData['KYC_DATA'])) {
            $kycData = $kycData['KYC_DATA'];
        }
        $kyc->name = $kycData['APP_HOLDER_NAME'] ?? $kycData['APP_NAME'] ?? null;
        $kyc->father_name = $kycData['APP_FATHER_NAME'] ?? null;
        $kyc->address = $kycData['APP_ADDRESS'] ?? null;
        $kyc->state = $kycData['APP_STATE'] ?? null;
        $kyc->city = $kycData['APP_CITY'] ?? null;
        $kyc->pincode = $kycData['APP_PINCODE'] ?? null;
        
        $kyc->save();
        
        return $kyc;
    }

    /**
     * Show KYC Check result
     */
    public function checkShow(Request $request, string $pan)
    {
        $pan = strtoupper($pan);
        
        // Get latest record for this PAN
        $record = KycRecord::where('pan', $pan)->orderBy('created_at', 'desc')->first();
        
        if (!$record) {
            return redirect()->route('kyc.check')->with('error', 'No KYC record found for this PAN');
        }
        
        // Decode stored result
        $result = json_decode($record->kyc_json, true);
        
        // Get recent records
        $recentRecords = KycRecord::orderBy('created_at', 'desc')->take(10)->get();
        
        return view('kyc.check', compact('result', 'recentRecords'));
    }

    /**
     * Download KYC ZIP by PAN (for records with zip_path)
     */
    public function downloadZipByPan(string $pan): StreamedResponse
    {
        $pan = strtoupper($pan);
        $record = KycRecord::where('pan', $pan)->whereNotNull('zip_path')->orderBy('verified_at', 'desc')->first();

        if (!$record || !Storage::exists($record->zip_path)) {
            abort(404, 'KYC documents not found for this PAN');
        }

        $filename = $pan . '_KYC_DOCUMENTS_' . now()->format('Ymd') . '.zip';
        return Storage::download($record->zip_path, $filename);
    }

    /**
     * Download document file
     */
    public function downloadDocument(Request $request, string $filename)
    {
        $path = 'kyc_documents/' . $filename;
        
        if (!Storage::exists($path)) {
            abort(404, 'Document not found');
        }
        
        return Storage::download($path);
    }

    /**
     * Verify KYC with PAN and DOB - Saves to database
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pan' => 'required|string|size:10|regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/',
            'dob' => 'required|string|date_format:d/m/Y|before:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $pan = strtoupper($request->pan);
        $dob = $request->dob;

        Log::info('KYC Verification Started', ['pan' => $pan, 'dob' => $dob]);

        $result = $this->cvlKraService->verifyKyc($pan, $dob);

        if ($result['success']) {
            // Save to database
            $this->saveKycRecord($pan, $dob, $result);
            
            Log::info('KYC Verification Completed', ['pan' => $pan, 'status' => $result['kyc_status']]);
            
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        }

        Log::error('KYC Verification Failed', ['pan' => $pan, 'error' => $result['error']]);
        
        return response()->json([
            'success' => false,
            'error' => $result['error'] ?? 'KYC verification failed',
        ], 400);
    }

    /**
     * Save KYC record to database
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
            $kyc->document_path = $result['zip_path']; // For backward compatibility
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
     * Save documents from API response
     */
    private function saveDocumentsFromApi(string $pan, array $documentResult): ?string
    {
        $documents = $documentResult['documents'] ?? [];
        if (empty($documents)) {
            return null;
        }
        
        // Create directory for this PAN
        $storagePath = 'kyc_records/' . strtoupper($pan) . '/';
        if (!Storage::exists($storagePath)) {
            Storage::makeDirectory($storagePath);
        }
        
        // Copy all documents
        $savedPaths = [];
        foreach ($documents as $doc) {
            if (isset($doc['path']) && Storage::exists($doc['path'])) {
                $filename = $doc['filename'] ?? basename($doc['path']);
                $destination = $storagePath . $filename;
                Storage::copy($doc['path'], $destination);
                $savedPaths[] = $destination;
            }
        }
        
        // Return JSON of saved documents
        return !empty($savedPaths) ? json_encode($savedPaths) : null;
    }

    /**
     * Get PAN status only
     */
    public function getStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pan' => 'required|string|size:10|regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/',
            'dob' => 'required|string|date_format:d/m/Y',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->cvlKraService->getPanStatus($request->pan, $request->dob);

        return response()->json($result);
    }

    /**
     * Get full KYC details
     */
    public function getDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pan' => 'required|string|size:10|regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/',
            'dob' => 'required|string|date_format:d/m/Y',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->cvlKraService->getKycDetails($request->pan, $request->dob);

        return response()->json($result);
    }

    /**
     * Download KYC documents ZIP file
     */
    public function downloadDocuments(Request $request): StreamedResponse
    {
        $validator = Validator::make($request->all(), [
            'pan' => 'required|string|size:10|regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/',
            'dob' => 'required|string|date_format:d/m/Y',
        ]);

        if ($validator->fails()) {
            abort(422, 'Invalid PAN or DOB format');
        }

        $result = $this->cvlKraService->downloadDocuments($request->pan, $request->dob);

        if (!$result['success']) {
            abort(404, $result['error'] ?? 'Documents not found');
        }

        if (!Storage::exists($result['path'])) {
            abort(404, 'Document file not found');
        }

        return Storage::download(
            $result['path'],
            'KYC_' . $request->pan . '_' . now()->format('Ymd') . '.zip'
        );
    }

    /**
     * Get environment info
     */
    public function environment()
    {
        return response()->json($this->cvlKraService->getEnvironmentInfo());
    }

    /**
     * API documentation endpoint
     */
    public function docs()
    {
        return view('kyc.docs');
    }

    // ==================== Manual KYC Tracking Methods ====================

    /**
     * Show manual KYC tracker - All records list
     */
    public function manualIndex(Request $request)
    {
        $query = KycRecord::query();
        
        // Search filter
        if ($request->search) {
            $query->where('pan', 'LIKE', '%' . strtoupper($request->search) . '%')
                  ->orWhere('name', 'LIKE', '%' . $request->search . '%');
        }
        
        // Status filter
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $perPage = $request->per_page ?? 10;
        $kycs = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return view('kyc.records-index', compact('kycs'));
    }

    /**
     * Export KYC records to Excel
     */
    public function export(Request $request)
    {
        $query = KycRecord::query();
        
        // Apply same filters as index
        if ($request->search) {
            $query->where('pan', 'LIKE', '%' . strtoupper($request->search) . '%')
                  ->orWhere('name', 'LIKE', '%' . $request->search . '%');
        }
        
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $kycs = $query->orderBy('created_at', 'desc')->get();
        
        $data = [
            ['PAN', 'Name', 'DOB', 'KYC Status', 'Status', 'ZIP', 'Verified Date']
        ];
        
        foreach ($kycs as $kyc) {
            $data[] = [
                $kyc->pan,
                $kyc->name ?? '-',
                $kyc->dob,
                $kyc->kyc_status ?? '-',
                $kyc->status,
                $kyc->zip_path ? 'Yes' : 'No',
                $kyc->verified_at ? $kyc->verified_at->format('d/m/Y') : '-',
            ];
        }
        
        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 'kyc_records_' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Show single KYC record details
     */
    public function recordShow($id)
    {
        $kyc = KycRecord::findOrFail($id);
        return view('kyc.record-show', compact('kyc'));
    }

    /**
     * Store manual KYC record
     */
    public function manualStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pan' => 'required|string|size:10|regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/',
            'dob' => 'required|string',
            'name' => 'nullable|string|max:255',
            'status' => 'required|in:pending,verified,not_found',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $kyc = KycRecord::create([
            'pan' => strtoupper($request->pan),
            'name' => $request->name,
            'dob' => $request->dob,
            'status' => $request->status,
            'verified_at' => $request->status === 'verified' ? now() : null,
        ]);

        return redirect()->route('kyc.records')->with('success', 'KYC record added successfully');
    }

    /**
     * Edit manual KYC record
     */
    public function manualEdit($id)
    {
        $kyc = KycRecord::findOrFail($id);
        return view('kyc.manual-edit', compact('kyc'));
    }

    /**
     * Update manual KYC record
     */
    public function manualUpdate(Request $request, $id)
    {
        $kyc = KycRecord::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'pan' => 'required|string|size:10|regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/',
            'name' => 'nullable|string|max:255',
            'status' => 'required|in:pending,verified,not_found',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $kyc->update([
            'pan' => strtoupper($request->pan),
            'name' => $request->name,
            'dob' => $request->dob,
            'status' => $request->status,
            'verified_at' => $request->status === 'verified' ? now() : ($kyc->verified_at ?? null),
            'notes' => $request->notes,
        ]);

        return redirect()->route('kyc.records')->with('success', 'KYC record updated successfully');
    }

    /**
     * Show documents for a KYC record
     */
    public function manualDocuments($id)
    {
        $kyc = KycRecord::findOrFail($id);
        
        // Get all files in the PAN folder
        $files = [];
        $folderPath = 'kyc_records/' . $kyc->pan;
        
        if (Storage::exists($folderPath)) {
            $allFiles = Storage::allFiles($folderPath);
            foreach ($allFiles as $file) {
                $files[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'size' => Storage::size($file),
                    'url' => Storage::url($file),
                ];
            }
        }
        
        return view('kyc.manual-documents', compact('kyc', 'files'));
    }

    /**
     * Upload document for KYC record
     */
    public function manualUploadDocument(Request $request, $id)
    {
        $kyc = KycRecord::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'document_type' => 'nullable|string|in:kyc_pdf,photo,signature,other',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $file = $request->file('document');
        $docType = $request->get('document_type', 'other');
        $filename = $docType . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        $path = $file->storeAs('kyc_records/' . $kyc->pan, $filename);
        
        // Update document path if first upload
        if (!$kyc->document_path) {
            $kyc->document_path = $path;
            $kyc->save();
        }

        return redirect()->route('kyc.manual.documents', $kyc->id)->with('success', 'Document uploaded successfully');
    }

    /**
     * Download a specific document
     */
    public function downloadRecordDocument($id, $filename)
    {
        $kyc = KycRecord::findOrFail($id);
        $path = 'kyc_records/' . $kyc->pan . '/' . $filename;
        
        if (!Storage::exists($path)) {
            abort(404, 'Document not found');
        }
        
        return Storage::download($path, $filename);
    }

    /**
     * Delete a document
     */
    public function deleteDocument($id, $filename)
    {
        $kyc = KycRecord::findOrFail($id);
        $path = 'kyc_records/' . $kyc->pan . '/' . $filename;
        
        if (Storage::exists($path)) {
            Storage::delete($path);
            return redirect()->route('kyc.manual.documents', $kyc->id)->with('success', 'Document deleted');
        }
        
        return redirect()->route('kyc.manual.documents', $kyc->id)->with('error', 'Document not found');
    }

    /**
     * Delete a KYC record
     */
    public function deleteRecord($id)
    {
        $kyc = KycRecord::findOrFail($id);
        $pan = $kyc->pan;
        
        // Delete all files in the PAN folder
        Storage::deleteDirectory('kyc_records/' . $pan);
        
        // Delete record
        $kyc->delete();
        
        return redirect()->route('kyc.records')->with('success', 'KYC record deleted');
    }
}

@extends('layouts.app')

@section('title', 'KYC API Documentation')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">KYC API Documentation</h1>
            <p class="mt-2 text-gray-600">CVL KRA API Integration Guide</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Introduction -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Introduction</h2>
                <p class="text-gray-600">
                    This API provides integration with CVL KRA (Central KYC Registry) for PAN verification and KYC data retrieval.
                    The API uses token-based authentication and supports both UAT and LIVE environments.
                </p>
            </div>

            <!-- Endpoints -->
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">API Endpoints</h2>

                <!-- Verify KYC -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">POST /kyc/verify</h3>
                    <p class="text-gray-600 mb-3">Complete KYC verification with PAN and DOB</p>
                    
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-green-400 text-sm"><code>// Request Body
{
    "pan": "ABCDE1234F",
    "dob": "15/08/1990"
}

// Response (Success)
{
    "success": true,
    "data": {
        "pan": "ABCDE1234F",
        "dob": "15/08/1990",
        "kyc_status": "Verified",
        "pan_status": {...},
        "kyc_details": {
            "name": "JOHN DOE",
            "father_name": "JANE DOE",
            "dob": "15/08/1990",
            "pan": "ABCDE1234F",
            "address": "123 MAIN STREET",
            "pincode": "110001",
            "state": "DELHI",
            "city": "NEW DELHI"
        },
        "documents": {
            "success": true,
            "path": "kyc_docs/ABCDE1234F/documents_20240115.zip",
            "file_name": "documents_20240115.zip"
        },
        "environment": "LIVE",
        "verified_at": "2024-01-15T10:30:00+05:30"
    }
}</code></pre>
                    </div>
                </div>

                <!-- Get Status -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">GET /kyc/status</h3>
                    <p class="text-gray-600 mb-3">Check PAN status only</p>
                    
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-green-400 text-sm"><code>// Query Parameters
?pan=ABCDE1234F&dob=15/08/1990

// Response
{
    "success": true,
    "data": {
        "status": "V",
        "kyc_status": "Verified",
        "last_updated": "2024-01-15"
    }
}</code></pre>
                    </div>
                </div>

                <!-- Get Details -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">GET /kyc/details</h3>
                    <p class="text-gray-600 mb-3">Get complete KYC details</p>
                    
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-green-400 text-sm"><code>// Query Parameters
?pan=ABCDE1234F&dob=15/08/1990

// Response
{
    "success": true,
    "data": {
        "name": "JOHN DOE",
        "father_name": "JANE DOE",
        "dob": "15/08/1990",
        "pan": "ABCDE1234F",
        "aadhaar": "XXXX-XXXX-1234",
        "address": "123 MAIN STREET",
        "pincode": "110001",
        "state": "DELHI",
        "city": "NEW DELHI",
        "mobile": "XXXXXXXX12",
        "email": "john@example.com"
    }
}</code></pre>
                    </div>
                </div>

                <!-- Download Documents -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">GET /kyc/documents/download</h3>
                    <p class="text-gray-600 mb-3">Download KYC documents as ZIP file</p>
                    
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-green-400 text-sm"><code>// Query Parameters
?pan=ABCDE1234F&dob=15/08/1990

// Response: Downloads ZIP file
// File: KYC_ABCDE1234F_20240115.zip</code></pre>
                    </div>
                </div>

                <!-- Environment Check -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">GET /kyc/environment</h3>
                    <p class="text-gray-600 mb-3">Check current API environment</p>
                    
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-green-400 text-sm"><code>// Response
{
    "environment": "LIVE",
    "is_production": false,
    "api_base_url": "https://krapancheck.cvlindia.com/V3/api"
}</code></pre>
                    </div>
                </div>
            </div>

            <!-- Status Codes -->
            <div class="p-6 border-t border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Response Status Codes</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">V</td>
                                <td class="px-4 py-2 text-sm text-green-600 font-medium">Verified</td>
                                <td class="px-4 py-2 text-sm text-gray-600">KYC is verified</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">P</td>
                                <td class="px-4 py-2 text-sm text-yellow-600 font-medium">Pending</td>
                                <td class="px-4 py-2 text-sm text-gray-600">KYC verification pending</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">D</td>
                                <td class="px-4 py-2 text-sm text-red-600 font-medium">Document Mismatch</td>
                                <td class="px-4 py-2 text-sm text-gray-600">Document details don't match</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">N</td>
                                <td class="px-4 py-2 text-sm text-gray-600">Not Found</td>
                                <td class="px-4 py-2 text-sm text-gray-600">KYC not found in registry</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Error Handling -->
            <div class="p-6 border-t border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Error Handling</h2>
                <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-red-400 text-sm"><code>// Error Response
{
    "success": false,
    "error": "Error message description",
    "errors": {
        "pan": ["The PAN must be 10 characters."],
        "dob": ["The DOB field is required."]
    }
}

// HTTP Status Codes
400 - Bad Request / Validation Error
404 - Not Found
422 - Validation Failed
500 - Server Error</code></pre>
                </div>
            </div>
        </div>

        <!-- Back Link -->
        <div class="mt-8 text-center">
            <a href="{{ route('kyc.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Back to KYC Verification
            </a>
        </div>
    </div>
</div>
@endsection

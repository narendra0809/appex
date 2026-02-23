@extends('layouts.app')

@section('title', 'KYC Verification - CVL KRA')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">CVL KRA KYC Verification</h1>
            <p class="mt-2 text-gray-600">Verify PAN card details with Central KYC Registry</p>
            
            <!-- Environment Badge -->
            @if($envInfo['is_production'])
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mt-4">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    LIVE Environment
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 mt-4">
                    <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                    UAT Environment
                </span>
            @endif
        </div>

        <!-- KYC Form Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <form id="kyc-form" method="POST" action="{{ route('kyc.verify') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- PAN Number -->
                        <div>
                            <label for="pan" class="block text-sm font-medium text-gray-700 mb-2">
                                PAN Number *
                            </label>
                            <input 
                                type="text" 
                                id="pan" 
                                name="pan" 
                                placeholder="ABCDE1234F"
                                maxlength="10"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase"
                                required
                            >
                            <p class="mt-1 text-xs text-gray-500">Format: 5 letters + 4 digits + 1 letter</p>
                            @error('pan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label for="dob" class="block text-sm font-medium text-gray-700 mb-2">
                                Date of Birth *
                            </label>
                            <input 
                                type="text" 
                                id="dob" 
                                name="dob" 
                                placeholder="DD/MM/YYYY"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required
                            >
                            <p class="mt-1 text-xs text-gray-500">Format: DD/MM/YYYY (e.g., 15/08/1990)</p>
                            @error('dob')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button 
                            type="submit" 
                            id="submit-btn"
                            class="w-full md:w-auto px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span id="btn-text">Verify KYC</span>
                            <span id="btn-loader" class="hidden ml-2">
                                <svg class="animate-spin h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Section -->
        <div id="results-section" class="hidden mt-8">
            <!-- Success Result -->
            <div id="result-success" class="hidden">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-xl font-bold text-gray-900">KYC Verification Successful</h2>
                                <p class="text-sm text-gray-500">PAN: <span id="result-pan" class="font-mono"></span></p>
                            </div>
                        </div>

                        <!-- KYC Status -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">KYC Status</h3>
                            <span id="kyc-status-badge" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Verified
                            </span>
                        </div>

                        <!-- KYC Details -->
                        <div id="kyc-details" class="mb-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">KYC Details</h3>
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <tbody id="kyc-details-table" class="divide-y divide-gray-200">
                                        <!-- Populated via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Download Documents -->
                        <div id="documents-section" class="mt-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Documents</h3>
                            <div id="documents-list" class="flex flex-wrap gap-3">
                                <!-- Populated via JavaScript -->
                            </div>
                            <div id="no-documents-message" class="text-sm text-gray-500 hidden">
                                No documents available for this KYC status
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Result -->
            <div id="result-error" class="hidden">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </span>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-xl font-bold text-gray-900">KYC Verification Failed</h2>
                                <p id="result-error-message" class="text-sm text-red-600 mt-1"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Documentation Link -->
        <div class="mt-8 text-center">
            <a href="{{ route('kyc.docs') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                View API Documentation →
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kyc-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoader = document.getElementById('btn-loader');
    const resultsSection = document.getElementById('results-section');
    const resultSuccess = document.getElementById('result-success');
    const resultError = document.getElementById('result-error');

    // PAN validation regex (case insensitive)
    const panRegex = /^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/;

    // Format PAN input
    document.getElementById('pan').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });

    // DOB date picker initialization
    const dobInput = document.getElementById('dob');
    dobInput.addEventListener('blur', function(e) {
        let value = this.value.replace(/[^0-9\/]/g, '');
        if (value.length === 8 && !value.includes('/')) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4) + '/' + value.substring(4);
        }
        this.value = value;
    });

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const pan = document.getElementById('pan').value.toUpperCase();
        const dob = document.getElementById('dob').value;

        // Validation
        if (!panRegex.test(pan)) {
            alert('Please enter a valid PAN number (e.g., ABCDE1234F)');
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        btnText.textContent = 'Verifying...';
        btnLoader.classList.remove('hidden');

        try {
            const response = await fetch('{{ route("kyc.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ pan, dob })
            });

            const data = await response.json();

            if (data.success) {
                // Show success results
                resultsSection.classList.remove('hidden');
                resultSuccess.classList.remove('hidden');
                resultError.classList.add('hidden');

                document.getElementById('result-pan').textContent = data.data.pan;
                
                // Update status badge
                const statusBadge = document.getElementById('kyc-status-badge');
                statusBadge.textContent = data.data.kyc_status || 'Verified';
                
                if (data.data.kyc_status === 'Verified') {
                    statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
                } else {
                    statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800';
                }

                // Populate documents
                const documentsList = document.getElementById('documents-list');
                const noDocsMessage = document.getElementById('no-documents-message');
                
                if (data.data.documents && data.data.documents.success && data.data.documents.documents) {
                    let docsHtml = '';
                    data.data.documents.documents.forEach(doc => {
                        const ext = doc.extension || 'bin';
                        const iconColor = {
                            'pdf': 'text-red-600',
                            'jpg': 'text-orange-500',
                            'png': 'text-blue-500',
                            'zip': 'text-gray-600'
                        }[ext] || 'text-gray-600';
                        
                        docsHtml += `
                            <a href="/storage/${doc.path}" 
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200">
                                <svg class="h-5 w-5 mr-2 ${iconColor}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                                ${doc.filename}
                            </a>
                        `;
                    });
                    documentsList.innerHTML = docsHtml;
                    documentsList.classList.remove('hidden');
                    noDocsMessage.classList.add('hidden');
                } else {
                    documentsList.innerHTML = '';
                    documentsList.classList.add('hidden');
                    noDocsMessage.classList.remove('hidden');
                }

                // Populate KYC details table
                const detailsTable = document.getElementById('kyc-details-table');
                if (data.data.kyc_details) {
                    const details = data.data.kyc_details;
                    let rows = '';
                    
                    const fieldLabels = {
                        name: 'Full Name',
                        father_name: 'Father\'s Name',
                        dob: 'Date of Birth',
                        pan: 'PAN Number',
                        aadhaar: 'Aadhaar Number',
                        address: 'Address',
                        pincode: 'Pincode',
                        state: 'State',
                        city: 'City',
                        mobile: 'Mobile',
                        email: 'Email'
                    };

                    for (const [key, value] of Object.entries(details)) {
                        if (fieldLabels[key] && value) {
                            rows += `
                                <tr class="bg-white">
                                    <td class="px-4 py-2 text-sm font-medium text-gray-500 w-1/3">${fieldLabels[key]}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">${value}</td>
                                </tr>
                            `;
                        }
                    }
                    detailsTable.innerHTML = rows;
                }

            } else {
                // Show error results
                resultsSection.classList.remove('hidden');
                resultSuccess.classList.add('hidden');
                resultError.classList.remove('hidden');
                document.getElementById('result-error-message').textContent = data.error || 'Verification failed';
            }

        } catch (error) {
            console.error('Error:', error);
            resultsSection.classList.remove('hidden');
            resultSuccess.classList.add('hidden');
            resultError.classList.remove('hidden');
            document.getElementById('result-error-message').textContent = 'An error occurred. Please try again.';
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            btnText.textContent = 'Verify KYC';
            btnLoader.classList.add('hidden');
        }
    });
});
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'KYC Check - CVL KRA')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">CVL KRA KYC Check</h1>
            <p class="mt-2 text-gray-600">Enter PAN details to fetch KYC information</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Enter Details</h2>
                        
                        <form id="kyc-check-form" method="POST" action="{{ route('kyc.check.store') }}">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="pan" class="block text-sm font-medium text-gray-700 mb-2">
                                    PAN Number *
                                </label>
                                <input 
                                    type="text" 
                                    id="pan" 
                                    name="pan" 
                                    placeholder="ABCDE1234F"
                                    maxlength="10"
                                    value="{{ old('pan', '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase"
                                    required
                                >
                                @error('pan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="dob" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date of Birth *
                                </label>
                                <input 
                                    type="text" 
                                    id="dob" 
                                    name="dob" 
                                    placeholder="DD-MM-YYYY"
                                    value="{{ old('dob', '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required
                                >
                                @error('dob')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button 
                                type="submit" 
                                id="submit-btn"
                                class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                            >
                                <span id="btn-text">Check API</span>
                                <span id="btn-loader" class="hidden ml-2 inline-block">
                                    <svg class="animate-spin h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Recent Checks -->
                @if(isset($recentRecords) && $recentRecords->count() > 0)
                <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b">
                        <h3 class="text-sm font-medium text-gray-700">Recent Checks</h3>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @foreach($recentRecords as $record)
                        <li>
                            <a href="{{ route('kyc.check.show', $record->pan) }}" class="block px-4 py-3 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $record->pan }}</p>
                                        <p class="text-xs text-gray-500">{{ $record->created_at->format('d M Y, h:i A') }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                        {{ $record->status === 'VERIFIED' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $record->status }}
                                    </span>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>

            <!-- Results Section -->
            <div class="lg:col-span-2">
                @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if(isset($result) && $result)
                <!-- Success Result -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-blue-600">
                        <h2 class="text-lg font-semibold text-white">KYC Details</h2>
                    </div>
                    
                    <div class="p-6">
                        <!-- Status Badge -->
                        <div class="flex items-center mb-6">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                {{ $result['kyc_status'] === 'VERIFIED' || $result['kyc_status'] === '01' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                Status: {{ $result['kyc_status'] ?? 'Unknown' }}
                            </span>
                            <span class="ml-4 text-sm text-gray-500">
                                PAN: <span class="font-mono font-medium">{{ $result['pan'] ?? request('pan') }}</span>
                            </span>
                        </div>

                        <!-- KYC Data -->
                        @if(isset($result['data']) && is_array($result['data']))
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Personal Details</h3>
                            <div class="bg-gray-50 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($result['data'] as $key => $value)
                                        @if(is_string($value) && strlen($value) < 200)
                                        <tr>
                                            <td class="px-4 py-2 text-sm font-medium text-gray-500 w-1/3">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $value }}</td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Documents -->
                        @if(isset($result['documents']['success']) && $result['documents']['success'] && isset($result['documents']['documents']))
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Documents ({{ count($result['documents']['documents']) }})</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($result['documents']['documents'] as $doc)
                                <div class="bg-gray-50 rounded-lg p-4 text-center border">
                                    <div class="text-4xl mb-2">
                                        @if($doc['extension'] === 'pdf')
                                        📄
                                        @elseif(in_array($doc['extension'], ['jpg', 'jpeg', 'png']))
                                        🖼️
                                        @else
                                        📎
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-600 truncate">{{ $doc['filename'] }}</p>
                                    <a href="{{ route('kyc.document.download', ['filename' => basename($doc['path'])]) }}" 
                                       class="mt-2 inline-block text-xs text-blue-600 hover:text-blue-800">
                                        Download
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        @if(isset($result['documents']['message']))
                        <div class="text-sm text-gray-500">
                            <p>{{ $result['documents']['message'] }}</p>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
                @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No KYC Data Yet</h3>
                    <p class="text-gray-500">Enter PAN details and click "Check API" to fetch KYC information.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kyc-check-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoader = document.getElementById('btn-loader');

    // Format PAN input
    document.getElementById('pan').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });

    // DOB formatting
    document.getElementById('dob').addEventListener('blur', function(e) {
        let value = this.value.replace(/[^0-9-]/g, '');
        if (value.length === 8 && !value.includes('-')) {
            value = value.substring(0, 2) + '-' + value.substring(2, 4) + '-' + value.substring(4);
        }
        this.value = value;
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        const pan = document.getElementById('pan').value;
        const dob = document.getElementById('dob').value;
        
        // Basic validation
        if (pan.length !== 10 || !/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/.test(pan)) {
            alert('Please enter a valid PAN number');
            e.preventDefault();
            return;
        }
        
        if (!dob.match(/^\\d{2}-\\d{2}-\\d{4}$/)) {
            alert('Please enter DOB in DD-MM-YYYY format');
            e.preventDefault();
            return;
        }

        submitBtn.disabled = true;
        btnText.textContent = 'Checking...';
        btnLoader.classList.remove('hidden');
    });
});
</script>
@endpush
@endsection

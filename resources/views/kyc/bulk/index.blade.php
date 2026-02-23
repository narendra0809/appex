@extends('layouts.app')

@section('title', 'Bulk KYC Verification')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                    <i class="fas fa-layer-group text-indigo-600 mr-3"></i>
                    Bulk KYC Verification
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Upload Excel file with PAN numbers and DOBs to verify KYC in bulk
                </p>
            </div>
            <a href="{{ route('kyc.records') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to KYC Records
            </a>
        </div>
    </div>

    <!-- Upload Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">
                <i class="fas fa-upload mr-2"></i>
                Upload Excel File
            </h2>
        </div>
        
        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span class="text-green-700 dark:text-green-300">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <span class="text-red-700 dark:text-red-300">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ route('kyc.bulk.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Batch Name -->
                    <div>
                        <label for="batch_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Batch Name (Optional)
                        </label>
                        <input type="text" 
                            name="batch_name" 
                            id="batch_name"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200 transition-all duration-200"
                            placeholder="e.g., January 2024 Batch">
                    </div>

                    <!-- Excel File -->
                    <div>
                        <label for="excel_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Excel File <span class="text-red-500">*</span>
                        </label>
                        <input type="file" 
                            name="excel_file" 
                            id="excel_file"
                            accept=".xlsx,.xls,.csv"
                            required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200">
                    </div>
                </div>

                <!-- File Format Info -->
                <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        Excel File Format
                    </h3>
                    <p class="text-sm text-blue-700 dark:text-blue-400 mb-2">
                        Your Excel file should contain the following columns:
                    </p>
                    <ul class="text-sm text-blue-600 dark:text-blue-400 list-disc list-inside space-y-1">
                        <li><strong>pan_number</strong> or <strong>pan</strong> - 10 character PAN number</li>
                        <li><strong>date_of_birth</strong> or <strong>dob</strong> - Date of birth (DD-MM-YYYY format preferred)</li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:bg-indigo-700 transition-all duration-200">
                        <i class="fas fa-upload mr-2"></i>
                        Upload & Preview
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Previous Batches -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">
                <i class="fas fa-history mr-2"></i>
                Previous Batches
            </h2>
        </div>
        
        <div class="p-6">
            @if($batches->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400">Batch Name</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400">File Name</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400">Records</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400">Success/Failed</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400">Created</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($batches as $batch)
                                <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-4 px-4">
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $batch->batch_name }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                        {{ $batch->original_filename }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            {{ $batch->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                            {{ $batch->status === 'processing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                            {{ $batch->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                            {{ $batch->status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                            {{ $batch->formatted_status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                        {{ $batch->total_records }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="text-green-600 dark:text-green-400">{{ $batch->success_count }}</span>
                                        /
                                        <span class="text-red-600 dark:text-red-400">{{ $batch->failed_count }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                        {{ $batch->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('kyc.bulk.show', $batch->id) }}" 
                                                class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors text-sm">
                                                <i class="fas fa-eye mr-1"></i>
                                                View
                                            </a>
                                            @if($batch->status === 'completed' && $batch->result_zip_path)
                                                <a href="{{ route('kyc.bulk.download', $batch->id) }}" 
                                                    class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm">
                                                    <i class="fas fa-download mr-1"></i>
                                                    ZIP
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $batches->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">No batches uploaded yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

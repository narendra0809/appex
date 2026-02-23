@extends('layouts.app')

@section('title', 'Bulk KYC Batch Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                    <i class="fas fa-layer-group text-indigo-600 mr-3"></i>
                    {{ $batch->batch_name }}
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Uploaded: {{ $batch->created_at->format('d M Y, H:i') }} | File: {{ $batch->original_filename }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('kyc.bulk.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to List
                </a>
                @if($batch->status === 'pending')
                    <form action="{{ route('kyc.bulk.process', $batch->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-play mr-2"></i>
                            Start Processing
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                <span class="text-green-700 dark:text-green-300">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Records -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Records</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $batch->total_records }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-list text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Processed -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Processed</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $batch->processed_records }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-spinner text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Success -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Success</p>
                    <p class="text-3xl font-bold text-green-600">{{ $batch->success_count }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Failed -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Failed</p>
                    <p class="text-3xl font-bold text-red-600">{{ $batch->failed_count }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-times text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    @if($batch->status === 'processing')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Processing Progress</span>
                <span class="text-sm font-medium text-indigo-600">{{ $batch->progress }}%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-4 rounded-full transition-all duration-500" style="width: {{ $batch->progress }}%"></div>
            </div>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Processing... Please wait or refresh the page to see updates.
            </p>
        </div>
    @endif

    <!-- Download Actions -->
    @if($batch->status === 'completed')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-download text-indigo-600 mr-2"></i>
                Download Options
            </h3>
            <div class="flex flex-wrap gap-4">
                @if($batch->result_zip_path)
                    <a href="{{ route('kyc.bulk.download', $batch->id) }}" 
                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl shadow-lg hover:bg-green-700 transition-all duration-200">
                        <i class="fas fa-file-archive mr-2"></i>
                        Download All Documents (ZIP)
                    </a>
                @endif
                @if($batch->failed_count > 0)
                    <a href="{{ route('kyc.bulk.error-report', $batch->id) }}" 
                        class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-xl shadow-lg hover:bg-red-700 transition-all duration-200">
                        <i class="fas fa-file-excel mr-2"></i>
                        Download Error Report (CSV)
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- Records Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">
                <i class="fas fa-list-alt mr-2"></i>
                Batch Records
            </h2>
        </div>
        
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400">#</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400">PAN Number</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400">DOB</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400">Error Message</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batch->records as $index => $record)
                            <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="font-mono font-medium text-gray-900 dark:text-gray-100">{{ $record->pan }}</span>
                                </td>
                                <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                    {{ $record->dob }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $record->status === 'success' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                        {{ $record->status === 'processing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                        {{ $record->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                        {{ $record->status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                        @if($record->status === 'success')
                                            <i class="fas fa-check-circle mr-1"></i>
                                        @elseif($record->status === 'failed')
                                            <i class="fas fa-times-circle mr-1"></i>
                                        @elseif($record->status === 'processing')
                                            <i class="fas fa-spinner fa-spin mr-1"></i>
                                        @else
                                            <i class="fas fa-clock mr-1"></i>
                                        @endif
                                        {{ $record->formatted_status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    @if($record->error_message)
                                        <span class="text-red-600 dark:text-red-400 text-sm">{{ $record->error_message }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($record->kycRecord)
                                        <a href="{{ route('kyc.record.show', $record->kycRecord->id) }}" 
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors text-sm">
                                            <i class="fas fa-eye mr-1"></i>
                                            View KYC
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Error Log -->
    @if($batch->error_log)
        <div class="mt-8 bg-red-50 dark:bg-red-900/30 rounded-xl border border-red-200 dark:border-red-800 p-6">
            <h3 class="text-lg font-semibold text-red-800 dark:text-red-300 mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error Log
            </h3>
            <pre class="text-sm text-red-700 dark:text-red-400 whitespace-pre-wrap bg-red-100 dark:bg-red-900/50 p-4 rounded-lg overflow-x-auto">{{ $batch->error_log }}</pre>
        </div>
    @endif
</div>

{{-- Auto-refresh for processing status --}}
@if($batch->status === 'processing')
    <script>
        setTimeout(function() {
            location.reload();
        }, 5000);
    </script>
@endif
@endsection

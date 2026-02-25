@extends('layouts.app')

@section('title', 'KYC Records')

@section('content')
<div class="p-4 md:p-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">KYC Records</h1>
                <p class="text-gray-600 mt-1">Manage KYC verification records</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('kyc.bulk.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Import Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Check KYC Form -->
    <div class="bg-white rounded-lg shadow p-4 md:p-6 mb-6">
        <form method="POST" action="{{ route('kyc.check.store') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div class="w-full sm:w-auto flex-1 min-w-[140px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">PAN</label>
                <input type="text" name="pan" required maxlength="10" placeholder="ABCDE1234F"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg uppercase font-mono text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    value="{{ old('pan') }}">
            </div>
            <div class="w-full sm:w-auto flex-1 min-w-[140px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">DOB</label>
                <input type="text" name="dob" required placeholder="DD-MM-YYYY"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    value="{{ old('dob') }}">
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                    Check
                </button>
                <a href="{{ route('kyc.export') }}" class="flex-1 sm:flex-none px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm text-center whitespace-nowrap">
                    Export
                </a>
            </div>
        </form>
        @error('pan')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
        @error('dob')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
        @error('error')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <p class="text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="w-full sm:w-auto flex-1 min-w-[120px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="PAN or Name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full sm:w-28 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="not_found" {{ request('status') == 'not_found' ? 'selected' : '' }}>Not Found</option>
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                <select name="per_page" class="w-full sm:w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Filter</button>
                <a href="{{ route('kyc.records') }}" class="flex-1 sm:flex-none px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm text-center">Clear</a>
            </div>
        </form>
    </div>

    <!-- KYC Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PAN</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Name</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DOB</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ZIP</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kycs as $kyc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-4 whitespace-nowrap font-mono font-bold text-gray-900 text-sm">{{ $kyc->pan }}</td>
                        <td class="px-3 py-4 whitespace-nowrap text-gray-700 text-sm hidden md:table-cell">{{ $kyc->name ?? '-' }}</td>
                        <td class="px-3 py-4 whitespace-nowrap text-gray-600 text-sm">{{ $kyc->dob }}</td>
                       
                        <td class="px-3 py-4 whitespace-nowrap">
                            @if($kyc->zip_path)
                                <a href="{{ route('kyc.download.zip', ['pan' => $kyc->pan]) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                    Download
                                </a>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-3 py-12 text-center text-gray-500 text-sm">
                            No KYC records yet
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($kycs->hasPages())
    <div class="mt-4">
        {{ $kycs->links() }}
    </div>
    @endif
</div>

@if(session('download_pan'))
<script>window.location.href = "{{ route('kyc.download.zip', ['pan' => session('download_pan')]) }}";</script>
@endif
@endsection

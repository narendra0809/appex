@extends('layouts.app')

@section('title', 'Documents - ' . $kyc->pan)

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('kyc.records') }}" class="text-blue-600 hover:text-blue-800">← Back to Records</a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $kyc->pan }}</h1>
                    <p class="text-gray-600">{{ $kyc->name ?? 'No name' }} • {{ $kyc->status }}</p>
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">📤 Upload Document</h2>
            <form action="{{ route('kyc.manual.upload', $kyc->id) }}" method="POST" enctype="multipart/form-data" class="flex gap-4 items-end">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Document</label>
                    <input type="file" name="document" required accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full border rounded-md px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="document_type" class="border rounded-md px-3 py-2">
                        <option value="kyc_pdf">KYC PDF</option>
                        <option value="photo">Photo</option>
                        <option value="signature">Signature</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Upload
                </button>
            </form>
            <p class="text-sm text-gray-500 mt-2">Accepted: PDF, JPG, PNG (Max 10MB)</p>
        </div>

        <!-- Documents Grid -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold">📁 Uploaded Documents</h2>
            </div>
            
            @if(count($files) > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($files as $file)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">
                                    @if(str_ends_with($file['name'], '.pdf'))📕
                                    @elseif(str_ends_with($file['name'], '.jpg') || str_ends_with($file['name'], '.jpeg'))🖼️
                                    @elseif(str_ends_with($file['name'], '.png'))🖼️
                                    @else📄
                                    @endif
                                </span>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $file['name'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $file['path'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $file['size'] }}</td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ $file['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="{{ route('kyc.record.document.download', [$kyc->id, $file['name']]) }}" class="text-green-600 hover:text-green-900 mr-3">Download</a>
                            <form action="{{ route('kyc.record.document.delete', [$kyc->id, $file['name']]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this file?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center">
                <div class="text-4xl mb-2">📭</div>
                <div class="text-gray-500 mb-4">No documents uploaded yet</div>
                <p class="text-sm text-gray-400">Upload KYC PDF, photo, signature, or other documents</p>
            </div>
            @endif
        </div>

        <!-- CVL Portal Link -->
        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">🔗 Quick Actions</h2>
            <div class="flex gap-4 flex-wrap">
                <a href="https://cvlkrayc.cvlindia.com/kyc/craSearch.jsp" target="_blank" 
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    🌐 Open CVL Portal
                </a>
                <a href="{{ route('kyc.record.show', $kyc->id) }}" 
                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    👁️ View Record Details
                </a>
                <a href="{{ route('kyc.records') }}" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                    📋 All Records
                </a>
            </div>
        </div>
    </div>
</div>

@extends('layouts.app')

@section('title', 'KYC Record - ' . $kyc->pan)

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('kyc.records') }}" class="text-blue-600 hover:text-blue-800">← Back</a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">KYC Record: {{ $kyc->pan }}</h1>
                    <p class="text-gray-600">Created: {{ $kyc->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('kyc.manual.edit', $kyc->id) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    Edit
                </a>
                <a href="{{ route('kyc.manual.documents', $kyc->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Documents
                </a>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        {{ $kyc->status === 'verified' ? 'bg-green-100 text-green-800' : 
                           ($kyc->status === 'not_found' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($kyc->status) }}
                    </span>
                    <div>
                        <div class="text-sm text-gray-500">Verified Date</div>
                        <div class="font-medium">{{ $kyc->verified_at ? $kyc->verified_at->format('d/m/Y H:i') : 'Not verified' }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Last Updated</div>
                    <div class="font-medium">{{ $kyc->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Personal Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Personal Information</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">PAN Number</dt>
                        <dd class="font-mono font-medium">{{ $kyc->pan }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Full Name</dt>
                        <dd class="font-medium">{{ $kyc->name ?? 'Not provided' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Date of Birth</dt>
                        <dd class="font-medium">{{ $kyc->dob }}</dd>
                    </div>
                    @if($kyc->father_name)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Father's Name</dt>
                        <dd class="font-medium">{{ $kyc->father_name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Address Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Address Details</h2>
                <dl class="space-y-3">
                    @if($kyc->address)
                    <div>
                        <dt class="text-gray-500 text-sm">Address</dt>
                        <dd class="font-medium">{{ $kyc->address }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-gray-500">City</dt>
                        <dd class="font-medium">{{ $kyc->city ?? 'Not provided' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">State</dt>
                        <dd class="font-medium">{{ $kyc->state ?? 'Not provided' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Pincode</dt>
                        <dd class="font-medium">{{ $kyc->pincode ?? 'Not provided' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Documents</h2>
            @php
            $files = [];
            $folderPath = 'kyc_records/' . $kyc->pan;
            if (Storage::exists($folderPath)) {
                $allFiles = Storage::allFiles($folderPath);
                foreach ($allFiles as $file) {
                    $files[] = [
                        'name' => basename($file),
                        'path' => $file,
                        'size' => number_format(Storage::size($file) / 1024, 2) . ' KB',
                        'url' => Storage::url($file),
                    ];
                }
            }
            @endphp
            
            @if(count($files) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($files as $file)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">📄</span>
                            <div>
                                <div class="font-medium">{{ $file['name'] }}</div>
                                <div class="text-sm text-gray-500">{{ $file['size'] }}</div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ $file['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                            <a href="{{ route('kyc.record.document.download', [$kyc->id, $file['name']]) }}" class="text-green-600 hover:text-green-800 text-sm">Download</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-2">📁</div>
                    <div>No documents uploaded</div>
                    <a href="{{ route('kyc.manual.documents', $kyc->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">Upload Documents →</a>
                </div>
            @endif
        </div>

        <!-- Notes -->
        @if($kyc->notes)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Notes</h2>
            <p class="text-gray-700">{{ $kyc->notes }}</p>
        </div>
        @endif

        <!-- API Response (if available) -->
        @if($kyc->api_raw_response || $kyc->kyc_json)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">API Response Data</h2>
            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-sm">{{ json_encode(json_decode($kyc->kyc_json ?? '{}'), JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endif

        <!-- Danger Zone -->
        <div class="mt-6 border border-red-200 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-red-600 mb-4">⚠️ Danger Zone</h2>
            <p class="text-gray-600 mb-4">Deleting this record will also delete all associated documents.</p>
            <form action="{{ route('kyc.record.delete', $kyc->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this KYC record? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Delete Record
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

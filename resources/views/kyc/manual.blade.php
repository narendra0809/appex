@extends('layouts.app')

@section('title', 'Manual KYC Tracker')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Manual KYC Tracker</h1>
                <p class="mt-2 text-gray-600">Track KYC status manually while API decryption is being set up</p>
            </div>
            <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                + Add New KYC
            </button>
        </div>

        <!-- KYC Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total KYCs</div>
                <div class="text-3xl font-bold text-gray-900">{{ count($kycs) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Verified</div>
                <div class="text-3xl font-bold text-green-600">{{ $kycs->where('status', 'verified')->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Pending</div>
                <div class="text-3xl font-bold text-yellow-600">{{ $kycs->where('status', 'pending')->count() }}</div>
            </div>
        </div>

        <!-- KYC Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PAN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">DOB</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verified Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kycs as $kyc)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-mono">{{ $kyc->pan }}</td>
                        <td class="px-6 py-4">{{ $kyc->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $kyc->dob }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $kyc->status === 'verified' ? 'bg-green-100 text-green-800' : 
                                   ($kyc->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($kyc->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $kyc->verified_at ? $kyc->verified_at->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ route('kyc.manual.edit', $kyc->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <a href="{{ route('kyc.manual.documents', $kyc->id) }}" class="text-indigo-600 hover:text-indigo-900">Documents</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No KYC records found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Quick Links -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">🔗 Quick Links</h2>
                <ul class="space-y-2">
                    <li>
                        <a href="https://cvlkrayc.cvlindia.com/kyc/craSearch.jsp" target="_blank" class="text-blue-600 hover:text-blue-800">
                            → CVL KRA Search Portal
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('kyc.index') }}" class="text-blue-600 hover:text-blue-800">
                            → CVL API KYC Form (Automated)
                        </a>
                    </li>
                </ul>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">📋 Instructions</h2>
                <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1">
                    <li>Go to CVL KRA Search Portal</li>
                    <li>Enter PAN and DOB manually</li>
                    <li>Download KYC PDF</li>
                    <li>Add record here with status</li>
                    <li>Upload documents if available</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Add New KYC</h2>
        <form action="{{ route('kyc.manual.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">PAN Number</label>
                <input type="text" name="pan" required class="w-full border rounded-md px-3 py-2 uppercase" placeholder="ABCDE1234F">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" class="w-full border rounded-md px-3 py-2" placeholder="John Doe">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="dob" required class="w-full border rounded-md px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border rounded-md px-3 py-2">
                    <option value="pending">Pending</option>
                    <option value="verified">Verified</option>
                    <option value="not_found">Not Found</option>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('modal').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('modal').classList.add('hidden');
}
</script>
@endsection

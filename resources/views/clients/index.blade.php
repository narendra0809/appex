@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Client Management</h1>
                <p class="text-gray-600 mt-1">Manage your clients and generate invoices</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="toggleExcelModal()" class="px-3 py-2 md:px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center justify-center text-sm md:text-base">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span class="hidden sm:inline">Import</span>
                    <span class="sm:hidden">Import</span>
                </button>
                <a href="{{ route('clients.export') }}" class="px-3 py-2 md:px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center text-sm md:text-base">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span class="hidden sm:inline">Export</span>
                    <span class="sm:hidden">Export</span>
                </a>
                <a href="{{ route('clients.create') }}" class="px-3 py-2 md:px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center text-sm md:text-base">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">Add New</span>
                    <span class="sm:hidden">Add</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Filters -->
    <div class="mb-4 bg-white rounded-lg p-4 shadow">
        <form method="GET" action="{{ route('clients.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="w-full sm:w-auto">
                <label class="block text-xs md:text-sm text-gray-600 mb-1">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, email or mobile" 
                    class="w-full sm:w-32 md:w-40 px-2 py-1.5 md:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-xs md:text-sm text-gray-600 mb-1">City</label>
                <input type="text" name="city" value="{{ request('city') }}" placeholder="City" 
                    class="w-full sm:w-24 md:w-32 px-2 py-1.5 md:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-xs md:text-sm text-gray-600 mb-1">Plan</label>
                <input type="text" name="plan" value="{{ request('plan') }}" placeholder="Plan" 
                    class="w-full sm:w-24 md:w-32 px-2 py-1.5 md:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-xs md:text-sm text-gray-600 mb-1">Per page</label>
                <select name="per_page" class="w-full sm:w-20 px-2 py-1.5 md:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-xs md:text-sm text-gray-600 mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                    class="w-full sm:w-28 px-2 py-1.5 md:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-xs md:text-sm text-gray-600 mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                    class="w-full sm:w-28 px-2 py-1.5 md:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none px-4 py-1.5 md:py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                    Filter
                </button>
                <a href="{{ route('clients.index') }}" class="flex-1 sm:flex-none px-4 py-1.5 md:py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm text-center">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Clients Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        @php
                            $currentSort = request('sort_by');
                            $currentDir = request('sort_dir', 'desc');
                        @endphp
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('clients.index', array_merge(request()->except('page'), ['sort_by' => 'client_name', 'sort_dir' => ($currentSort=='client_name' && $currentDir=='asc') ? 'desc' : 'asc'])) }}">Client Name</a>
                        </th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('clients.index', array_merge(request()->except('page'), ['sort_by' => 'city', 'sort_dir' => ($currentSort=='city' && $currentDir=='asc') ? 'desc' : 'asc'])) }}">City</a>
                        </th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('clients.index', array_merge(request()->except('page'), ['sort_by' => 'plan', 'sort_dir' => ($currentSort=='plan' && $currentDir=='asc') ? 'desc' : 'asc'])) }}">Plan</a>
                        </th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('clients.index', array_merge(request()->except('page'), ['sort_by' => 'gross_amount', 'sort_dir' => ($currentSort=='gross_amount' && $currentDir=='asc') ? 'desc' : 'asc'])) }}">Amount</a>
                        </th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('clients.index', array_merge(request()->except('page'), ['sort_by' => 'payment_date', 'sort_dir' => ($currentSort=='payment_date' && $currentDir=='asc') ? 'desc' : 'asc'])) }}">Payment Date</a>
                        </th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Send Date Agreement</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Send Date Invoice</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($clients as $client)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $client->client_name }}</div>
                            @if($client->email)
                            <div class="text-sm text-gray-500">{{ $client->email }}</div>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->city ?? 'N/A' }}</td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $client->plan ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ₹{{ number_format($client->gross_amount ?? 0, 2) }}
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $client->payment_date ? \Carbon\Carbon::parse($client->payment_date)->format('d M, Y') : 'N/A' }}
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap">
                            @if($client->agreement && $client->agreement->agreement_sent_at)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Sent {{ $client->agreement->agreement_sent_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap">
                            @if($client->invoice && $client->invoice->sent_at)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Sent {{ $client->invoice->sent_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-1 md:gap-2">
                                <a href="{{ route('clients.edit', $client->id) }}" class="text-blue-600 hover:text-blue-900 p-1" title="Edit">
                                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <a href="/test/{{ $client->id }}" target="_blank" class="text-blue-600 hover:text-blue-900 p-1" title="Preview Invoice">
                                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('invoice.word', $client->id) }}" class="text-green-600 hover:text-green-900 p-1" title="Download PDF">
                                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </a>
                                <button onclick="openEmailModal({{ $client->id }}, '{{ $client->amount_type }}')" class="text-purple-600 hover:text-purple-800 p-1" title="Send Email">
                                    <svg class="w-4 h-4 md:w-5 md:h-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this client?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 p-1" title="Delete">
                                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No clients</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new client or importing from Excel.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 bg-white border-t">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-sm text-gray-700 text-center sm:text-left">
                    Showing <strong>{{ $clients->firstItem() ?? 0 }}</strong> to <strong>{{ $clients->lastItem() ?? 0 }}</strong> of <strong>{{ $clients->total() }}</strong> clients
                </div>
                <div>
                    {{ $clients->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Excel Import Modal -->
<div id="excelModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Import Excel File</h3>
                <button onclick="toggleExcelModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('clients.import') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Excel File</label>
                    <input type="file" name="file" accept=".xlsx,.xls" required 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">Supported: .xlsx, .xls</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleExcelModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div id="sendEmailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Send Document</h3>
                <button onclick="closeEmailModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <p id="emailClientInfo" class="text-sm text-gray-700 mb-3"></p>
            <div class="flex flex-col gap-3">
                <a id="sendInvoiceLink" href="#" class="px-4 py-3 bg-blue-600 text-white rounded-lg text-center hover:bg-blue-700 transition">Send Invoice</a>
                <a id="sendAgreementLink" href="#" class="px-4 py-3 bg-green-600 text-white rounded-lg text-center hover:bg-green-700 transition hidden">Send Agreement</a>
                <button onclick="closeEmailModal()" class="px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleExcelModal() {
    document.getElementById('excelModal').classList.toggle('hidden');
}

function openEmailModal(clientId, amountType) {
    document.getElementById('sendEmailModal').classList.remove('hidden');
    const invoiceUrl = '{{ url('/invoice/email') }}' + '/' + clientId;
    const agreementUrl = '{{ url('/agreement/email') }}' + '/' + clientId;
    document.getElementById('sendInvoiceLink').setAttribute('href', invoiceUrl);
    if (amountType === 'New Enrollment') {
        document.getElementById('sendAgreementLink').classList.remove('hidden');
        document.getElementById('sendAgreementLink').setAttribute('href', agreementUrl);
    } else {
        document.getElementById('sendAgreementLink').classList.add('hidden');
    }
    document.getElementById('emailClientInfo').textContent = 'Send documents to client via email.';
}

function closeEmailModal() {
    document.getElementById('sendEmailModal').classList.add('hidden');
}
</script>
@endsection

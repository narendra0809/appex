@extends('layouts.app')

@section('content')
        <div class="p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            {{ $client ? 'Edit Client' : 'Add New Client' }}
                        </h1>
                        <p class="text-gray-600 mt-1">Fill in all the client details below</p>
                    </div>
                    <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ $client ? route('clients.update', $client->id) : route('clients.store') }}" method="POST" class="space-y-6">
                @csrf
                @if($client)
                    @method('PUT')
                @endif

                <div class="bg-white rounded-lg shadow p-6">
                    <!-- Personal Information -->
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 pb-2 border-b">Personal Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Date <span class="text-red-500">*</span></label>
                            <input type="date" name="payment_date" value="{{ old('payment_date', $client->payment_date ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('payment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Client Name <span class="text-red-500">*</span></label>
                            <input type="text" name="client_name" value="{{ old('client_name', $client->client_name ?? '') }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('client_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                            <input type="text" name="mobile" value="{{ old('mobile', $client->mobile ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('mobile')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $client->email ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Father Name</label>
                            <input type="text" name="father_name" value="{{ old('father_name', $client->father_name ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('father_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                            <input type="date" name="dob" value="{{ old('dob', $client->dob ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('dob')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <!-- Identity Documents -->
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 pb-2 border-b">Identity Documents</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">PAN Card</label>
                            <input type="text" name="pan_card" value="{{ old('pan_card', $client->pan_card ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('pan_card')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Aadhaar Card</label>
                            <input type="text" name="aadhaar_card" value="{{ old('aadhaar_card', $client->aadhaar_card ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('aadhaar_card')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <!-- Address Information -->
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 pb-2 border-b">Address Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <input type="text" name="city" value="{{ old('city', $client->city ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                            <input type="text" name="state" value="{{ old('state', $client->state ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <!-- Service Information -->
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 pb-2 border-b">Service Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Segment / Service</label>
                            <select name="segment" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Segment</option>
                                <option value="Index Options" {{ old('segment', $client->segment ?? '') == 'Index Options' ? 'selected' : '' }}>Index Options</option>
                                <option value="Commodity" {{ old('segment', $client->segment ?? '') == 'Commodity' ? 'selected' : '' }}>Commodity</option>
                                <option value="Systematic Trading Plan - STP" {{ old('segment', $client->segment ?? '') == 'Systematic Trading Plan - STP' ? 'selected' : '' }}>Systematic Trading Plan - STP</option>
                                <option value="Cash/Equity" {{ old('segment', $client->segment ?? '') == 'Cash/Equity' ? 'selected' : '' }}>Cash/Equity</option>
                            </select>
                            @error('segment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Plan</label>
                            <select name="plan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Plan</option>
                                <option value="Basic" {{ old('plan', $client->plan ?? '') == 'Basic' ? 'selected' : '' }}>Basic</option>
                                <option value="STP" {{ old('plan', $client->plan ?? '') == 'STP' ? 'selected' : '' }}>STP</option>
                            </select>
                            @error('plan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                            <input type="text" name="assigned_to" value="{{ old('assigned_to', $client->assigned_to ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('assigned_to')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount Type</label>
                            <select name="amount_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Type</option>
                                <option value="New Enrollment" {{ old('amount_type', $client->amount_type ?? '') == 'New Enrollment' ? 'selected' : '' }}>New Enrollment</option>
                                <option value="Remaining" {{ old('amount_type', $client->amount_type ?? '') == 'Remaining' ? 'selected' : '' }}>Remaining</option>
                                <option value="Upgradation" {{ old('amount_type', $client->amount_type ?? '') == 'Upgradation' ? 'selected' : '' }}>Upgradation</option>
                            </select>
                            @error('amount_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Start Date</label>
                            <input type="date" name="service_start" value="{{ old('service_start', $client->service_start ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('service_start')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service End Date</label>
                            <input type="date" name="service_end" value="{{ old('service_end', $client->service_end ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('service_end')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- service field removed; using `segment` as single source of truth -->

                        <!-- New: Bank Dropdown -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bank</label>
                            <select name="bank" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Bank</option>
                                <option value="IndusInd" {{ old('bank', $client->bank ?? '') == 'IndusInd' ? 'selected' : '' }}>IndusInd</option>
                                <option value="AU Bank" {{ old('bank', $client->bank ?? '') == 'AU Bank' ? 'selected' : '' }}>AU Bank</option>
                                <option value="BOB" {{ old('bank', $client->bank ?? '') == 'BOB' ? 'selected' : '' }}>BOB</option>
                                <option value="Canara" {{ old('bank', $client->bank ?? '') == 'Canara' ? 'selected' : '' }}>Canara</option>
                                <option value="YES" {{ old('bank', $client->bank ?? '') == 'YES' ? 'selected' : '' }}>YES</option>
                                <option value="HDFC" {{ old('bank', $client->bank ?? '') == 'HDFC' ? 'selected' : '' }}>HDFC</option>
                                <option value="Indian Bank" {{ old('bank', $client->bank ?? '') == 'Indian Bank' ? 'selected' : '' }}>Indian Bank</option>
                            </select>
                            @error('bank')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <!-- Financial Information -->
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 pb-2 border-b">Financial Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Net Amount</label>
                            <input id="net_amount" type="number" step="0.01" name="net_amount" value="{{ old('net_amount', $client->net_amount ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('net_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gross Amount</label>
                            <input id="gross_amount" type="number" step="0.01" name="gross_amount" value="{{ old('gross_amount', $client->gross_amount ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('gross_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Remark</label>
                        <textarea name="remark" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('remark', $client->remark ?? '') }}</textarea>
                        @error('remark')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4 pt-6">
                    <a href="{{ route('clients.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $client ? 'Update Client' : 'Create Client' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const netInput = document.getElementById('net_amount');
        const grossInput = document.getElementById('gross_amount');
        if (!netInput || !grossInput) return;

        function updateGross() {
            const n = parseFloat(netInput.value);
            if (Number.isNaN(n)) {
                grossInput.value = '';
                return;
            }
            const gross = n * 1.18; // add 18% GST
            grossInput.value = gross.toFixed(2);
        }

        netInput.addEventListener('input', updateGross);

        // initialize on page load if net has value
        if (netInput.value) {
            updateGross();
        }
    });
</script>

@endsection

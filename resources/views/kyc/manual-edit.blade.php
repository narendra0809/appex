@extends('layouts.app')

@section('title', 'Edit KYC Record')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold mb-6">Edit KYC Record</h1>
            
            <form action="{{ route('kyc.manual.update', $kyc->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">PAN Number *</label>
                    <input type="text" name="pan" value="{{ old('pan', $kyc->pan) }}" required 
                        class="w-full border rounded-md px-3 py-2 uppercase" placeholder="ABCDE1234F">
                    @error('pan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $kyc->name) }}" 
                        class="w-full border rounded-md px-3 py-2" placeholder="John Doe">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth *</label>
                    <input type="text" name="dob" value="{{ old('dob', $kyc->dob) }}" required 
                        class="w-full border rounded-md px-3 py-2" placeholder="DD/MM/YYYY">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" class="w-full border rounded-md px-3 py-2">
                        <option value="pending" {{ $kyc->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ $kyc->status === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="not_found" {{ $kyc->status === 'not_found' ? 'selected' : '' }}>Not Found</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full border rounded-md px-3 py-2">{{ old('notes', $kyc->notes) }}</textarea>
                </div>
                
                <div class="flex justify-end gap-3">
                    <a href="{{ route('kyc.manual') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

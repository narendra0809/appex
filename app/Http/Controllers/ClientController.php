<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ClientsImport;

class ClientController extends Controller
{
    /**
     * Client listing page
     */
    public function index(Request $request)
    {
        $query = Client::query();

        // Simple text search across name, email and mobile
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('client_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('mobile', 'like', "%{$q}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.$request->city.'%');
        }

        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        // Date range filter for payment_date
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        // Sorting
        $allowedSorts = ['client_name', 'city', 'plan', 'gross_amount', 'payment_date', 'id'];
        $sortBy = $request->get('sort_by', 'id');
        if (! in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $sortDir = strtolower($request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $perPage = (int) $request->get('per_page', 15);

        $clients = $query->with(['agreement', 'invoice'])->orderBy($sortBy, $sortDir)->latest('id')->paginate($perPage)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    /**
     * Show client form (create/edit)
     */
    public function show($id = null)
    {
        $client = $id ? Client::findOrFail($id) : null;
        return view('clients.form', compact('client'));
    }

    /**
     * Store new client
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_date' => 'nullable|date',
            'client_name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'father_name' => 'nullable|string|max:255',
            'pan_card' => 'nullable|string|max:20',
            'aadhaar_card' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'gross_amount' => 'nullable|numeric',
            'net_amount' => 'nullable|numeric',
            'amount_type' => 'nullable|string|max:255',
            'segment' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
            'plan' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
            'service_start' => 'nullable|date',
            'service_end' => 'nullable|date',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client created successfully');
    }

    /**
     * Update existing client
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'payment_date' => 'nullable|date',
            'client_name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'father_name' => 'nullable|string|max:255',
            'pan_card' => 'nullable|string|max:20',
            'aadhaar_card' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'gross_amount' => 'nullable|numeric',
            'net_amount' => 'nullable|numeric',
            'amount_type' => 'nullable|string|max:255',
            'segment' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
            'plan' => 'nullable|string|max:255',
            'service_start' => 'nullable|date',
            'service_end' => 'nullable|date',
            'bank' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully');
    }

    /**
     * Delete client
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully');
    }

    /**
     * Excel import (old + new data)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new ClientsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Excel imported successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing Excel: ' . $e->getMessage());
        }
    }

    /**
     * Excel export - same format as import
     */
    public function export(Request $request)
    {
        $query = Client::query();

        // Apply same filters as index
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('client_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('mobile', 'like', "%{$q}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.$request->city.'%');
        }

        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $clients = $query->orderBy('id', 'desc')->get();

        $data = [
            ['Payment Date', 'Client Name', 'Mobile', 'Email', 'Father Name', 'PAN Card', 'Aadhaar Card', 'DOB', 'City', 'State', 'Gross Amount', 'Net Amount', 'Amount Type', 'Segment', 'Assigned To', 'Plan', 'Service Start', 'Service End', 'Bank', 'Remark']
        ];

        foreach ($clients as $client) {
            $data[] = [
                $client->payment_date ? \Carbon\Carbon::parse($client->payment_date)->format('d-M-y') : '-',
                $client->client_name,
                $client->mobile ?? '-',
                $client->email ?? '-',
                $client->father_name ?? '-',
                $client->pan_card ?? '-',
                $client->aadhaar_card ?? '-',
                $client->dob ? \Carbon\Carbon::parse($client->dob)->format('d-M-y') : '-',
                $client->city ?? '-',
                $client->state ?? '-',
                $client->gross_amount ?? 0,
                $client->net_amount ?? 0,
                $client->amount_type ?? '-',
                $client->segment ?? '-',
                $client->assigned_to ?? '-',
                $client->plan ?? '-',
                $client->service_start ? \Carbon\Carbon::parse($client->service_start)->format('d-M-y') : '-',
                $client->service_end ? \Carbon\Carbon::parse($client->service_end)->format('d-M-y') : '-',
                $client->bank ?? '-',
                $client->remark ?? '-',
            ];
        }

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 'clients_' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}

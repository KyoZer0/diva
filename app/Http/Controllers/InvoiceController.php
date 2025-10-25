<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{

    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'user']);

        // If user is not admin, only show invoices they added
        if (!Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::id());
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);
        $clients = Client::orderBy('name')->get();

        return view('invoices.index', compact('invoices', 'clients'));
    }

    public function create(Request $request)
    {
        $clients = Client::orderBy('name')->get();
        $selectedClientId = $request->get('client_id');
        return view('invoices.create', compact('clients', 'selectedClientId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'invoice_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'description' => 'nullable|string',
            'invoice_image' => 'nullable|image|max:10240', // 10MB max
        ]);

        $validated['user_id'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('invoice_image')) {
            $validated['invoice_image'] = $request->file('invoice_image')->store('invoices', 'public');
        }

        Invoice::create($validated);

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice added successfully!');
    }

    public function show(Invoice $invoice)
    {
        // Check if user can view this invoice
        if (!Auth::user()->isAdmin() && $invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to view this invoice.');
        }

        $invoice->load(['client', 'user']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        // Check if user can edit this invoice
        if (!Auth::user()->isAdmin() && $invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to edit this invoice.');
        }

        $clients = Client::orderBy('name')->get();
        return view('invoices.edit', compact('invoice', 'clients'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        // Check if user can update this invoice
        if (!Auth::user()->isAdmin() && $invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to update this invoice.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $invoice->id,
            'invoice_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'description' => 'nullable|string',
            'invoice_image' => 'nullable|image|max:10240', // 10MB max
        ]);

        // Handle file upload
        if ($request->hasFile('invoice_image')) {
            // Delete old image if exists
            if ($invoice->invoice_image) {
                \Storage::disk('public')->delete($invoice->invoice_image);
            }
            $validated['invoice_image'] = $request->file('invoice_image')->store('invoices', 'public');
        }

        $invoice->update($validated);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully!');
    }
}
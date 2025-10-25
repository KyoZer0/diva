<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::where('user_id', Auth::id());

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by client type
        if ($request->filled('client_type')) {
            $query->where('client_type', $request->client_type);
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('clients.index', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_type' => 'required|in:individual,company',
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:255',
            'likes' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:lead,prospect,customer,inactive',
            'budget_range' => 'nullable|numeric|min:0',
            'last_contact_date' => 'nullable|date'
        ]);

        $validated['user_id'] = Auth::id();

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client added successfully!');
    }

    public function show(Client $client)
    {
        // Check if user can view this client
        if ($client->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Load client with related data
        $client->load(['user']);
        
        // Get invoices for this client
        $invoices = \App\Models\Invoice::where('client_id', $client->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clients.show', compact('client', 'invoices'));
    }

    public function destroy(Client $client)
    {
        if ($client->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }
        
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully!');
    }

    public function analytics()
    {
        $clients = Client::where('user_id', Auth::id())->get();

        // Analytics data
        $totalClients = $clients->count();
        
        $sources = $clients->groupBy('source')->map(function ($group) {
            return [
                'count' => $group->count(),
                'percentage' => 0
            ];
        })->toArray();

        $totalForPercentage = array_sum(array_column($sources, 'count'));
        foreach ($sources as &$source) {
            $source['percentage'] = $totalForPercentage > 0 
                ? round(($source['count'] / $totalForPercentage) * 100, 1) 
                : 0;
        }

        $cities = $clients->where('city', '!=', null)
            ->groupBy('city')
            ->map(fn($group) => $group->count())
            ->sortDesc()
            ->take(10);

        // Status distribution
        $statusDistribution = $clients->groupBy('status')->map(fn($group) => $group->count())->toArray();

        // Conversion rate calculation (leads to customers)
        $totalLeads = $clients->where('status', 'lead')->count();
        $totalCustomers = $clients->where('status', 'customer')->count();
        $conversionRate = $totalLeads > 0 ? round(($totalCustomers / $totalLeads) * 100, 1) : 0;

        return view('analytics.index', compact('totalClients', 'sources', 'cities', 'statusDistribution', 'conversionRate'));
    }
}

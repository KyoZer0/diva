<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientController extends Controller
{
    
    use AuthorizesRequests;
    /**
     * Display a listing of the clients.
     */
    public function index(Request $request)
    {
        $query = Client::where('user_id', Auth::id());
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by client type
        if ($request->filled('client_type')) {
            $query->where('client_type', $request->client_type);
        }
        
        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        
        // Filter by source
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $clients = $query->get();
        
        // Get unique cities and sources for filters
        $cities = Client::where('user_id', Auth::id())
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->pluck('city')
            ->sort();
            
        $sources = Client::where('user_id', Auth::id())
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->distinct()
            ->pluck('source')
            ->sort();
        
        return view('clients.index', compact('clients', 'cities', 'sources'));
    }
    
    /**
     * Export clients to CSV
     */
    public function export()
    {
        $clients = Client::where('user_id', Auth::id())->get();
        
        $filename = 'clients_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($clients) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'Nom complet',
                'Type',
                'Entreprise',
                'Téléphone',
                'Email',
                'Ville',
                'Source',
                'Produits',
                'Conseiller',
                'Devis demandé',
                'Statut',
                'Notes',
                'Date ajout',
                'Dernier contact'
            ]);
            
            // Data
            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->full_name,
                    $client->client_type,
                    $client->company_name,
                    $client->phone,
                    $client->email,
                    $client->city,
                    $client->source,
                    is_array($client->products) ? implode(', ', $client->products) : '',
                    $client->conseiller,
                    $client->devis_demande ? 'Oui' : 'Non',
                    $client->status,
                    $client->notes,
                    $client->created_at->format('Y-m-d H:i:s'),
                    $client->last_contact_date ? $client->last_contact_date->format('Y-m-d') : ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'client_type' => 'required|in:particulier,professionnel',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:255',
            'products' => 'nullable|array',
            'products.*' => 'string|max:255',
            'conseiller' => 'nullable|string|max:255',
            'devis_demande' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:visited,purchased,follow_up',
            'last_contact_date' => 'nullable|date',
        ]);

        Client::create([
            'user_id' => Auth::id(),
            'full_name' => $validated['full_name'],
            'client_type' => $validated['client_type'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'city' => $validated['city'] ?? null,
            'source' => $validated['source'] ?? null,
            'products' => isset($validated['products']) ? json_encode($validated['products']) : null,
            'conseiller' => $validated['conseiller'] ?? null,
            'devis_demande' => $validated['devis_demande'] ?? false,
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'] ?? 'visited',
            'last_contact_date' => $validated['last_contact_date'] ?? null,
        ]);

        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès!');
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'client_type' => 'required|in:particulier,professionnel',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:255',
            'products' => 'nullable|array',
            'products.*' => 'string|max:255',
            'conseiller' => 'nullable|string|max:255',
            'devis_demande' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:visited,purchased,follow_up',
            'last_contact_date' => 'nullable|date',
        ]);

        $client->update([
            'full_name' => $validated['full_name'],
            'client_type' => $validated['client_type'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'city' => $validated['city'] ?? null,
            'source' => $validated['source'] ?? null,
            'products' => isset($validated['products']) ? json_encode($validated['products']) : null,
            'conseiller' => $validated['conseiller'] ?? null,
            'devis_demande' => $validated['devis_demande'] ?? false,
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'] ?? 'visited',
            'last_contact_date' => $validated['last_contact_date'] ?? null,
        ]);

        return redirect()->route('clients.index')->with('success', 'Client modifié avec succès!');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès!');
    }

    /**
     * Display analytics dashboard.
     */
    public function analytics()
    {
        $user = Auth::user();
        
        // Get all clients for this user
        $clients = Client::where('user_id', $user->id)->get();
        
        // Total clients
        $totalClients = $clients->count();
        
        // Clients by source with percentages
        $sourcesData = $clients->groupBy('source')
            ->map(function ($group) use ($totalClients) {
                return [
                    'count' => $group->count(),
                    'percentage' => $totalClients > 0 ? round(($group->count() / $totalClients) * 100, 1) : 0
                ];
            })
            ->sortByDesc('count')
            ->toArray();
        
        // Translate source names
        $sourceTranslations = [
            'reseaux_sociaux' => 'Réseaux sociaux',
            'publicite' => 'Publicité',
            'recommandation' => 'Recommandation',
            'passage_showroom' => 'Passage showroom',
            'autre' => 'Autre',
        ];
        
        $sources = [];
        foreach ($sourcesData as $key => $data) {
            $translatedKey = $sourceTranslations[$key] ?? ucfirst(str_replace('_', ' ', $key));
            $sources[$translatedKey] = $data;
        }
        
        // Top cities
        $cities = $clients->whereNotNull('city')
            ->where('city', '!=', '')
            ->groupBy('city')
            ->map->count()
            ->sortDesc()
            ->take(10);
        
        // Status distribution
        $statusDistribution = $clients->groupBy('status')
            ->map->count()
            ->toArray();
        
        // Conversion rate (clients with devis_demande = true / total clients)
        $devisRequested = $clients->where('devis_demande', true)->count();
        $conversionRate = $totalClients > 0 ? round(($devisRequested / $totalClients) * 100, 1) : 0;
        
        // Products interest analysis
        $productsInterest = [];
        foreach ($clients as $client) {
            if ($client->products && is_array($client->products)) {
                foreach ($client->products as $product) {
                    if (!isset($productsInterest[$product])) {
                        $productsInterest[$product] = 0;
                    }
                    $productsInterest[$product]++;
                }
            }
        }
        arsort($productsInterest);
        
        // Translate product names
        $productTranslations = [
            'carrelage' => 'Carrelage',
            'meubles' => 'Meubles',
            'sanitaires' => 'Sanitaires',
            'autre' => 'Autre',
        ];
        
        $translatedProducts = [];
        foreach ($productsInterest as $key => $count) {
            $translatedKey = $productTranslations[$key] ?? ucfirst($key);
            $translatedProducts[$translatedKey] = [
                'count' => $count,
                'percentage' => $totalClients > 0 ? round(($count / $totalClients) * 100, 1) : 0
            ];
        }
        
        // Client type distribution
        $clientTypes = $clients->groupBy('client_type')
            ->map->count()
            ->toArray();
        
        // Recent activity (last 30 days)
        $recentClients = $clients->where('created_at', '>=', now()->subDays(30))->count();
        
        // Clients with quotes
        $clientsWithQuotes = $clients->where('devis_demande', true)->count();
        
        return view('analytics.index', compact(
            'totalClients',
            'sources',
            'cities',
            'statusDistribution',
            'conversionRate',
            'translatedProducts',
            'clientTypes',
            'recentClients',
            'clientsWithQuotes'
        ));
    }
}
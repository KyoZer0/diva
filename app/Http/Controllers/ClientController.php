<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientController extends Controller
{
    
    use AuthorizesRequests;
    
    /**
     * Display a listing of clients.
     * Admin sees all clients, Reps see only their own.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Admin sees all clients, Rep sees only their own
        if ($user->isAdmin()) {
            $query = Client::query();
        } else {
            $query = Client::where('user_id', $user->id);
        }
        
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
        $baseQuery = $user->isAdmin() ? Client::query() : Client::where('user_id', $user->id);
        
        $cities = (clone $baseQuery)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->pluck('city')
            ->sort();
            
        $sources = (clone $baseQuery)
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
        $user = Auth::user();
        
        // Admin exports all clients, Rep exports only their own
        if ($user->isAdmin()) {
            $clients = Client::all();
        } else {
            $clients = Client::where('user_id', $user->id)->get();
        }
        
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
                'Style',
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
                    $client->client_type === 'particulier' ? 'Particulier' : 'Professionnel',
                    $client->company_name ?? '',
                    $client->phone,
                    $client->email ?? '',
                    $client->city ?? '',
                    $client->source ? ucfirst(str_replace('_', ' ', $client->source)) : '',
                    is_array($client->products) ? implode(', ', array_map(function($p) {
                        return ucfirst(str_replace(['_', 'Autres: '], [' ', ''], $p));
                    }, $client->products)) : '',
                    is_array($client->style) ? implode(', ', array_map(function($s) {
                        return ucfirst(str_replace(['_', 'Autres: '], [' ', ''], $s));
                    }, $client->style)) : '',
                    $client->conseiller ?? '',
                    $client->devis_demande ? 'Oui' : 'Non',
                    $client->status === 'visited' ? 'A visité' : ($client->status === 'purchased' ? 'Client' : ($client->status === 'follow_up' ? 'À recontacter' : ucfirst($client->status))),
                    $client->notes ?? '',
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
        $reps = collect();
        if (Auth::user()->isAdmin()) {
            $reps = User::whereHas('roles', function($query) {
                $query->where('name', 'rep');
            })->orderBy('name')->get();
        }
        return view('clients.create', compact('reps'));
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $rules = [
            'full_name' => 'required|string|max:255',
            'client_type' => 'required|in:particulier,professionnel',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:255',
            'products' => 'nullable|array',
            'products.*' => 'string|max:255',
            'style' => 'nullable|array',
            'style.*' => 'string|max:255',
            'conseiller' => 'nullable|string|max:255',
            'devis_demande' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:visited,purchased,follow_up',
            'last_contact_date' => 'nullable|date',
        ];
        
        // Admin must assign to a rep
        if ($user->isAdmin()) {
            $rules['assigned_rep_id'] = 'required|exists:users,id';
        } else {
            $rules['assigned_rep_id'] = 'nullable|exists:users,id';
        }
        
        $validated = $request->validate($rules);

        // Determine user_id and conseiller
        $userId = Auth::id();
        $conseillerName = null;

        if ($user->isAdmin()) {
            $assignedRep = User::findOrFail($validated['assigned_rep_id']);
            $userId = $assignedRep->id;
            $conseillerName = $assignedRep->name;
        } else {
            $userId = Auth::id();
            $conseillerName = $user->name;
        }

        Client::create([
            'user_id' => $userId,
            'full_name' => $validated['full_name'],
            'client_type' => $validated['client_type'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'city' => $validated['city'] ?? null,
            'source' => $validated['source'] ?? null,
            'products' => !empty($validated['products']) ? $validated['products'] : null,
            'style' => !empty($validated['style']) ? $validated['style'] : null,
            'conseiller' => $conseillerName,
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
        // Admin can see all clients, Reps can only see their own
        if (!Auth::user()->isAdmin() && $client->user_id !== Auth::id()) {
            abort(403, 'Non autorisé');
        }
        
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client)
    {
        // Admin can edit all clients, Reps can only edit their own
        if (!Auth::user()->isAdmin() && $client->user_id !== Auth::id()) {
            abort(403, 'Non autorisé');
        }
        
        $reps = collect();
        if (Auth::user()->isAdmin()) {
            $reps = User::whereHas('roles', function($query) {
                $query->where('name', 'rep');
            })->orderBy('name')->get();
        }
        
        return view('clients.edit', compact('client', 'reps'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client)
    {
        // Admin can update all clients, Reps can only update their own
        if (!Auth::user()->isAdmin() && $client->user_id !== Auth::id()) {
            abort(403, 'Non autorisé');
        }
        
        $user = Auth::user();
        
        $rules = [
            'full_name' => 'required|string|max:255',
            'client_type' => 'required|in:particulier,professionnel',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:255',
            'products' => 'nullable|array',
            'products.*' => 'string|max:255',
            'style' => 'nullable|array',
            'style.*' => 'string|max:255',
            'conseiller' => 'nullable|string|max:255',
            'devis_demande' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:visited,purchased,follow_up',
            'last_contact_date' => 'nullable|date',
        ];
        
        // Admin can reassign clients
        if ($user->isAdmin()) {
            $rules['assigned_rep_id'] = 'nullable|exists:users,id';
        }
        
        $validated = $request->validate($rules);

        $updateData = [
            'full_name' => $validated['full_name'],
            'client_type' => $validated['client_type'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'city' => $validated['city'] ?? null,
            'source' => $validated['source'] ?? null,
            'products' => !empty($validated['products']) ? $validated['products'] : null,
            'style' => !empty($validated['style']) ? $validated['style'] : null,
            'conseiller' => $validated['conseiller'] ?? null,
            'devis_demande' => $validated['devis_demande'] ?? false,
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'] ?? 'visited',
            'last_contact_date' => $validated['last_contact_date'] ?? null,
        ];
        
        // If admin reassigns client
        if ($user->isAdmin() && isset($validated['assigned_rep_id'])) {
            $assignedRep = User::findOrFail($validated['assigned_rep_id']);
            $updateData['user_id'] = $assignedRep->id;
            $updateData['conseiller'] = $assignedRep->name;
        }

        $client->update($updateData);

        return redirect()->route('clients.index')->with('success', 'Client modifié avec succès!');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client)
    {
        // Only admin can delete clients
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Non autorisé');
        }
        
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès!');
    }

    /**
     * Display analytics dashboard.
     */
    public function analytics()
{
    $user = Auth::user();
    
    // 1. Base Query (Admin sees all, Rep sees their own)
    $query = $user->isAdmin() ? Client::query() : Client::where('user_id', $user->id);
    
    // 2. Headline Stats
    $totalClients = $query->count();
    
    // Growth (This Month vs Last Month)
    $thisMonthCount = $query->clone()
        ->where('created_at', '>=', now()->startOfMonth())
        ->count();
        
    $lastMonthCount = $query->clone()
        ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
        ->count();
    
    $growthPercentage = $lastMonthCount > 0 
        ? (($thisMonthCount - $lastMonthCount) / $lastMonthCount) * 100 
        : ($thisMonthCount > 0 ? 100 : 0);

    // 3. The Funnel (Matches your migration structure)
    // 'visited' here represents the Total Pool of leads
    $funnel = [
        'visited'   => $totalClients, 
        'quotes'    => $query->clone()->where('devis_demande', true)->count(),
        'purchased' => $query->clone()->where('status', 'purchased')->count(),
    ];

    // 4. Source Intelligence (With "Efficiency" rating)
    // We group by your 'source' string column
    $sourcesData = $query->clone()
        ->select('source', 'devis_demande')
        ->whereNotNull('source')
        ->get()
        ->groupBy('source');
        
    $sourceIntelligence = [];
    foreach($sourcesData as $source => $rows) {
        $count = $rows->count();
        $quotes = $rows->where('devis_demande', true)->count();
        
        $sourceIntelligence[ucfirst(str_replace('_', ' ', $source))] = [
            'count' => $count,
            'conversion_rate' => $count > 0 ? round(($quotes / $count) * 100) : 0,
            'percentage' => $totalClients > 0 ? round(($count / $totalClients) * 100) : 0
        ];
    }
    // Sort by Volume (count)
    uasort($sourceIntelligence, fn($a, $b) => $b['count'] <=> $a['count']);

    // 5. Growth Chart (Last 6 Months)
    // Uses 'created_at' timestamp
    $growthChart = $query->clone()
        ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
        ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
        ->groupBy('month')
        ->pluck('count', 'month')
        ->toArray();

    $formattedGrowth = [];
    for ($i = 5; $i >= 0; $i--) {
        $key = now()->subMonths($i)->format('Y-m');
        $label = now()->subMonths($i)->locale('fr')->format('M'); // Short month name in French
        $formattedGrowth[$label] = $growthChart[$key] ?? 0;
    }

    // 6. Product Demand (Parsing the JSON column)
    // Matches: $table->json('products')
    $allClients = $query->clone()->select('products')->get();
    $productStats = [];
    
    foreach ($allClients as $c) {
        $prods = $c->products;
        // Safety check: decode if it's a string, or use as array if cast in model
        if (is_string($prods)) $prods = json_decode($prods, true);
        
        if (is_array($prods)) {
            foreach ($prods as $p) {
                // Cleanup name: remove "Autres: " prefix and underscores
                $cleanName = ucfirst(str_replace(['_', 'Autres: '], [' ', ''], $p));
                if (!isset($productStats[$cleanName])) $productStats[$cleanName] = 0;
                $productStats[$cleanName]++;
            }
        }
    }
    arsort($productStats);
    $topProducts = array_slice($productStats, 0, 5); // Keep top 5

    // 7. "At Risk" Clients (Actionable Data)
    // Clients not updated in 30 days and NOT purchased yet
    $staleClients = $query->clone()
        ->where('status', '!=', 'purchased')
        ->where('updated_at', '<', now()->subDays(30))
        ->orderBy('updated_at', 'asc') // Oldest interaction first
        ->limit(5)
        ->get();

    // 8. Rep Performance (Admin Only)
    // Requires User::clients() relationship
    $repStats = [];
    if ($user->isAdmin()) {
        $repStats = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'rep'))
            ->withCount('clients') // Total clients
            ->withCount(['clients as quotes_count' => function ($query) {
                $query->where('devis_demande', true);
            }])
            ->withCount(['clients as recent_clients' => function ($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            }])
            ->get()
            ->map(function ($rep) {
                $rep->conversion_rate = $rep->clients_count > 0 
                    ? round(($rep->quotes_count / $rep->clients_count) * 100) 
                    : 0;
                return $rep;
            })
            ->sortByDesc('conversion_rate'); // Sort by efficiency
    }

    return view('analytics.index', compact(
        'totalClients', 
        'thisMonthCount', // Renamed for clarity in view
        'recentClients', // You can map thisMonthCount to this if view expects it
        'growthPercentage', 
        'funnel', 
        'sourceIntelligence', 
        'formattedGrowth', 
        'topProducts', 
        'staleClients', 
        'repStats',
        'clientsWithQuotes', // Calculate below if needed specifically
        'conversionRate' // Calculate below
    ))
    ->with('clientsWithQuotes', $funnel['quotes'])
    ->with('recentClients', $thisMonthCount)
    ->with('conversionRate', $totalClients > 0 ? round(($funnel['quotes'] / $totalClients) * 100, 1) : 0);
}

/**
     * Agenda / Calendar Page
     */
    public function calendar()
    {
        $user = Auth::user();

        // 1. Upcoming Tasks: Clients marked as 'follow_up' or 'visited' ordered by last update
        // We assume clients not updated recently are the priority
        $upcoming = Client::where('user_id', $user->id)
            ->whereIn('status', ['follow_up', 'visited'])
            ->orderBy('updated_at', 'asc') // Oldest update = highest priority to reconnect
            ->limit(20)
            ->get();

        // 2. Real Stats for the Side Widget
        // Quotes generated this week
        $quotesThisWeek = Client::where('user_id', $user->id)
            ->where('devis_demande', true)
            ->where('updated_at', '>=', now()->startOfWeek())
            ->count();

        // Total clients waiting for follow-up
        $pendingFollowUps = Client::where('user_id', $user->id)
            ->where('status', 'follow_up')
            ->count();

        // Calculate a dynamic progress (Assuming a target of 5 quotes/week for gamification)
        $weeklyGoal = 5; 
        $quoteProgress = min(100, round(($quotesThisWeek / $weeklyGoal) * 100));

        return view('calendar', compact('upcoming', 'quotesThisWeek', 'pendingFollowUps', 'quoteProgress'));
    }
}
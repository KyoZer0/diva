<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function dashboard()
    {
        $totalClients = Client::count();
        $totalReps = User::whereHas('roles', function($query) {
            $query->where('name', 'rep');
        })->count();

        $recentClients = Client::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalClients', 'totalReps', 'recentClients'
        ));
    }

    public function allClients(Request $request)
    {
        $query = Client::with('user');

        // Filter by rep
        if ($request->filled('rep_id')) {
            $query->where('user_id', $request->rep_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by client type
        if ($request->filled('client_type')) {
            $query->where('client_type', $request->client_type);
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(20);
        $reps = User::whereHas('roles', function($query) {
            $query->where('name', 'rep');
        })->get();

        return view('admin.clients', compact('clients', 'reps'));
    }

    public function repPerformance()
    {
        $reps = User::whereHas('roles', function($query) {
            $query->where('name', 'rep');
        })->with(['clients'])->get();

        $repStats = $reps->map(function($rep) {
            $clients = $rep->clients;

            return [
                'rep' => $rep,
                'total_clients' => $clients->count(),
            ];
        });

        return view('admin.rep-performance', compact('repStats'));
    }

    /**
     * Display analytics dashboard for admin.
     * Admin sees ALL clients from ALL reps.
     */
    public function analytics()
    {
        // Get ALL clients (admin can see everything)
        $clients = Client::all();
        
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
        
        // ADMIN SPECIFIC: Rep performance stats
        $reps = User::whereHas('roles', function($query) {
            $query->where('name', 'rep');
        })->get();
        
        $repStats = [];
        foreach ($reps as $rep) {
            $repClients = $clients->where('user_id', $rep->id);
            $repStats[] = [
                'rep' => $rep,
                'total_clients' => $repClients->count(),
                'clients_with_quotes' => $repClients->where('devis_demande', true)->count(),
                'recent_clients' => $repClients->where('created_at', '>=', now()->subDays(30))->count(),
                'purchased_clients' => $repClients->where('status', 'purchased')->count(),
            ];
        }
        
        // Sort by total clients
        usort($repStats, function($a, $b) {
            return $b['total_clients'] - $a['total_clients'];
        });
        
        return view('analytics.index', compact(
            'totalClients',
            'sources',
            'cities',
            'statusDistribution',
            'conversionRate',
            'translatedProducts',
            'clientTypes',
            'recentClients',
            'clientsWithQuotes',
            'repStats'
        ));
    }

    /**
     * Display detailed analytics for a specific rep.
     */
    public function repDetails($repId)
    {
        $rep = User::findOrFail($repId);
        
        // Get all clients for this rep
        $clients = Client::where('user_id', $repId)->get();
        
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
        
        // Client type distribution
        $clientTypes = $clients->groupBy('client_type')
            ->map->count()
            ->toArray();
        
        // Recent activity (last 30 days)
        $recentClients = $clients->where('created_at', '>=', now()->subDays(30))->count();
        
        // Clients with quotes
        $clientsWithQuotes = $clients->where('devis_demande', true)->count();
        
        // Recent 10 clients
        $recentClientsList = Client::where('user_id', $repId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.rep-details', compact(
            'rep',
            'totalClients',
            'sources',
            'cities',
            'statusDistribution',
            'clientTypes',
            'recentClients',
            'clientsWithQuotes',
            'recentClientsList'
        ));
    }
}
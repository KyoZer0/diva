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
        $cities = Client::whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->pluck('city')
            ->sort();
            
        $sources = Client::whereNotNull('source')
            ->where('source', '!=', '')
            ->distinct()
            ->pluck('source')
            ->sort();

        $reps = User::whereHas('roles', function($query) {
            $query->where('name', 'rep');
        })->orderBy('name')->get();

        return view('admin.all-clients', compact('clients', 'reps', 'cities', 'sources'));
    }

    /**
     * Display all reps
     */
    public function reps()
    {
        $reps = User::whereHas('roles', function($query) {
            $query->where('name', 'rep');
        })->with(['clients'])->orderBy('name')->get();

        $repStats = $reps->map(function($rep) {
            $clients = $rep->clients;

            return [
                'rep' => $rep,
                'total_clients' => $clients->count(),
                'purchased_clients' => $clients->where('status', 'purchased')->count(),
                'recent_clients' => $clients->where('created_at', '>=', now()->subDays(30))->count(),
                'clients_with_quotes' => $clients->where('devis_demande', true)->count(),
            ];
        });

        return view('admin.reps', compact('repStats'));
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

    /**
     * Export all clients to CSV (admin)
     */
    public function exportAllClients()
    {
        $clients = Client::with('user')->get();
        
        $filename = 'tous_les_clients_' . date('Y-m-d_H-i-s') . '.csv';
        
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
                    $client->user->name ?? '',
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
     * Export rep's clients to CSV
     */
    public function exportRepClients($repId)
    {
        $rep = User::findOrFail($repId);
        $clients = Client::where('user_id', $repId)->get();
        
        $filename = 'clients_' . str_replace(' ', '_', $rep->name) . '_' . date('Y-m-d_H-i-s') . '.csv';
        
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
}
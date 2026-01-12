<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        return redirect()->route('admin.analytics');
    }

    /**
     * Main Analytics View
     */
    public function analytics()
    {
        // 1. Snapshot KPIs (Always Global)
        $query = Client::query();
        $totalClients = $query->count();
        $thisMonth = $query->clone()->where('created_at', '>=', now()->startOfMonth())->count();
        $lastMonth = $query->clone()->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();
        $growthPercentage = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : ($thisMonth > 0 ? 100 : 0);

        // 2. Funnel Data
        $funnel = [
            'visited'   => $totalClients, 
            'quotes'    => $query->clone()->where('devis_demande', true)->count(),
            'purchased' => $query->clone()->where('status', 'purchased')->count(),
        ];
        
        $clientsWithQuotes = $funnel['quotes'];
        $conversionRate = $totalClients > 0 ? round(($clientsWithQuotes / $totalClients) * 100, 1) : 0;

        // 3. Detailed Tables Data
        // Source Intelligence
        $sourcesRaw = $query->clone()->select('source', 'devis_demande')->whereNotNull('source')->get()->groupBy('source');
        $sourceIntelligence = [];
        foreach($sourcesRaw as $source => $rows) {
            $count = $rows->count();
            $quotes = $rows->where('devis_demande', true)->count();
            $sourceIntelligence[ucfirst(str_replace('_', ' ', $source))] = [
                'count' => $count,
                'conversion_rate' => $count > 0 ? round(($quotes / $count) * 100) : 0,
                'percentage' => $totalClients > 0 ? round(($count / $totalClients) * 100) : 0
            ];
        }
        uasort($sourceIntelligence, fn($a, $b) => $b['count'] <=> $a['count']);

        // Demographics
        $cities = $query->clone()->whereNotNull('city')->where('city', '!=', '')->groupBy('city')->select('city', DB::raw('count(*) as count'))->orderByDesc('count')->limit(5)->pluck('count', 'city');
        $clientTypes = $query->clone()->groupBy('client_type')->select('client_type', DB::raw('count(*) as count'))->pluck('count', 'client_type');

        // At Risk Clients
        $staleClients = $query->clone()->where('status', '!=', 'purchased')->where('updated_at', '<', now()->subDays(30))->orderBy('updated_at', 'asc')->limit(5)->get();

        // Rep Performance
        $repStats = User::whereHas('roles', fn($q) => $q->where('name', 'rep'))
            ->withCount('clients')
            ->withCount(['clients as quotes_count' => fn($q) => $q->where('devis_demande', true)])
            ->withCount(['clients as recent_clients' => fn($q) => $q->where('created_at', '>=', now()->subDays(30))])
            ->get()
            ->map(function ($rep) {
                $rep->conversion_rate = $rep->clients_count > 0 ? round(($rep->quotes_count / $rep->clients_count) * 100) : 0;
                return $rep;
            })->sortByDesc('conversion_rate');

        // 4. Initial Chart Data (Default 30 Days)
        $chartData = $this->fetchChartData(30);

        return view('analytics.index', compact(
            'totalClients', 'thisMonth', 'growthPercentage', 
            'funnel', 'sourceIntelligence', 'cities', 'clientTypes', 
            'staleClients', 'repStats', 'clientsWithQuotes', 'conversionRate',
            'chartData' // Pass the JSON ready data
        ));
    }

    /**
     * API: Get Chart Data via AJAX
     */
    public function getChartData(Request $request)
    {
        $days = $request->get('days', 30);
        return response()->json($this->fetchChartData($days));
    }

    /**
     * Helper to query logic for charts
     */
    private function fetchChartData($days)
    {
        $startDate = now()->subDays($days);

        // 1. Line Chart: Trend Over Time
        $trends = Client::selectRaw('DATE(created_at) as date, count(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // 2. Doughnut Chart: Sources Distribution
        $sources = Client::select('source', DB::raw('count(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('source')
            ->groupBy('source')
            ->orderByDesc('count')
            ->get();

        // 3. Bar Chart: Products Demand (Requires parsing logic)
        // Note: For performance on large datasets with JSON, native SQL JSON queries are better, 
        // but for compatibility we will fetch and parse in PHP for now.
        $allProducts = Client::select('products')
            ->where('created_at', '>=', $startDate)
            ->get();
            
        $productStats = [];
        foreach ($allProducts as $c) {
            $prods = $c->products;
            if (is_string($prods)) $prods = json_decode($prods, true);
            if (is_array($prods)) {
                foreach ($prods as $p) {
                    $clean = ucfirst(str_replace(['_', 'Autres: '], [' ', ''], $p));
                    if (!isset($productStats[$clean])) $productStats[$clean] = 0;
                    $productStats[$clean]++;
                }
            }
        }
        arsort($productStats);
        $topProducts = array_slice($productStats, 0, 5);

        return [
            'trend' => [
                'labels' => $trends->pluck('date')->map(fn($d) => date('d/m', strtotime($d))),
                'data' => $trends->pluck('count')
            ],
            'sources' => [
                'labels' => $sources->pluck('source')->map(fn($s) => ucfirst(str_replace('_', ' ', $s))),
                'data' => $sources->pluck('count')
            ],
            'products' => [
                'labels' => array_keys($topProducts),
                'data' => array_values($topProducts)
            ]
        ];
    }

    // --- EXISTING EXPORT & FILTER METHODS (Preserved) ---
    
    public function exportAllClients()
    {
        $clients = Client::with('user')->latest()->get();
        return $this->generateExcelResponse($clients, 'tous_les_clients');
    }

    public function exportRepClients($repId)
    {
        $rep = User::findOrFail($repId);
        $clients = Client::where('user_id', $repId)->latest()->get();
        return $this->generateExcelResponse($clients, 'clients_' . str_replace(' ', '_', $rep->name));
    }
    
    public function allClients(Request $request) {
        $query = Client::with('user');
        if ($request->filled('rep_id')) $query->where('user_id', $request->rep_id);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }
        // ... rest of filters
        $clients = $query->orderBy('created_at', 'desc')->get();
        $cities = Client::whereNotNull('city')->distinct()->pluck('city')->sort();
        $sources = Client::whereNotNull('source')->distinct()->pluck('source')->sort();
        $reps = User::whereHas('roles', fn($q) => $q->where('name', 'rep'))->orderBy('name')->get();
        return view('admin.all-clients', compact('clients', 'reps', 'cities', 'sources'));
    }

    public function reps() {
        $reps = User::whereHas('roles', fn($q) => $q->where('name', 'rep'))->with(['clients'])->orderBy('name')->get();
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

    public function repDetails($repId) {
        $rep = User::findOrFail($repId);
        $clients = Client::where('user_id', $repId)->get();
        $totalClients = $clients->count();
        $sourcesData = $clients->groupBy('source')->map(function ($group) use ($totalClients) {
            return ['count' => $group->count(), 'percentage' => $totalClients > 0 ? round(($group->count() / $totalClients) * 100, 1) : 0];
        })->sortByDesc('count');
        $sources = [];
        foreach ($sourcesData as $key => $data) $sources[ucfirst(str_replace('_', ' ', $key))] = $data;
        $cities = $clients->whereNotNull('city')->groupBy('city')->map->count()->sortDesc()->take(10);
        $statusDistribution = $clients->groupBy('status')->map->count();
        $clientTypes = $clients->groupBy('client_type')->map->count();
        $recentClients = $clients->where('created_at', '>=', now()->subDays(30))->count();
        $clientsWithQuotes = $clients->where('devis_demande', true)->count();
        $recentClientsList = Client::where('user_id', $repId)->orderBy('created_at', 'desc')->limit(10)->get();
        return view('admin.rep-details', compact('rep', 'totalClients', 'sources', 'cities', 'statusDistribution', 'clientTypes', 'recentClients', 'clientsWithQuotes', 'recentClientsList'));
    }

    private function generateExcelResponse($clients, $filenamePrefix)
    {
        $filename = $filenamePrefix . '_' . date('Y-m-d_H-i-s') . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache', 'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0', 'Expires' => '0',
        ];
        $callback = function() use ($clients) {
            $file = fopen('php://output', 'w');
            $output = '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            $output .= '<style>table{border-collapse:collapse;width:100%;}th{background:#000;color:#fff;border:1px solid #ccc;padding:10px;}td{border:1px solid #ccc;padding:8px;}.text{mso-number-format:"\@";}</style></head><body>';
            $output .= '<table><thead><tr><th>ID</th><th>Nom</th><th>Type</th><th>Entreprise</th><th>Tél</th><th>Email</th><th>Ville</th><th>Source</th><th>Produits</th><th>Style</th><th>Conseiller</th><th>Devis</th><th>Statut</th><th>Notes</th><th>Date</th></tr></thead><tbody>';
            foreach ($clients as $c) {
                $p = is_string($c->products) ? json_decode($c->products, true) : $c->products;
                $pStr = is_array($p) ? implode(', ', $p) : '';
                $s = is_string($c->style) ? json_decode($c->style, true) : $c->style;
                $sStr = is_array($s) ? implode(', ', $s) : '';
                $output .= "<tr><td>{$c->id}</td><td class='text'>{$c->full_name}</td><td>{$c->client_type}</td><td class='text'>{$c->company_name}</td><td class='text'>{$c->phone}</td><td>{$c->email}</td><td>{$c->city}</td><td>{$c->source}</td><td>{$pStr}</td><td>{$sStr}</td><td>".($c->user->name??'')."</td><td>".($c->devis_demande?'OUI':'NON')."</td><td>{$c->status}</td><td>{$c->notes}</td><td>{$c->created_at}</td></tr>";
            }
            $output .= '</tbody></table></body></html>';
            fwrite($file, $output);
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
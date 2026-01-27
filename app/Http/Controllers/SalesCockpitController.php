<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Bl;
use App\Services\SmartSuggestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesCockpitController extends Controller
{
    protected $recommender;

    public function __construct(SmartSuggestionService $recommender)
    {
        $this->recommender = $recommender;
    }

    public function index()
    {
        $user = Auth::user();

        // 1. My Sales Portfolio (Main Client Table, Scoped)
        $myClients = Client::where('user_id', $user->id)
            ->orderByDesc('potential_score')
            ->get();

        // 2. Smart Suggestions
        $suggestions = collect($this->recommender->getSuggestionsForRep($user->id));

        // 3. Simple KPIs
        $totalPotential = $myClients->sum('potential_score');
        $activeDeals = $myClients->where('smart_status', 'warm')->count();
        $taskCount = \App\Models\SalesTask::where('user_id', $user->id)->where('is_completed', false)->count();

        return view('tools.sales.index', compact('myClients', 'suggestions', 'totalPotential', 'activeDeals', 'taskCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50', // Required in clients table
            'email' => 'nullable|email|max:255',
            'interest_tags' => 'nullable|string', // Comma separated
            'client_type' => 'required|in:particulier,professionnel',
            'professional_category' => 'nullable|in:revendeur,architecte,promoteur',
        ]);

        $products = [];
        if (!empty($validated['interest_tags'])) {
            $products = array_map('trim', explode(',', $validated['interest_tags']));
        }

        $data = [
            'user_id' => Auth::id(),
            'full_name' => $validated['name'],
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'client_type' => $validated['client_type'],
            'professional_category' => $validated['client_type'] === 'professionnel' ? ($validated['professional_category'] ?? null) : null,
            'products' => $products,
            'smart_status' => 'cold',
            'potential_score' => 50,
            'status' => 'visited', // Default for main CRM
        ];

        Client::create($data);

        return redirect()->back()->with('success', 'Client ajouté au CRM.');
    }

    public function show(Client $client)
    {
        // Simple ownership check
        if ($client->user_id !== Auth::id()) {
            abort(403);
        }

        // 1. Fetch Logistics Data
        $relatedBls = Bl::where(function($q) use ($client) {
            $q->where('client_name', $client->full_name)
              ->orWhere('client_name', $client->company_name);
        })->with('articles')->orderBy('date', 'desc')->get();

        // 2. Client Analytics (Top Products)
        $blArticles = $relatedBls->pluck('articles')->flatten();
        $topArticles = $blArticles->groupBy('name')->map(function ($group) {
            return [
                'name' => $group->first()->name,
                'total_qty' => $group->sum('quantity'),
                'unit' => $group->first()->unit
            ];
        })->sortByDesc('total_qty')->take(5);

        // 3. Dynamic Recommendations
        $recentSales = Bl::where('created_at', '>=', now()->subDays(60))
            ->whereNull('supplier_ref') 
            ->with('articles')->get();
            
        $popularItems = $recentSales->pluck('articles')->flatten()
            ->groupBy('name')
            ->map(fn($g) => $g->count())
            ->sortDesc()->keys();

        $recentInbound = Bl::where('created_at', '>=', now()->subDays(30))
            ->whereNotNull('supplier_ref')
            ->with('articles')->get();
        $inStockNames = $recentInbound->pluck('articles')->flatten()->pluck('name')->unique()->toArray();

        $clientBoughtNames = $topArticles->pluck('name')->toArray();
        $recommendations = $popularItems->filter(function($name) use ($clientBoughtNames, $inStockNames) {
            if (in_array($name, $clientBoughtNames)) return false;
             if (!empty($inStockNames)) {
                return in_array($name, $inStockNames);
             }
             return true; 
        })->take(3);

        // 4. Volume Analytics by Unit (Breakdown)
        $volumeByUnit = $blArticles->groupBy('unit')->map(fn($group) => $group->sum('quantity'));

        // 5. Chart Data (Purchase Frequency - # of BLs over last 6 months)
        $chartData = [
            'labels' => [],
            'data' => []
        ];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $chartData['labels'][] = $date->translatedFormat('M');
            
            // Count unique BLs for this month
            $frequency = $relatedBls->filter(function($bl) use ($monthKey) {
                return \Carbon\Carbon::parse($bl->date)->format('Y-m') === $monthKey;
            })->count();
            
            $chartData['data'][] = $frequency;
        }

        return view('tools.sales.client', compact('client', 'relatedBls', 'topArticles', 'recommendations', 'chartData', 'volumeByUnit'));
    }

    public function edit(Client $client)
    {
        if ($client->user_id !== Auth::id()) abort(403);
        return view('tools.sales.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        if ($client->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'interest_tags' => 'nullable|string',
            'client_type' => 'required|in:particulier,professionnel',
            'professional_category' => 'nullable|in:revendeur,architecte,promoteur',
        ]);

        $products = [];
        if (!empty($validated['interest_tags'])) {
            $products = array_map('trim', explode(',', $validated['interest_tags']));
        }

        $client->update([
            'full_name' => $validated['name'],
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'client_type' => $validated['client_type'],
            'professional_category' => $validated['client_type'] === 'professionnel' ? ($validated['professional_category'] ?? null) : null,
            'products' => $products,
        ]);
        
        return redirect()->route('tools.sales.show', $client->id)->with('success', 'Profil mis à jour.');
    }

    public function updateNote(Request $request, Client $client)
    {
        if ($client->user_id !== Auth::id()) abort(403);
        
        $client->update(['notes' => $request->input('notes')]);
        return back()->with('success', 'Note sauvegardée.');
    }

    // ... Performance and Agenda (existing)
    public function performance()
    {
        $user = Auth::user();
        $myClients = Client::where('user_id', $user->id)->get();
        
        // Mock Data for Charts (Keep dynamic mock for now)
        $monthlyPerformance = [
            'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            'data' => [12, 19, 3, 5, 2, 3],
        ];

        $total = $myClients->count();
        $active = $myClients->whereIn('smart_status', ['warm', 'hot'])->count();
        $conversionRate = $total > 0 ? round(($active / $total) * 100) : 0;

        return view('tools.sales.performance', compact('monthlyPerformance', 'conversionRate', 'total', 'active'));
    }

    public function agenda()
    {
        $user = Auth::user();
        
        $tasks = \App\Models\SalesTask::where('user_id', $user->id)
            ->orderBy('position', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tools.sales.agenda', compact('tasks'));
    }

    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:client,product,memo'
        ]);

        $validated['user_id'] = Auth::id();
        \App\Models\SalesTask::create($validated);
        return back()->with('success', 'Tâche ajoutée.');
    }

    public function toggleTask(\App\Models\SalesTask $task)
    {
        if ($task->user_id !== Auth::id()) abort(403);
        $task->update(['is_completed' => !$task->is_completed]);
        return back();
    }

    public function destroyTask(\App\Models\SalesTask $task)
    {
        if ($task->user_id !== Auth::id()) abort(403);
        $task->delete();
        return back()->with('success', 'Tâche supprimée.');
    }

    public function reorderTasks(Request $request)
    {
        $items = $request->input('items', []);
        
        foreach($items as $item) {
            \App\Models\SalesTask::where('id', $item['id'])
                ->where('user_id', Auth::id())
                ->update([
                    'position' => $item['position'],
                    'is_completed' => $item['status'] == 'completed'
                ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function news()
    {
        $news = \App\Models\News::latest()->get();
        return view('tools.sales.news', compact('news'));
    }

    public function catalog(Request $request)
    {
        // Fetch from News table (which has stock_quantity and warehouse)
        $query = \App\Models\News::query();

        // Search filter
        if ($request->has('q') && $request->q != '') {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        $products = $query->orderBy('created_at', 'desc')->get();

        return view('products', compact('products'));
    }
}

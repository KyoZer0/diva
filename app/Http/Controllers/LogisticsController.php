<?php

namespace App\Http\Controllers;

use App\Models\Bl;
use App\Models\Article;
use App\Models\Incident;
use App\Models\CatalogProduct;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogisticsController extends Controller
{
    // --- API FOR SMART INPUTS ---
    
    // 1. Prevent Double Charging
    public function checkBlExists(Request $request) {
        $exists = Bl::where('bl_number', $request->bl_number)->first();
        if($exists) {
            return response()->json(['exists' => true, 'url' => route('tools.logistics.show', $exists->id)]);
        }
        return response()->json(['exists' => false]);
    }

    // 2. Smart Autocomplete
    public function searchCatalog(Request $request) {
        $term = $request->get('term');
        
        // Return empty if term is short
        if(strlen($term) < 2) return response()->json([]);
    
        // 1. Search in Catalog (Primary Source)
        $catalog = CatalogProduct::where('name', 'LIKE', "%{$term}%")
            ->orWhere('reference', 'LIKE', "%{$term}%")
            ->limit(10)
            ->get()
            ->map(function($p) {
                return [
                    'name' => $p->name,
                    'reference' => $p->reference,
                    'unit' => $p->unit,
                    'warehouse' => $p->default_warehouse,
                    'conversion' => $p->default_conversion
                ];
            });
        
        // 2. Search in History (Secondary Source)
        $history = Article::select('name', 'reference', 'unit', 'warehouse')
            ->where('name', 'LIKE', "%{$term}%")
            ->orWhere('reference', 'LIKE', "%{$term}%")
            ->orderBy('created_at', 'desc') // Prioritize recent items
            ->limit(30)
            ->get()
            ->unique('name'); // Deduplicate here to keep latest

        // Merge results: Catalog overrides History if name matches, but we want a rich list
        // Actually, let's prefer History for the warehouse suggestion if available
        $merged = $history->concat($catalog)->unique('name');
    
        // Transform for the frontend
        return $merged->map(function($item) {
            // Check for hidden conversion factor "REF123|1.44"
            $parts = explode('|', $item->reference);
            
            return [
                'name' => $item->name,
                'reference' => $parts[0] ?? '', // The clean reference
                'conversion' => $parts[1] ?? null, // The hidden math factor
                'unit' => $item->unit ?? 'box',
                'warehouse' => $item->warehouse ?? null // Suggest warehouse too!
            ];
        })->values(); // Reset keys
    }
    public function destroy(Bl $bl)
    {
        // Optional: Only allow if no articles are delivered yet
        if($bl->status === 'delivered') {
            return back()->with('error', 'Impossible de supprimer un BL validé/livré.');
        }
    
        $bl->articles()->delete();
        $bl->history()->delete();
        $bl->delete();
    
        return redirect()->route('tools.logistics.index')->with('success', 'Dossier supprimé.');
    }
    // --- DASHBOARD ---
    public function dashboard()
    {
        // A. The "Ghost" BLs (Backlog) - Older than today, not delivered
        // These are the ones we forgot about!
        $backlogBls = Bl::where('status', '!=', 'delivered')
            ->whereDate('created_at', '<', today())
            ->with(['articles'])
            ->orderBy('created_at', 'asc') // Oldest first to prioritize
            ->get();

        // B. Today's Active Feed
        $todayBls = Bl::whereDate('created_at', today())
            ->with(['articles'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($bl) {
                // UI State Logic
                $total = $bl->articles->count();
                $done = $bl->articles->where('status', 'delivered')->count();
                $partial = $bl->articles->where('status', 'partial')->count();
                
                $bl->progress_pct = $total > 0 ? ($done / $total) * 100 : 0;
                
                if ($partial > 0) $bl->ui_state = 'alert';
                elseif ($done > 0 && $done < $total) $bl->ui_state = 'progress';
                else $bl->ui_state = 'fresh';
                
                return $bl;
            });

        // KPIs
        $loadingCount = Bl::where('status', 'loading')->count();
        $todayItems = Article::whereDate('created_at', today())->sum('quantity');

        // Archives list for footer
        $archives = Bl::selectRaw('DATE(date) as day, count(*) as count')
            ->groupBy('day')
            ->orderBy('day', 'desc')
            ->limit(6)
            ->get();

        return view('tools.logistics.dashboard', compact('loadingCount', 'todayItems', 'todayBls', 'backlogBls', 'archives'));
    }

    // --- END OF DAY RITUAL ---
    public function dailyClosing() {
        $bls = Bl::whereDate('created_at', today())
            ->with('articles')
            ->get();
        return view('tools.logistics.closing', compact('bls'));
    }

    // --- STANDARD CRUD ---
    public function create() { 
        return view('tools.logistics.create'); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'bl_number' => 'required|unique:bls',
            'client_name' => 'required',
            'date' => 'required|date',
        ]);
    
        // ... (Your existing photo upload logic) ...
        $photoPath = $request->hasFile('supplier_photo') 
            ? $request->file('supplier_photo')->store('bl_scans', 'public') 
            : null;
    
        $bl = Bl::create([
            'bl_number' => $request->bl_number,
            'client_name' => $request->client_name,
            'date' => $request->date,
            'supplier_name' => $request->supplier_name,
            'supplier_ref' => $request->supplier_ref,
            'supplier_photo' => $photoPath,
            'status' => 'loading'
        ]);
    
        if ($request->has('articles')) {
            foreach ($request->articles as $item) {
                if(!empty($item['name'])) {
                    // Determine unit: if conversion used (and valid), it's m2
                    $unit = $item['unit'] ?? 'box';
                    if($unit === 'box' && !empty($item['conversion']) && floatval($item['conversion']) > 0) {
                        $unit = 'm2';
                    }

                    $bl->articles()->create([
                        'name' => $item['name'],
                        'quantity' => $item['final_quantity'], // We use the calculated quantity
                        'unit' => $unit,
                        'reference' => $item['reference'] ?? null,
                        'status' => 'pending',
                        'warehouse' => $item['warehouse'] ?? null
                    ]);
                    
                    // SMART LEARNING (NO MIGRATION HACK)
                    // We store the conversion factor in the reference if provided
                    // Format: "REF123|1.44"
                    $cleanRef = $item['reference'];
                    if(!empty($item['conversion']) && $item['unit'] === 'box') {
                        // Check if ref already has a pipe
                        if($cleanRef && strpos($cleanRef, '|') === false) {
                            $cleanRef = $cleanRef . '|' . $item['conversion'];
                        } elseif (!$cleanRef) {
                             $cleanRef = '|' . $item['conversion']; // Edge case: no ref, just conversion
                        }
                    }
    
                    \App\Models\CatalogProduct::updateOrCreate(
                        ['name' => $item['name']],
                        [
                            'reference' => $cleanRef, // Saves "Ref|Conversion"
                            'unit' => $item['unit'], // Keep original unit (box) for the catalog so it defaults correctly next time
                            'default_warehouse' => $item['warehouse'] ?? null,
                            'default_conversion' => $item['conversion'] ?? null
                        ]
                    );
                }
            }
        }
        return redirect()->route('tools.logistics.show', $bl->id)->with('success', 'BL Créé');
    }
    
        // --- EDIT & UPDATE ---
    public function edit(Bl $bl)
    {
        $bl->load('articles');
        return view('tools.logistics.edit', compact('bl'));
    }

    public function update(Request $request, Bl $bl)
    {
        // 1. Update Parent BL
        $bl->update([
            'bl_number' => $request->bl_number,
            'client_name' => $request->client_name,
            'date' => $request->date,
            'supplier_name' => $request->supplier_name,
            'supplier_ref' => $request->supplier_ref,
        ]);

        // 2. Sync Articles (Add/Update/Delete)
        if ($request->has('articles')) {
            $currentIds = [];
            
            foreach ($request->articles as $item) {
                if(!empty($item['name'])) {
                    // Update existing or Create new
                    $article = $bl->articles()->updateOrCreate(
                        ['id' => $item['id'] ?? null], // If ID exists, update. Else create.
                        [
                            'reference' => $item['reference'] ?? null,
                            'name' => $item['name'],
                            'quantity' => $item['quantity'],
                            'unit' => $item['unit'],
                            // Don't reset status on edit unless strictly needed
                        ]
                    );
                    $currentIds[] = $article->id;
                    
                    // Learn for Catalog
                    CatalogProduct::firstOrCreate(['name' => $item['name']]);
                }
            }
            
            // Delete articles that were removed from the form
            $bl->articles()->whereNotIn('id', $currentIds)->delete();
        }

        $bl->log('update', 'Dossier mis à jour manuellement.');
        return redirect()->route('tools.logistics.show', $bl->id)->with('success', 'Dossier mis à jour.');
    }

    public function destroyArticle(Article $article)
    {
        $article->delete();
        return back()->with('success', 'Ligne supprimée');
    }

    public function show(Bl $bl) {
        $bl->load(['articles', 'history.user']);
        return view('tools.logistics.show', compact('bl'));
    }

    public function editArticleDetails(Article $article) {
        // Find catalog entry to get default learning
        $catalog = CatalogProduct::where('name', $article->name)->first();
        
        // Extract conversion from reference if not explicitly set
        // Logic: "REF|1.44"
        $conversion = 0;
        if($article->reference && strpos($article->reference, '|') !== false) {
            $parts = explode('|', $article->reference);
            $conversion = floatval($parts[1]);
        } elseif ($catalog && $catalog->default_conversion) {
            $conversion = $catalog->default_conversion;
        }

        $default_boxes_per_pallet = $catalog->default_boxes_per_pallet ?? 0;

        return view('tools.logistics.articles.edit', compact('article', 'conversion', 'default_boxes_per_pallet'));
    }

    public function updateArticleDetails(Request $request, Article $article) {
        // 1. Update the specific article
        $article->update([
            'name' => $request->name,
            'reference' => $request->reference . ($request->conversion ? '|'.$request->conversion : ''),
            'warehouse' => $request->warehouse,
            'boxes_per_pallet' => $request->boxes_per_pallet,
            'pallet_count' => $request->pallet_count,
            'quantity' => $request->quantity
        ]);

        // 2. Smart Learning -> Update Catalog
        CatalogProduct::updateOrCreate(
            ['name' => $request->name],
            [
                'reference' => $request->reference,
                'default_warehouse' => $request->warehouse,
                'default_boxes_per_pallet' => $request->boxes_per_pallet,
                'default_conversion' => $request->conversion
            ]
        );

        return redirect()->route('tools.logistics.show', $article->bl_id)->with('success', 'Article et Catalogue mis à jour.');
    }

    public function updateStatus(Request $request, Bl $bl) {
        $newStatus = $request->status;
        DB::transaction(function () use ($bl, $newStatus) {
            $bl->update(['status' => $newStatus]);
            if ($newStatus === 'delivered') {
                foreach ($bl->articles as $article) {
                    $article->update(['status' => 'delivered', 'quantity_delivered' => $article->quantity]);
                }
            }
        });
        if(method_exists($bl, 'log')) $bl->log('status_change', "Statut Global : " . ucfirst($newStatus));
        return back()->with('success', 'Statut mis à jour.');
    }

    public function updateArticle(Request $request, Article $article) {
        if ($request->status === 'delivered') $request->merge(['quantity_delivered' => $article->quantity]);
        elseif ($request->status === 'pending') $request->merge(['quantity_delivered' => 0]);

        $article->update([
            'status' => $request->status,
            'quantity_delivered' => $request->quantity_delivered ?? 0
        ]);
        return back()->with('success', 'Article mis à jour');
    }

    public function addNote(Request $request, Bl $bl) {
        $bl->log('note', $request->note);
        return back()->with('success', 'Note ajoutée.');
    }

    public function archives(Request $request) {
        $dates = Bl::selectRaw('DATE(date) as day, count(*) as count')->groupBy('day')->orderBy('day', 'desc')->get();
        $date = $request->get('date', now()->format('Y-m-d'));
        $bls = Bl::whereDate('date', $date)->with(['articles'])->orderBy('created_at', 'desc')->get();
        $stats = ['total' => $bls->count(), 'items' => $bls->sum(fn($bl) => $bl->articles->count())];
        return view('tools.logistics.archives', compact('dates', 'bls', 'date', 'stats'));
    }

    public function incidents() {
        $incidents = Incident::latest()->get();
        return view('tools.sav.index', compact('incidents'));
    }
    
    public function storeIncident(Request $request) {
        Incident::create($request->all() + ['status' => 'reported']);
        return back();
    }
    
    public function news() {
        return view('tools.news.index', ['news' => News::latest()->get()]);
    }
    
    // 1. PRODUCT REGISTRY (The List)
    public function articleIndex(Request $request)
    {
        $query = Article::select('name', 'unit')
            ->selectRaw('MAX(reference) as reference') // Get the most common ref
            ->selectRaw('COUNT(*) as total_bls')       // How many times it appears
            ->selectRaw('SUM(quantity) as total_qty')  // Total volume moved
            ->selectRaw('MAX(created_at) as last_seen')
            ->groupBy('name', 'unit');

        if($request->has('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $products = $query->orderBy('last_seen', 'desc')->paginate(20);

        return view('tools.logistics.articles.index', compact('products'));
    }

    // 2. PRODUCT HISTORY (The Details)
    public function articleShow(Request $request)
    {
        $name = $request->query('name');
        
        // Fetch every BL that contains this specific product name
        $movements = Article::where('name', $name)
            ->with('bl')
            ->orderBy('created_at', 'desc')
            ->get();

        if($movements->isEmpty()) return redirect()->route('tools.logistics.articles.index');

        // Fetch Catalog Product for global details
        $product = CatalogProduct::firstOrCreate(['name' => $name]);

        // Stats for the header
        $stats = [
            'total_volume' => $movements->sum('quantity'),
            'unit' => $movements->first()->unit,
            'client_count' => $movements->unique('bl.client_name')->count(),
            'first_seen' => $movements->last()->created_at,
            'last_seen' => $movements->first()->created_at
        ];

        // --- NEW: ANALYTICS DATA ---
        
        // 1. Monthly Trends (Last 12 Months)
        $trendData = $movements->groupBy(function($date) {
            return \Carbon\Carbon::parse($date->created_at)->format('Y-m'); // Group by Year-Month
        })->map(function ($row) {
            return $row->sum('quantity');
        })->sortKeys();

        // Fill missing months for better chart
        $trends = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i)->format('Y-m');
            $trends['labels'][] = \Carbon\Carbon::parse($date)->format('M Y');
            $trends['data'][] = $trendData[$date] ?? 0;
        }

        // 2. Warehouse Distribution
        $warehouses = $movements->whereNotNull('warehouse')->groupBy('warehouse')->map->count();
        $whChart = [
            'labels' => $warehouses->keys(),
            'data' => $warehouses->values()
        ];

        // 3. Top Clients
        $topClients = $movements->groupBy('bl.client_name')
            ->map(function ($group) {
                return $group->sum('quantity');
            })
            ->sortDesc()
            ->take(5);

        return view('tools.logistics.articles.show', compact('name', 'movements', 'stats', 'product', 'trends', 'whChart', 'topClients'));
    }

    // NEW: Global Catalog Product Edit
    public function updateCatalogProduct(Request $request) {
        $product = CatalogProduct::where('name', $request->name)->firstOrFail();
        
        $product->update([
            'default_conversion' => $request->conversion,
            'default_boxes_per_pallet' => $request->boxes_per_pallet,
            'default_warehouse' => $request->warehouse
        ]);
        
        return back()->with('success', 'Fiche produit mise à jour.');
    }
}
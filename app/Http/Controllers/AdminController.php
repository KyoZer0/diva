<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\Invoice;
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
        $totalInvoices = Invoice::count();

        $recentClients = Client::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalClients', 'totalReps', 'totalInvoices', 'recentClients'
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

    public function analytics()
    {
        // Get all clients for admin analytics
        $clients = Client::with('user')->get();

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

        // Rep performance data
        $reps = User::whereHas('roles', function($query) {
            $query->where('name', 'rep');
        })->with(['clients'])->get();

        $repStats = $reps->map(function($rep) {
            $clients = $rep->clients;
            return [
                'rep' => $rep,
                'total_clients' => $clients->count(),
                'customers' => $clients->where('status', 'customer')->count(),
                'prospects' => $clients->where('status', 'prospect')->count(),
                'leads' => $clients->where('status', 'lead')->count(),
            ];
        });

        return view('analytics.index', compact('totalClients', 'sources', 'cities', 'statusDistribution', 'conversionRate', 'repStats'));
    }
}
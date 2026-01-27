<?php

namespace App\Services;

use App\Models\SalesClient;
use App\Models\Bl;
use Carbon\Carbon;

class SmartSuggestionService
{
    /**
     * Get suggestions for a specific sales rep (user)
     */
    public function getSuggestionsForRep($userId)
    {
        $suggestions = [];

        // 1. Get rep's clients
        $myClients = SalesClient::where('user_id', $userId)->get();

        // 2. Fetch recent "New Arrival" BLs (last 7 days)
        // Assuming 'status' indicating new arrival effectively, or just recent BLs
        // In the prompt, it says "if a new arrival comes in". 
        // We'll look for BLs created recently.
        $recentBls = Bl::where('created_at', '>=', Carbon::now()->subDays(7))
            ->with(['articles'])
            ->get();

        // 3. Logic: Match clients to these new arrivals
        foreach ($recentBls as $bl) {
            foreach ($myClients as $client) {
                
                // Strategy A: Name Match (Direct)
                // If the BL literally belongs to this client (string match on name)
                if (strcasecmp($bl->client_name, $client->name) === 0 || 
                    strcasecmp($bl->client_name, $client->company_name) === 0) {
                    
                    $suggestions[] = [
                        'type' => 'logistics_update',
                        'priority' => 'high',
                        'client' => $client,
                        'message' => "New Delivery processed for {$client->getDisplayNameAttribute()}",
                        'data' => $bl,
                        'action' => 'Call to confirm receipt'
                    ];
                    continue; // Found a match for this BL/Client combo
                }

                // Strategy B: Interest Match (Smart)
                // If the BL contains items that match the client's interest tags
                // And the BL is NOT for this client (it's new stock arriving for someone else or general stock)
                // *Note: The current BL model puts a 'client_name' on everything, assuming it's a delivery.
                // If we treat every BL as "Stock Arrival" we might want to check if it's a Supplier BL? 
                // The Bl model has 'supplier_name'. If 'client_name' is empty/null, it might be an inbound shipment?
                // Let's assume ANY recent BL represents activity we can piggyback on, or specifically Supplier BLs if they exist.
                // For now, let's look for "Opportunity" based on what OTHERS are buying (Trending)
                // OR based on simple "Stock Arrival" if we can distinguish that.
                
                // Let's implement a simpler "Interest Match" based on the articles in the BL
                if ($client->interest_tags) {
                    foreach ($bl->articles as $article) {
                        foreach ($client->interest_tags as $tag) {
                            if (stripos($article->name, $tag) !== false) {
                                $suggestions[] = [
                                    'type' => 'opportunity',
                                    'priority' => 'medium',
                                    'client' => $client,
                                    'message' => "New stock arrival: {$article->name} matches interest '{$tag}'",
                                    'data' => $bl,
                                    'action' => 'Inform client of new stock'
                                ];
                                break 2; // Stop checking articles for this client/BL combo
                            }
                        }
                    }
                }
            }
        }

        // 4. Sort by priority
        usort($suggestions, function($a, $b) {
            $scores = ['high' => 3, 'medium' => 2, 'low' => 1];
            return $scores[$b['priority']] <=> $scores[$a['priority']];
        });

        return array_slice($suggestions, 0, 10); // Return top 10
    }
}

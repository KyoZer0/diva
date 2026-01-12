<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\Bl;

Schedule::call(function () {
    // Find all BLs that are still 'loading' from today
    $openBls = Bl::whereDate('date', today())
                 ->where('status', 'loading')
                 ->get();

    foreach ($openBls as $bl) {
        // Option A: Auto-mark as Loaded (assuming they forgot)
        $bl->update(['status' => 'loaded']);
        $bl->log('system', 'Fermeture automatique journalière (21h00).');
        
        // Option B: If you want a specific "Closed" status, add it to your enum first.
    }
})->dailyAt('21:00')->timezone('Africa/Casablanca');
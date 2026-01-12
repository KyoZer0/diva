<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bl extends Model
{
    use HasFactory;

    protected $fillable = [
        'bl_number', 
        'client_name', 
        'date', 
        'status',
        'supplier_name',
        'supplier_ref',
        'supplier_photo'
    ];
    
    public function history()
    {
        return $this->hasMany(BlHistory::class)->latest();
    }
    
    // Helper to log activity
    public function log($action, $details)
    {
        $this->history()->create([
            'user_id' => auth()->id() ?? null,
            'action' => $action,
            'details' => $details
        ]);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
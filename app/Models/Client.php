<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'client_type',
        'professional_category', // NEW
        'company_name',
        'phone',
        'email',
        'city',
        'source',
        'products',
        'style',
        'conseiller',
        'devis_demande',
        'notes',
        'status',
        'last_contact_date',
        'potential_score', // NEW
        'smart_status', // NEW
        'last_interaction_at' // NEW
    ];
    
    protected $casts = [
        'products' => 'array',
        'style' => 'array',
        'devis_demande' => 'boolean',
        'last_contact_date' => 'date',
        'last_interaction_at' => 'datetime',
        'potential_score' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the latest BL for this client (by name matching).
     */
    public function getLatestBlAttribute()
    {
        // Try matching by full_name OR company_name
        return \App\Models\Bl::where(function($q) {
            $q->where('client_name', $this->full_name);
            if ($this->company_name) {
                $q->orWhere('client_name', $this->company_name);
            }
        })->latest('date')->first();
    }

    /**
     * Smart Last Contact: Returns the latest of 'last_interaction_at' OR 'latest_bl_date'.
     */
    public function getLastContactAttribute()
    {
        $manual = $this->last_interaction_at;
        $system = $this->latest_bl?->created_at; // OR date from BL

        if (!$manual && !$system) return $this->last_contact_date; // Fallback to legacy date
        if (!$manual) return $system;
        if (!$system) return $manual;

        return $manual > $system ? $manual : $system;
    }

    public function getDisplayNameAttribute()
    {
        if ($this->company_name) {
             return "{$this->company_name} ({$this->full_name})";
        }
        return $this->full_name;
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->city,
            // Add other address fields if they exist in the model
        ]);
        return implode(', ', $parts);
    }
}

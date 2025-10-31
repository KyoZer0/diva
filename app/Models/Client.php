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
    'company_name',
    'phone',
    'email',
    'city',
    'source',
    'products',
    'conseiller',
    'devis_demande',
    'notes',
    'status',
    'last_contact_date',
    ];
    
    protected $casts = [
        'products' => 'array',
        'devis_demande' => 'boolean',
        'last_contact_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');

    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getDisplayNameAttribute()
    {
        if ($this->client_type === 'company' && $this->company_name) {
            return $this->company_name . ($this->contact_person ? ' (' . $this->contact_person . ')' : '');
        }
        return $this->name;
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->postal_code,
            $this->country
        ]);
        return implode(', ', $parts);
    }
    public function getProductsAttribute($value)
{
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }
    return $value ?: [];
}

}

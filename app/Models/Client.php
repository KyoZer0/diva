<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client_type',
        'company_name',
        'contact_person',
        'contact',
        'phone',
        'email',
        'city',
        'address',
        'postal_code',
        'country',
        'source',
        'likes',
        'notes',
        'status',
        'budget_range',
        'last_contact_date',
        'user_id'
    ];

    protected $casts = [
        'budget_range' => 'decimal:2',
        'last_contact_date' => 'date'
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

}

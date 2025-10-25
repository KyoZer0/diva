<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'invoice_number',
        'invoice_date',
        'amount',
        'currency',
        'status',
        'description',
        'image_path'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

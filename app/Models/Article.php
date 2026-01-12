<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    // ADD 'status' and 'quantity_delivered' HERE:
    protected $fillable = [
        'bl_id', 
        'name', 
        'reference', 
        'quantity', 
        'unit', 
        'status', 
        'quantity_delivered',
        'warehouse',
        'boxes_per_pallet',
        'pallet_count'
    ];

    public function bl()
    {
        return $this->belongsTo(Bl::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reference',
        'unit',
        'default_warehouse',
        'default_conversion',
        'default_boxes_per_pallet'
    ];
}
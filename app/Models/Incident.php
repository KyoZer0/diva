<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = ['bl_id', 'article_name', 'quantity', 'notes', 'date', 'status'];

    public function bl()
    {
        return $this->belongsTo(Bl::class);
    }
}
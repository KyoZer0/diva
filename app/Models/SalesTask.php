<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTask extends Model
{
    protected $fillable = ['user_id', 'title', 'type', 'is_completed', 'position'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

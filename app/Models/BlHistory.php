<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlHistory extends Model
{
    use HasFactory;
    protected $fillable = ['bl_id', 'user_id', 'action', 'details'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
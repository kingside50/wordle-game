<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'friend_id',
    ];

    // Relatie om de vriend van dit model op te halen
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id', 'id');
    }
}
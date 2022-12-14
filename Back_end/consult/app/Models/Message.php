<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    protected function scopeFilter($query, $filters)
    {
        $query-> when($filters['chat'] ?? false,fn($query, $chat) =>
        $query-> whereHas('chat', fn($query) =>
                    $query->where('id', $chat)
                )
        );

    }
}

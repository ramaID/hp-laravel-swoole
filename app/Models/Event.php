<?php

// app/Models/Event.php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * This simulates a complex, time-consuming query.
     */
    public function scopeOfType(Builder $query, string $type)
    {
        sleep(1); // Simulate a 1-second query time

        return $query->where('type', $type)
            ->orderBy('date', 'desc')
            ->limit(5);
    }
}

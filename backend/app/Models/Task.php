<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'statement',
        'task_date',
        'order',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'order' => 'integer',
        'task_date' => 'date',
        'created_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('task_date', $date);
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where('statement', 'ILIKE', '%'.$searchTerm.'%');
    }
}

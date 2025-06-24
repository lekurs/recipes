<?php

namespace App\Models;

use App\Enums\AnswerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'comment',
        'question',
        'responded_at',
        'recipe_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => AnswerStatus::class,
            'responded_at' => 'datetime',
        ];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

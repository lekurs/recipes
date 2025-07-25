<?php

namespace App\Models;

use App\Enums\RecipeStatus;
use App\Enums\RecipeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'description',
        'project_id',
        'status',
        'file_path'
    ];

    protected function casts(): array
    {
        return [
            'type' => RecipeType::class,
            'status' => RecipeStatus::class,
        ];
    }

    public function getStatusColor()
    {
        return $this->status->badgeColor();
    }

    public function getStatusLabel()
    {
        return $this->status->label();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(RecipeFile::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}

<?php

namespace App\Models;

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
    ];

//    protected function casts(): array
//    {
//        return [
//            'type' => RecipeType::class,
//        ];
//    }

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

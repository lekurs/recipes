<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class AnswerFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_name',
        'size',
        'answer_id',
        'mime_type',
    ];

    public function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }

    public function getFileUrl()
    {
        return Storage::url('answer-files/' . $this->filename);
    }
}

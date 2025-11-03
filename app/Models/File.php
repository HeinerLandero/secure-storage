<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_name',
        'path',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Get the user who uploaded this file.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

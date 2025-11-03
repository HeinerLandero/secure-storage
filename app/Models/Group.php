<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'quota',
    ];

    protected $casts = [
        'quota' => 'integer',
    ];

    /**
     * Get the users that belong to this group.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the files uploaded by users in this group.
     */
    public function files()
    {
        return $this->hasManyThrough(File::class, User::class, 'group_id', 'user_id', 'id', 'id');
    }
}

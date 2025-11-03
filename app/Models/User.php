<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'group_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get a temporary plain password for display purposes (development only)
     */
    public function getPlainPasswordAttribute()
    {
        // For development/demo purposes only
        // In production, passwords should NEVER be displayed in plain text
        if (app()->environment('local', 'development')) {
            // Generate a simple temporary password based on user info
            return 'Temp' . substr($this->email, 0, 3) . '123!';
        }
        return null;
    }

    /**
     * Get the group this user belongs to.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get all files uploaded by this user.
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user.
     */
    public function isUser(): bool
    {
        return $this->role === 'usuario';
    }

    /**
     * Get the total storage used by this user.
     */
    public function getStorageUsed(): int
    {
        return $this->files()->sum('size');
    }

    /**
     * Get user's storage quota (user-specific, group-specific, or global).
     */
    public function getStorageQuota(): int
    {
        // First check for user-specific quota (this could be added to user model later)
        // For now, check group quota or global default

        if ($this->group && $this->group->quota > 0) {
            return $this->group->quota;
        }

        // Get global quota from configurations
        $globalQuota = \App\Models\Configuration::getValue('cuota_global', '10485760'); // Default 10MB
        return (int) $globalQuota;
    }

    /**
     * Check if user can upload a file of given size.
     */
    public function canUpload(int $fileSize): bool
    {
        $quota = $this->getStorageQuota();
        $used = $this->getStorageUsed();

        return ($used + $fileSize) <= $quota;
    }

    /**
     * Get remaining storage quota.
     */
    public function getRemainingQuota(): int
    {
        return $this->getStorageQuota() - $this->getStorageUsed();
    }
}

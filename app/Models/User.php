<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'cloudflare_api_key',
        'cloudflare_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cloudflare_api_key' => 'encrypted',
        'cloudflare_token' => 'encrypted',
    ];

    /**
     * Related domains for user
     *
     * @return HasMany
     */
    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeHasCredentials(Builder $builder): Builder
    {
        return $builder->whereNotNull('cloudflare_token')->orWhereNotNull('cloudflare_api_key');
    }

    /**
     * @return bool
     */
    public function getHasCredentialsAttribute(): bool
    {
        return $this->cloudflare_token || $this->cloudflare_api_key;
    }
}

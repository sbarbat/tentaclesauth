<?php

namespace App\Models;

use Database\Factories\SocialConnectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialConnection extends Model
{
    /** @use HasFactory<SocialConnectionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'provider',
        'provider_account_id',
        'provider_account_name',
        'access_token',
        'token_expires_at',
        'refresh_token',
        'refresh_token_expires_at',
        'webhook_id',
        'webhook_secret',
        'error_code',
        'error_message',
        'meta',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'access_token',
        'refresh_token',
        'webhook_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'token_expires_at' => 'datetime',
            'refresh_token' => 'encrypted',
            'refresh_token_expires_at' => 'datetime',
            'webhook_secret' => 'encrypted',
            'meta' => 'array',
        ];
    }

    /**
     * The team that owns this connection.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Determine if the connection's access token has expired.
     */
    public function isTokenExpired(): bool
    {
        return $this->token_expires_at !== null && $this->token_expires_at->isPast();
    }

    /**
     * Determine if a webhook has been registered for this connection.
     */
    public function hasWebhook(): bool
    {
        return ! empty($this->webhook_id);
    }
}

<?php

namespace MyDaniel\TemporaryTokens\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Class TemporaryToken
 *
 * Represents a temporary token in the database.
 *
 * @property string $id
 * @property string|null $type
 * @property string $token
 * @property int $usage_count
 * @property int $max_usage_limit
 * @property array|null $metadata
 * @property string|null $tokenable_type
 * @property string|null $tokenable_id
 * @property Carbon|null $expires_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Model|null $tokenable
 */
class TemporaryToken extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'token',
        'usage_count',
        'max_usage_limit',
        'metadata',
        'expires_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'usage_count' => 'integer',
        'max_usage_limit' => 'integer',
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the table associated with the model.
     * Reads the table name from the configuration file.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('temporary-tokens.table_name', 'temporary_tokens');
    }

    /**
     * Get the parent tokenable model (e.g., a User).
     *
     * @return MorphTo
     */
    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Mark the token as used by incrementing its usage count.
     *
     * @return $this
     */
    public function use(): self
    {
        $this->increment('usage_count');

        return $this;
    }

    /**
     * Determine if the token has been used at least once.
     *
     * @return bool
     */
    public function hasUsed(): bool
    {
        return $this->usage_count > 0;
    }

    /**
     * Determine if the token has expired.
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        return !($this->expires_at === null) && now()->gt($this->expires_at);
    }

    /**
     * Force the token to expire immediately.
     *
     * @return $this
     */
    public function markAsExpired(): self
    {
        $this->forceFill(['expires_at' => now()])->save();

        return $this;
    }

    /**
     * Determine if the token has a usage limit.
     * A limit of 0 or fewer means unlimited usage.
     *
     * @return bool
     */
    public function hasMaxUsageLimit(): bool
    {
        return $this->max_usage_limit > 0;
    }

    /**
     * Determine if the token's usage count has reached its limit.
     *
     * @return bool
     */
    public function hasExceedMaxUsage(): bool
    {
        return $this->hasMaxUsageLimit() && ($this->usage_count >= $this->max_usage_limit);
    }

    /**
     * Determine if the token is currently valid (not expired and not over its usage limit).
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return !($this->hasExpired() || $this->hasExceedMaxUsage());
    }

    /**
     * Find a valid token by its value and type.
     *
     * @param  string  $token
     * @param  string|null  $type
     *
     * @return self|null
     */
    public static function findValid(string $token, ?string $type = null): ?self
    {
        $query = static::where('token', $token);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->valid()->first();
    }

    /**
     * Scope a query to only include valid tokens.
     *
     * @param  Builder  $query  The query builder instance.
     *
     * @return Builder
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $query) { // check usage limit
                return $query
                    ->where('max_usage_limit', '=', 0) // Unlimited
                    ->orWhereRaw('usage_count < max_usage_limit');
            })
            ->where(function (Builder $query) { // check expiration time
                return $query
                    ->whereNull('expires_at') // Never expires
                    ->orWhere('expires_at', '>', now());
            });
    }
}

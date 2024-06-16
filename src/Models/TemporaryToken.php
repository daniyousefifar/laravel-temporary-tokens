<?php

namespace Zirsakht\TemporaryTokens\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\Builder;

class TemporaryToken extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'temporary_tokens';

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
     * @var array
     */
    protected $casts = [
        'usage_count' => 'integer',
        'max_usage_limit' => 'integer',
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Retrieve related model.
     *
     * @return MorphTo
     */
    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Mark token as used.
     *
     * @return $this
     */
    public function use(): self
    {
        $this->increment('usage_count');

        return $this;
    }

    /**
     * Determine if token has used.
     *
     * @return bool
     */
    public function hasUsed(): bool
    {
        return $this->usage_count > 0;
    }

    /**
     * Determine if token has expired.
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        return $this->expires_at === null ? false : now()->gt($this->expires_at);
    }

    /**
     * Mark token as expired.
     *
     * @return $this
     */
    public function markAsExpired(): self
    {
        $this->forceFill(['expires_at' => now()])->save();

        return $this;
    }

    /**
     * Determine usage limit is enabled.
     *
     * @return bool
     */
    public function hasMaxUsageLimit(): bool
    {
        return $this->max_usage_limit > 0;
    }

    /**
     * Determine usage limit has exceed.
     *
     * @return bool
     */
    public function hasExceedMaxUsage(): bool
    {
        $maxUsageLimit = $this->max_usage_limit;
        $usageCount = $this->usage_count;

        return $this->hasMaxUsageLimit() && ($usageCount >= $maxUsageLimit);
    }

    /**
     * Determine token is valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return !($this->hasExpired() || $this->hasExceedMaxUsage());
    }

    /**
     * Filter valid tokens.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $query) { // check usage limit
                return $query
                    ->where('max_usage_limit', '=', 0)
                    ->orWhereRaw('usage_count < max_usage_limit');
            })
            ->where(function (Builder $query) { // check expiration time
                return $query
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
}

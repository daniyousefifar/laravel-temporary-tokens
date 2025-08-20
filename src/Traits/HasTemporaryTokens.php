<?php

namespace MyDaniel\TemporaryTokens\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use MyDaniel\TemporaryTokens\Models\TemporaryToken;
use MyDaniel\TemporaryTokens\TokenBuilder;

/**
 * Trait HasTemporaryTokens
 *
 * Provides functionality for an Eloquent model to have and manage temporary tokens.
 *
 * @mixin Model
 */
trait HasTemporaryTokens
{
    /**
     * Get all of the temporary tokens for this model.
     *
     * @return mixed
     */
    public function temporaryTokens(): MorphMany
    {
        return $this->morphMany(TemporaryToken::class, 'tokenable');
    }

    /**
     * Get a new TokenBuilder instance for this model.
     * This presets the tokenable entity on the builder.
     *
     * @return TokenBuilder
     */
    public function temporaryTokenBuilder(): TokenBuilder
    {
        $tokenBuilder = new TokenBuilder();
        $tokenBuilder->setTokenable($this);

        return $tokenBuilder;
    }
}

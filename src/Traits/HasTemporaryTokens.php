<?php

namespace Zirsakht\TemporaryTokens\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Zirsakht\TemporaryTokens\Models\TemporaryToken;
use Zirsakht\TemporaryTokens\TokenBuilder;

trait HasTemporaryTokens
{
    /**
     * Get all the temporary tokens.
     *
     * @return mixed
     */
    public function temporaryTokens(): MorphMany
    {
        return $this->morphMany(TemporaryToken::class, 'tokenable');
    }

    public function temporaryTokenBuilder(): TokenBuilder
    {
        $tokenBuilder = new TokenBuilder();

        $tokenBuilder->setTokenable($this);

        return $tokenBuilder;
    }
}

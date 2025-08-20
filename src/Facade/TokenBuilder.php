<?php

namespace MyDaniel\TemporaryTokens\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method self setToken(string $token)
 * @method string|null getToken()
 * @method self setType(string $type)
 * @method string|null getType()
 * @method self setMetadata(array $data)
 * @method array getMetadata()
 * @method self setExpireDate(\Illuminate\Support\Carbon $date)
 * @method \Illuminate\Support\Carbon|null getExpireDate()
 * @method self setUsageLimit(int $limit)
 * @method int getUsageLimit()
 * @method self setTokenable(\Illuminate\Database\Eloquent\Model $entity)
 * @method \Illuminate\Database\Eloquent\Model getTokenable()
 * @method self setTokenLength(int $length)
 * @method int getTokenLength()
 * @method \MyDaniel\TemporaryTokens\Models\TemporaryToken build()
 */
class TokenBuilder extends Facade
{
    /**
     * Get the registered name of the component.

     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'laravel-temporary-tokens-builder';
    }
}

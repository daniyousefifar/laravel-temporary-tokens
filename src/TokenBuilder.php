<?php

namespace MyDaniel\TemporaryTokens;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Random\RandomException;
use MyDaniel\TemporaryTokens\Models\TemporaryToken;

/**
 * Class TokenBuilder
 *
 * A fluent builder class for creating and configuring TemporaryToken instances.
 */
class TokenBuilder
{
    /**
     * The custom token string.
     * If not set, a random token will be generated.
     *
     * @var string|null
     */
    protected ?string $token = null;

    /**
     * The type or category of the token (e.g., 'password-reset', 'email-verification').
     *
     * @var string|null
     */
    protected ?string $type = null;

    /**
     * Arbitrary data to store with the token.
     *
     * @var mixed|array
     */
    protected mixed $metadata = [];

    /**
     * The expiration date and time for the token.
     *
     * @var Carbon|null
     */
    protected ?Carbon $expiresAt = null;

    /**
     * The Eloquent model this token belongs to.
     *
     * @var Model|null
     */
    protected ?Model $tokenable = null;

    /**
     * The maximum number of times the token can be used.
     *
     * @var int
     */
    protected int $usageLimit = 1;

    /**
     * The length of the randomly generated numeric token.
     *
     * @var int
     */
    protected int $length;

    /**
     * TokenBuilder constructor.
     * Initializes the builder and sets the default token length from the config.
     */
    public function __construct()
    {
        $this->length = config('temporary-tokens.default_token_length', 6);
    }

    /**
     * Set a custom token string.
     *
     * @param  string  $token  The token string.
     *
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Retrieve the current token string.
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Set the token type.
     *
     * @param  string  $type  The type of the token.
     *
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Retrieve the token type.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set metadata for the token.
     *
     * @param  array<string, mixed>  $data  The metadata array.
     *
     * @return $this
     */
    public function setMetadata(array $data): self
    {
        $this->metadata = $data;

        return $this;
    }

    /**
     * Retrieve the metadata.
     *
     * @return array<string, mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Set the expiration date.
     *
     * @param  Carbon  $date  The expiration date.
     *
     * @return $this
     */
    public function setExpireDate(Carbon $date): self
    {
        $this->expiresAt = $date;

        return $this;
    }

    /**
     * Retrieve the expiration date.
     *
     * @return Carbon|null
     */
    public function getExpireDate(): ?Carbon
    {
        return $this->expiresAt;
    }

    /**
     * Set the maximum usage limit.
     *
     * @param  int  $limit  The usage limit.
     *
     * @return $this
     */
    public function setUsageLimit(int $limit): self
    {
        $this->usageLimit = $limit;

        return $this;
    }

    /**
     * Retrieve the maximum usage limit.
     *
     * @return int
     */
    public function getUsageLimit(): int
    {
        return $this->usageLimit;
    }

    /**
     * Set the related Eloquent model instance.
     *
     * @param  Model  $entity  The model instance.
     *
     * @return $this
     */
    public function setTokenable(Model $entity): self
    {
        $this->tokenable = $entity;

        return $this;
    }

    /**
     * Retrieve the related Eloquent model instance.
     *
     * @return Model|null
     */
    public function getTokenable(): ?Model
    {
        return $this->tokenable;
    }

    /**
     * Set the token length for random generation.
     *
     * @param  int  $length  The desired length.
     *
     * @return $this
     */
    public function setTokenLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Retrieve the token length.
     *
     * @return int
     */
    public function getTokenLength(): int
    {
        return $this->length;
    }

    /**
     * Generate a random integer of a given length.
     *
     * @param  int  $length  The length of the desired integer.
     *
     * @return int
     *
     * @throws RandomException If a cryptographically secure source of randomness is not available.
     */
    private function generateRandomInt(int $length): int
    {
        return random_int(10 ** ($length - 1) + 1, (10 ** $length) - 1);
    }

    /**
     * Build a new, unsaved TemporaryToken instance with the configured attributes.
     *
     * @return TemporaryToken
     *
     * @throws RandomException
     */
    public function build(): TemporaryToken
    {
        $attributes = [
            'token' => $this->getToken() ?? $this->generateRandomInt($this->getTokenLength()),
            'type' => $this->getType(),
            'max_usage_limit' => $this->getUsageLimit(),
            'metadata' => $this->getMetadata(),
            'expired_at' => $this->getExpireDate(),
        ];

        if ($this->getTokenable() instanceof Model) {
            return $this->getTokenable()->temporaryTokens()->make($attributes);
        }

        return new TemporaryToken($attributes);
    }

    /**
     * Build and persist the new TemporaryToken to the database.
     *
     * @return TemporaryToken
     *
     * @throws RandomException
     */
    public function create(): TemporaryToken
    {
        $token = $this->build();
        $token->save();

        return $token;
    }
}

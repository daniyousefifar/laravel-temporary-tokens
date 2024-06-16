<?php

namespace Zirsakht\TemporaryTokens;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Zirsakht\TemporaryTokens\Models\TemporaryToken;
use Random\RandomException;

class TokenBuilder
{
    protected ?string $token = null;

    protected ?string $type = null;

    protected mixed $metadata = [];

    protected ?Carbon $expiresAt = null;

    protected ?Model $tokenable = null;

    protected int $usageLimit = 1;

    protected int $length = 6;

    public function __construct()
    {
        // TODO
    }

    /**
     * Set token.
     *
     * @param string $token
     *
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Retrieve token.
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Retrieve type.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set metadata
     *
     * @param array $data
     *
     * @return $this
     */
    public function setMetadata(array $data): self
    {
        $this->metadata = $data;

        return $this;
    }

    /**
     * Retrieve metadata
     *
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Set expiration date.
     *
     * @param Carbon $date
     *
     * @return $this
     */
    public function setExpireDate(Carbon $date): self
    {
        $this->expiresAt = $date;

        return $this;
    }

    /**
     * Retrieve expiration date.
     *
     * @return Carbon|null
     */
    public function getExpireDate(): ?Carbon
    {
        return $this->expiresAt;
    }

    /**
     * Set max usage count.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function setUsageLimit(int $limit): self
    {
        $this->usageLimit = $limit;

        return $this;
    }

    /**
     * Retrieve max usage count.
     *
     * @return int
     */
    public function getUsageLimit(): int
    {
        return $this->usageLimit;
    }

    /**
     * Set related Eloquent model instance.
     *
     * @param Model $entity
     *
     * @return $this
     */
    public function setTokenable(Model $entity): self
    {
        $this->tokenable = $entity;

        return $this;
    }

    /**
     * Retrieve related Eloquent model instance.
     *
     * @return Model|null
     */
    public function getTokenable(): ?Model
    {
        return $this->tokenable;
    }

    /**
     * Set token length.
     *
     * @param int $length
     *
     * @return $this
     */
    public function setTokenLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Retrieve token length.
     *
     * @return int
     */
    public function getTokenLength(): int
    {
        return $this->length;
    }

    /**
     * Generate a random number.
     *
     * @param int $length
     *
     * @return int
     *
     * @throws RandomException
     */
    protected function generateRandomInt(int $length): int
    {
        return random_int(10 ** ($length - 1) + 1, (10 ** $length) - 1);
    }

    public function build(): TemporaryToken
    {
        $payload = [
            'token' => $this->getToken() ?? $this->generateRandomInt($this->getTokenLength()),
            'type' => $this->getType(),
            'max_usage_limit' => $this->getUsageLimit(),
            'metadata' => $this->getMetadata(),
            'expires_at' => $this->getExpireDate(),
        ];

        if ($this->getTokenable() instanceof Model) {
            $token = $this->getTokenable()->temporaryTokens()->create($payload);
        } else {
            $token = TemporaryToken::create($payload);
        }

        return $token;
    }
}

<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use TTBooking\WBEngine\Casts\Query;
use TTBooking\WBEngine\Casts\Result;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;

/**
 * @template TResult of ResultInterface
 *
 * @property string $uuid
 * @property string $session_uuid
 * @property string $base_uri
 * @property string $endpoint
 * @property QueryInterface<TResult> $query
 * @property TResult $result
 * @property array<string, mixed>|null $attrs
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection<int, static> $session
 *
 * @implements StorableState<TResult>
 */
class State extends Model implements StorableState
{
    use HasUuids;

    protected $table = 'wbeng_state';

    protected $primaryKey = 'uuid';

    protected $casts = [
        'query' => Query::class,
        'result' => Result::class,
        'attrs' => 'array',
    ];

    /**
     * @return HasMany<static>
     */
    public function session(): HasMany
    {
        return $this->hasMany(static::class, 'session_uuid', 'session_uuid');
    }

    public function setId(string $id): static
    {
        $this->uuid = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->uuid;
    }

    public function setSessionId(string $sessionId): static
    {
        $this->session_uuid = $sessionId;

        return $this;
    }

    public function getSessionId(): ?string
    {
        return $this->session_uuid;
    }

    public function setBaseUri(string $baseUri): static
    {
        $this->base_uri = $baseUri;

        return $this;
    }

    public function getBaseUri(): string
    {
        return $this->base_uri;
    }

    public function setLegacy(bool $legacy = true): static
    {
        $this->attrs[self::ATTR_LEGACY] = $legacy;

        return $this;
    }

    public function isLegacy(): bool
    {
        return (bool) ($this->attrs[self::ATTR_LEGACY] ?? true);
    }

    public function setQuery(QueryInterface $query): static
    {
        $this->query = $query;

        return $this;
    }

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }

    public function setResult(ResultInterface $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function getResult(): ResultInterface
    {
        return $this->result;
    }

    public function setAttrs(array $attributes): static
    {
        foreach ($attributes as $attribute => $value) {
            $this->setAttr($attribute, $value);
        }

        return $this;
    }

    public function getAttrs(): array
    {
        return [
            self::ATTR_LEGACY => $this->isLegacy(),
            self::ATTR_SESSION_ID => $this->getSessionId(),
        ] + ($this->attrs ?? []);
    }

    public function setAttr(string $attribute, mixed $value): static
    {
        match ($attribute) {
            self::ATTR_LEGACY => $this->setLegacy($value), // @phpstan-ignore-line
            self::ATTR_SESSION_ID => $this->setSessionId($value), // @phpstan-ignore-line
            default => $this->attrs[$attribute] = $value,
        };

        return $this;
    }

    public function getAttr(string $attribute, mixed $default = null): mixed
    {
        return match ($attribute) {
            self::ATTR_LEGACY => $this->isLegacy(),
            self::ATTR_SESSION_ID => $this->getSessionId(),
            default => $this->attrs[$attribute] ?? null,
        };
    }
}

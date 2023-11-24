<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use TTBooking\WBEngine\Casts\Query;
use TTBooking\WBEngine\Casts\Result;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;

/**
 * @property non-empty-string $uuid
 * @property non-empty-string|null $parent_uuid
 * @property string $base_uri
 * @property string $endpoint
 * @property QueryInterface $query
 * @property ResultInterface $result
 * @property array|null $appendix
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property static|null $parent
 * @property Collection<int, static> $children
 */
class State extends Model
{
    use HasUuids;

    protected $table = 'wbeng_state';

    protected $primaryKey = 'uuid';

    protected $casts = [
        'query' => Query::class,
        'result' => Result::class,
        'appendix' => 'array',
    ];

    /**
     * @return BelongsTo<static, static>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }

    /**
     * @return HasMany<static>
     */
    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_uuid');
    }
}

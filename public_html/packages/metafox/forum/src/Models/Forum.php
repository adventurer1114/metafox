<?php

namespace MetaFox\Forum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MetaFox\Forum\Repositories\ForumRepositoryInterface;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasAmounts;
use MetaFox\Platform\Contracts\HasTitle;
use MetaFox\Platform\Contracts\HasUrl;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Traits\Eloquent\Model\HasAmountsTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * @property mixed  $title
 * @property Forum  $subForums
 * @property Forum  $parentForums
 * @property int    $parent_id
 * @property string $description
 * @property int    $level
 */
class Forum extends Model implements
    Entity,
    HasAmounts,
    HasTitle,
    HasUrl
{
    use HasEntity;
    use HasAmountsTrait;
    use SoftDeletes;

    public const ENTITY_TYPE = 'forum';

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'parent_id',
        'description',
        'ordering',
        'level',
        'total_thread',
        'is_closed',
        'total_comment',
        'total_sub',
    ];

    protected $casts = [
        'level'        => 'integer',
        'total_thread' => 'integer',
        'ordering'     => 'integer',
    ];

    public function threads(): HasMany
    {
        return $this->hasMany(ForumThread::class, 'forum_id');
    }

    public function subForums(): ?HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->orderBy('ordering');
    }

    public function parentForums(): ?BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id')
            ->withTrashed();
    }

    public function toTitle(): string
    {
        return $this->title;
    }

    public function getParentId(): int
    {
        return $this->parent_id;
    }

    public function getTotalThread(): int
    {
        return $this->total_thread;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function toLink(): ?string
    {
        return url_utility()->makeApiUrl('forum/' . $this->entityId() . '/' . $this->toSlug());
    }

    public function toUrl(): ?string
    {
        return url_utility()->makeApiFullUrl('forum/' . $this->entityId() . '/' . $this->toSlug());
    }

    public function toRouter(): ?string
    {
        return url_utility()->makeApiMobileUrl('forum/' . $this->entityId());
    }

    public function toSubLinkAdminCP(): string
    {
        return url_utility()->makeApiUrl('admincp/forum/forum/browse?parent_id=' . $this->entityId());
    }

    protected function toSlug(): string
    {
        $title = Arr::get($this->attributes, 'title');

        if (null === $title) {
            return MetaFoxConstant::EMPTY_STRING;
        }

        return Str::slug($title);
    }

    public function getSeoDataAttribute(): array
    {
        $generalBreadcrumbs = [
            ['label' => __p('forum::web.forums'), 'to' => '/forum'],
        ];

        $modelBreadcrumbs = resolve(ForumRepositoryInterface::class)->getBreadcrumbs($this->entityId());

        if (count($modelBreadcrumbs)) {
            $generalBreadcrumbs = array_merge($generalBreadcrumbs, $modelBreadcrumbs);
        }

        return [
            'breadcrumbs' => $generalBreadcrumbs,
        ];
    }
}

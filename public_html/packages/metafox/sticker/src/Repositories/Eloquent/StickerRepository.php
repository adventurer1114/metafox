<?php

namespace MetaFox\Sticker\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Validation\ValidationException;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Sticker\Models\Sticker;
use MetaFox\Sticker\Policies\StickerSetPolicy;
use MetaFox\Sticker\Repositories\StickerRepositoryInterface;
use MetaFox\Sticker\Repositories\StickerSetRepositoryInterface;
use MetaFox\Sticker\Support\Browse\Scopes\RecentScope;

/**
 * Class StickerRepository.
 * @method Sticker find($id, $columns = ['*'])
 * @method Sticker getModel()
 * @ignore
 * @codeCoverageIgnore
 */
class StickerRepository extends AbstractRepository implements StickerRepositoryInterface
{
    public function model(): string
    {
        return Sticker::class;
    }

    /**
     * @param  User                                       $context
     * @param  array<string, mixed>                       $attributes
     * @return Paginator
     * @throws ValidationException|AuthorizationException
     */
    public function viewStickers(User $context, array $attributes): Paginator
    {
        return $this->getStickerSetRepository()->getStickers($context, $attributes);
    }

    /**
     * @param  User                                         $context
     * @param  int                                          $id
     * @return bool
     * @throws ValidationException | AuthorizationException
     */
    public function deleteSticker(User $context, int $id): bool
    {
        return $this->getStickerSetRepository()->deleteSticker($context, $id);
    }

    /**
     * @param  User                   $context
     * @param  array<string, mixed>   $attributes
     * @return Paginator
     * @throws AuthorizationException
     */
    public function viewRecentStickers(User $context, array $attributes): Paginator
    {
        policy_authorize(StickerSetPolicy::class, 'viewAny', $context);

        return $this->getModel()
            ->newModelQuery()
            ->addScope(new RecentScope())
            ->simplePaginate($attributes['limit']);
    }

    protected function getStickerSetRepository(): StickerSetRepositoryInterface
    {
        return resolve(StickerSetRepositoryInterface::class);
    }
}

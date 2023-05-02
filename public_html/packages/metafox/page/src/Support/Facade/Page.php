<?php

namespace MetaFox\Page\Support\Facade;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use MetaFox\Page\Contracts\PageContract;
use MetaFox\Platform\Contracts\User;

/**
 * @method static array      getMentions(string $content)
 * @method static Collection getPagesForMention(array $ids)
 * @method static Builder    getPageBuilder(User $user)
 * @method static array      getListTypes()
 * @method static bool       isFollowing(User $context, User $user)
 */
class Page extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PageContract::class;
    }
}

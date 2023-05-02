<?php

namespace MetaFox\Announcement\Repositories\Eloquent;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use MetaFox\Announcement\Models\Style;
use MetaFox\Announcement\Repositories\StyleRepositoryInterface;
use MetaFox\Announcement\Support\CacheManager;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * Class HiddenRepository.
 * @property Style $model
 * @method   Style getModel()
 * @method   Style find($id, $columns = ['*'])
 *
 * @ignore
 * @codeCoverageIgnore
 */
class StyleRepository extends AbstractRepository implements StyleRepositoryInterface
{
    public function model(): string
    {
        return Style::class;
    }

    /**
     * @inheritDoc
     */
    public function getStyleOptions(): array
    {
        $styles = Cache::rememberForever(CacheManager::ANNOUNCEMENT_STYLE_CACHE, function () {
            return $this->getModel()->get()->collect();
        });

        if (!$styles instanceof Collection) {
            return [];
        }

        return $styles->map(function (Style $style) {
            return [
                'label' => $style->name,
                'value' => $style->entityId(),
            ];
        })->toArray();
    }
}

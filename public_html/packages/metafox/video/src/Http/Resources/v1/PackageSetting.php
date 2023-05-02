<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Video\Http\Resources\v1;

use MetaFox\Video\Http\Resources\v1\Category\CategoryItemCollection;
use MetaFox\Video\Repositories\CategoryRepositoryInterface;

/**
 * | stub: src/Http/Resources/v1/PackageSetting.stub
 */

/**
 * Class PackageSetting.
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSetting
{
    public function getMobileSettings(CategoryRepositoryInterface $repository): array
    {
        return [
            'categories' => new CategoryItemCollection($repository->getCategoryForFilter()),
        ];
    }
}

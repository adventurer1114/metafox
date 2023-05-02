<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform\Facades;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Facade;

/**
 * Class ResourceGate.
 * @method static void              setVersion($version)
 * @method static string            getVersion()
 * @method static string            getMajorVersion()
 * @method static JsonResource|null asResource($model, $variant, mixed $checkPrivacy = 'view')
 * @method static JsonResource|null asItem($model, mixed $checkPrivacy = 'view')
 * @method static JsonResource|null asEmbed($model, mixed $checkPrivacy = 'view')
 * @method static JsonResource|null asDetail($model, mixed $checkPrivacy = 'view')
 * @method static JsonResource|null toItem(mixed $itemType, mixed $itemId, mixed $checkPrivacy = 'view')
 * @method static JsonResource|null toResource(mixed $variant, mixed $itemType, mixed $itemId, mixed $checkPrivacy =   'view')
 * @method static presentAs
 * @method static mixed       getItem(mixed $itemType, mixed $itemId)
 * @method static string|null pickNearestVersion(array $versions)
 * @link \MetaFox\Platform\ApiResourceManager::setVersion
 */
class ResourceGate extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ResourceGate';
    }
}

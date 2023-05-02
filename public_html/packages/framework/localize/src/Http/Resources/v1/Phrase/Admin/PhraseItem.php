<?php

namespace MetaFox\Localize\Http\Resources\v1\Phrase\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\App\Repositories\PackageRepositoryInterface;
use MetaFox\Core\Support\Facades\Language;
use MetaFox\Localize\Models\Phrase as Model;
use MetaFox\Localize\Repositories\LanguageRepositoryInterface;
use MetaFox\Platform\PackageManager;

/**
 * Class PhraseItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PhraseItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $key = preg_replace('#^(.+)\.#', '', $this->resource->key);

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'core',
            'resource_name' => 'core_phrase_admin',
            'key'           => $key,
            'group'         => $this->resource->group,
            'locale'        => $this->resource->locale,
            'language'      => Language::getName($this->resource->locale),
            'package_id'    => $this->resource->package_id,
            'namespace'     => $this->resource->namespace,
            'app_name'      => $this->getAppName($this->resource->package_id),
            'text'          => strip_tags($this->resource->text),
        ];
    }

    protected function getAppName(string $packageId): string
    {
        if ($packageId === 'core') {
            return __p('core::phrase.system');
        }

        $package = resolve(PackageRepositoryInterface::class)->findByName($packageId);

        return $package ? $package->title : 'Unknown';
    }
}

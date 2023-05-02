<?php

namespace MetaFox\Saved\Http\Resources\v1\Saved;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\ResourcePermission as ACL;
use MetaFox\Platform\Traits\Http\Resources\HasExtra;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\Saved\Models\Saved as Model;
use MetaFox\Saved\Repositories\SavedListRepositoryInterface;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\User\Models\UserEntity;

/**
 * Class SavedItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class SavedItem extends JsonResource
{
    use HasStatistic;
    use HasExtra;

    private ?int $collectionId = null;

    /**
     * @return array<string, mixed>
     */
    protected function getStatistic(): array
    {
        $savedLists = resolve(SavedListRepositoryInterface::class)->filterSavedListByUser(
            user(),
            $this->resource->savedLists
        );

        return [
            'total_collection' => $savedLists->count(),
        ];
    }

    public function setCollectionId(?int $collectionId): self
    {
        $this->collectionId = $collectionId;

        return $this;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string,           mixed>
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        $item         = $this->resource->item;
        $context      = user();
        $title        = '';
        $image        = null;
        $link         = null;
        $owner        = null;
        $itemTypeName = null;
        $totalPhoto   = 0;
        $photoPhrase  = '';

        if ($item instanceof HasSavedItem) {
            $savedData = $item->toSavedItem();
            if (!empty($savedData)) {
                $title = $savedData['title']; //todo: substring
                $image = $savedData['image'];
                /** @var UserEntity $owner */
                $owner        = $savedData['user'];
                $itemTypeName = $savedData['item_type_name'];
                $totalPhoto   = $savedData['total_photo'];
                $link         = $savedData['link'];

                if ($totalPhoto > 0) {
                    $photoPhrase = '1 ' . __p('photo::phrase.photo');
                }

                if ($totalPhoto > 1) {
                    $photoPhrase = $totalPhoto . ' ' . __p('photo::phrase.photos');
                }

                if (empty($title) && $this->resource->item_type == 'photo_set') {
                    $title = $photoPhrase;
                }
            }
        }
        $defaultCollection  = $this->resource->default_collection;
        $belongToCollection = false;

        if ($defaultCollection != null) {
            $belongToCollection = true;
        }

        return [
            'id'                      => $this->resource->entityId(),
            'module_name'             => 'saved',
            'resource_name'           => $this->resource->entityType(),
            'user'                    => new UserEntityDetail($this->resource->userEntity),
            'owner'                   => new UserEntityDetail($owner),
            'is_saved'                => true,
            'item_type'               => $this->resource->itemType(),
            'item_id'                 => $this->resource->itemId(),
            'title'                   => $title,
            'additional_information'  => null,
            'image'                   => $image,
            'is_opened'               => $this->resource->isOpened($context, $this->collectionId),
            'item_type_name'          => $itemTypeName,
            'total_photo'             => $totalPhoto,
            'photo_phrase'            => $photoPhrase,
            'saved_id'                => $this->resource->entityId(),
            'belong_to_collection'    => $belongToCollection,
            'default_collection_name' => $defaultCollection?->name,
            'default_collection_id'   => $defaultCollection?->entityId(),
            'creation_date'           => $this->resource->created_at,
            'modification_date'       => $this->resource->updated_at,
            'link'                    => $link,
            'extra'                   => $this->getCustomExtra(),
            'privacy'                 => MetaFoxPrivacy::EVERYONE,
            'statistic'               => $this->getStatistic(),
            'collection_ids'          => $this->resource->collection_ids,
            'url'                     => $link ? url_utility()->makeApiFullUrl($link) : null,
            'embed_object'            => ResourceGate::asEmbed($this->resource->item),
        ];
    }

    /**
     * @return array<string,           bool>
     * @throws AuthenticationException
     */
    protected function getCustomExtra(): array
    {
        $extras = $this->getExtra();

        return [
            ACL::CAN_VIEW   => $extras[ACL::CAN_VIEW] ?? true,
            ACL::CAN_DELETE => $extras[ACL::CAN_DELETE] ?? true,
            ACL::CAN_ADD    => $extras[ACL::CAN_ADD] ?? true,
            ACL::CAN_EDIT   => $extras[ACL::CAN_EDIT] ?? true,
            ACL::CAN_SHARE  => false,
        ];
    }
}

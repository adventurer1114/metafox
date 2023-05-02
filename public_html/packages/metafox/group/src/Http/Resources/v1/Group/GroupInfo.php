<?php

namespace MetaFox\Group\Http\Resources\v1\Group;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Group\Http\Resources\v1\Category\CategoryEmbed;
use MetaFox\Group\Http\Resources\v1\Type\TypeEmbed;
use MetaFox\Group\Models\Group as Model;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Traits\Http\Resources\HasExtra;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/**
 * Class GroupInfo.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class GroupInfo extends JsonResource
{
    use HasExtra;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        $groupText = $this->resource->groupText;
        $shortDescription = $text = $textParsed = '';
        $parent = $totalMembers = null;
        if ($groupText) {
            $shortDescription = parse_output()->getDescription($groupText->text_parsed);
            $text = $groupText->text;
            $textParsed = $groupText->text_parsed;
        }
        $category = $this->resource->category;
        if ($category->parent_id != null) {
            $parent = new CategoryEmbed($category->parentCategory);
        }

        if (user()->hasPermissionTo('group_member.view')) {
            $totalMembers = $this->resource->total_member;
        }

        return [
            'id'                => $this->resource->entityId(),
            'module_name'       => $this->resource->entityType(),
            'resource_name'     => 'page_info',
            'text'              => $text,
            'text_parsed'       => $textParsed,
            'description'       => $shortDescription,
            'total_member'      => $totalMembers,
            'external_link'     => $this->resource->external_link,
            'phone'             => $this->resource->phone,
            'location'          => $this->resource->location_name,
            'user'              => new UserEntityDetail($this->resource->userEntity),
            'creation_date'     => $this->resource->created_at,
            'modification_date' => $this->resource->updated_at,
            'link'              => $this->resource->toLink(),
            'url'               => $this->resource->toUrl(),
            'extra'             => $this->getExtra(),
            'privacy'           => $this->resource->privacy,
            'reg_method'        => $this->resource->privacy_type,
            'reg_name'          => __p(PrivacyTypeHandler::PRIVACY_PHRASE[$this->resource->privacy_type]),
            'category'          => new CategoryEmbed($category),
            'type'              => $parent,
        ];
    }
}

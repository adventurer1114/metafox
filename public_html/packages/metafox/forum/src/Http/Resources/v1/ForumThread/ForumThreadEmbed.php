<?php

namespace MetaFox\Forum\Http\Resources\v1\ForumThread;

use MetaFox\Forum\Support\Browse\Traits\ForumThread\StatisticTrait;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

class ForumThreadEmbed extends ForumThreadDetail
{
    use StatisticTrait;

    public function toArray($request): array
    {
        $resource = $this->resource;

        $description = null;

        if (null !== $resource->description) {
            $description = $this->getTransformContent($resource->description->text_parsed);
            $description = parse_output()->parse($description);
        }

        $user = null;

        if (null !== $resource->userEntity) {
            $user = new UserEntityDetail($resource->userEntity);
        }

        $title = $this->handleTitle($resource->toTitle());

        return [
            'id'            => $resource->entityId(),
            'module_name'   => 'forum',
            'resource_name' => $resource->entityType(),
            'title'         => $title,
            'description'   => $description,
            'short_description' => $resource->short_description,
            'user'          => $user,
            'privacy'       => $resource->getPrivacy(),
            'is_sponsor'    => $resource->isSponsor(),
            'link'          => $resource->toLink(),
            'url'           => $resource->toUrl(),
            'statistic'     => $this->getStatistic(),
        ];
    }
}

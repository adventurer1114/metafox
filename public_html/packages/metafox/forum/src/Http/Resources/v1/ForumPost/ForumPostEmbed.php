<?php

namespace MetaFox\Forum\Http\Resources\v1\ForumPost;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use MetaFox\Forum\Models\ForumPostText;
use MetaFox\Forum\Support\Browse\Traits\ForumPost\StatisticTrait;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

class ForumPostEmbed extends JsonResource
{
    use StatisticTrait;

    public function toArray($request): array
    {
        $resource = $this->resource;

        $content = '';

        $postText = $resource->postText;

        if ($postText instanceof ForumPostText) {
            $content = parse_output()->parse($postText->text_parsed);
        }

        $userEntity = new UserEntityDetail($resource->userEntity);

        return [
            'id'            => $resource->entityId(),
            'resource_name' => $resource->entityType(),
            'module_name'   => 'forum',
            'user'          => $userEntity,
            'short_content' => $resource->short_content,
            'content'       => $content,
            'statistic'     => $this->getStatistic(),
            'creation_date' => Carbon::parse($this->resource->created_at)->format('c'),
            'link'          => $this->resource->toLink(),
        ];
    }
}

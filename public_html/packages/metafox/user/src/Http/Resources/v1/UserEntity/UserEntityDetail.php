<?php

namespace MetaFox\User\Http\Resources\v1\UserEntity;

use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use MetaFox\Platform\Contracts\User as ContractUser;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\User\Models\UserEntity as Model;
use MetaFox\User\Support\Facades\User as UserFacade;
use MetaFox\User\Traits\UserLocationTrait;
use MetaFox\User\Traits\UserStatisticTrait;
use Illuminate\Support\Str;
use stdClass;

/**
 * Class UserDetail.
 * @property Model|stdClass $resource
 */
class UserEntityDetail extends JsonResource
{
    use UserLocationTrait;
    use UserStatisticTrait;

    /**
     * Special case when user is deleted and the relation is null
     * Laravel auto return null if $resource property is null
     * so need to override to bypass default behavior of Laravel.
     *
     * @param Model|null $resource
     */
    public function __construct(mixed $resource)
    {
        if (null === $resource) {
            $resource = new stdClass();
        }

        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        if (!$this->resource instanceof Model) {
            return $this->getDeletedUserResource();
        }

        $context    = user();
        $isDeleted  = $this->resource->isDeleted();
        $detail     = $this->resource->detail;
        $friendship = $detail instanceof ContractUser ? UserFacade::getFriendship($context, $detail) : null;

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => $this->resource->entityType(),
            'resource_name' => $this->resource->entityType(),
            'full_name'     => $this->when($isDeleted, __p('core::phrase.deleted_user'), $this->resource->name),
            'user_name'     => $this->when($isDeleted, null, $this->resource->user_name),
            'avatar'        => $this->when($isDeleted, null, $this->resource->avatars),
            'is_featured'   => $this->when($isDeleted, null, $this->resource->is_featured),
            'short_name'    => $this->when($isDeleted, null, $this->resource->short_name),
            'friendship'    => $this->when($isDeleted, null, $friendship),
            'link'          => $this->when($isDeleted, null, $this->resource->toLink()),
            'url'           => $this->when($isDeleted, null, $this->resource->toUrl()),
            'router'        => $this->when($isDeleted, null, $this->resource->toRouter()),
            'location'      => $this->when($isDeleted, null, $this->getLocationValue($context, $detail)),
            'is_deleted'    => $isDeleted,
            'statistic'     => $this->getResourceStatistics($request),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getResourceStatistics(Request $request): array
    {
        $default = $this->getStatistic();

        $detail = $this->resource->detail;

        if (null === $detail) {
            return $default;
        }

        $resource = ResourceGate::asEmbed($detail);

        if (null === $resource) {
            return $default;
        }

        $resource = $resource->toArray($request);

        $statistics = Arr::get($resource, 'statistic', []);

        return array_merge($default, $statistics);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDeletedUserResource(): array
    {
        $id = sprintf('deleted_user_%s_%s', Str::random(12), Carbon::now()->timestamp);

        return [
            'id'            => $id,
            'module_name'   => 'user',
            'resource_name' => 'user',
            'full_name'     => __p('core::phrase.deleted_user'),
            'user_name'     => $id,
            'avatar'        => null,
            'is_featured'   => null,
            'short_name'    => null,
            'friendship'    => null,
            'link'          => null,
            'url'           => null,
            'router'        => null,
            'location'      => null,
            'is_deleted'    => true,
            'statistic'     => [],
        ];
    }
}

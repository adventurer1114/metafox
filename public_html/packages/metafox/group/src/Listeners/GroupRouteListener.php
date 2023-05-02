<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Group\Listeners;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Models\GroupInviteCode;
use MetaFox\Group\Repositories\GroupInviteCodeRepositoryInterface;
use MetaFox\User\Models\UserEntity;

/**
 * Class GroupRouteListener.
 * @ignore
 * @codeCoverageIgnore
 */
class GroupRouteListener
{

    public GroupInviteCodeRepositoryInterface $codeRepository;

    public function __construct(GroupInviteCodeRepositoryInterface $codeRepository)
    {
        $this->codeRepository = $codeRepository;
    }

    /**
     * @param  string  $url
     *
     * @return array<string,mixed>|void
     */
    public function handle(string $url)
    {
        if (!Str::startsWith($url, 'group/')) {
            return;
        }
        $code = Arr::last(explode('/', $url));
        if (Str::startsWith($url, 'group/invite')) {
            $inviteCode = $this->codeRepository->getCodeByValue($code, 1);
            if (!$inviteCode instanceof GroupInviteCode) {
                return;
            }

            $group = $inviteCode->group;
            if (!$group instanceof Group) {
                return;
            }

            return [
                'path' => "/{$group->entityType()}/{$group->entityId()}?invite_code={$inviteCode->code}",
            ];
        }

        /** @var UserEntity $user */
        $user = UserEntity::query()->where('user_name', '=', $code)->firstOrFail();

        $entityId = $user->entityId();
        $prefix = $user->entityType();

        return [
            'path' => "/$prefix/$entityId",
        ];
    }
}

<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Event\Listeners;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MetaFox\Core\Models\UrlRewrite;
use MetaFox\Event\Models\Event;
use MetaFox\Event\Models\InviteCode;
use MetaFox\Event\Repositories\InviteCodeRepositoryInterface;

/**
 * Class EventRouteListener.
 * @ignore
 * @codeCoverageIgnore
 */
class EventRouteListener
{
    private InviteCodeRepositoryInterface $codeRepository;

    public function __construct(InviteCodeRepositoryInterface $codeRepository)
    {
        $this->codeRepository = $codeRepository;
    }

    /**
     * @param string $url
     *
     * @return array<string,mixed>|void
     */
    public function handle(string $url)
    {
        if (!Str::startsWith($url, 'event/invite')) {
            return;
        }

        $code = Arr::last(explode('/', $url));
        $inviteCode = $this->codeRepository->getCodeByValue($code, 1);
        if (!$inviteCode instanceof InviteCode) {
            return;
        }

        $event = $inviteCode->event;
        if (!$event instanceof Event) {
            return;
        }

        return [
            'path' => "/{$event->entityType()}/{$event->entityId()}?invite_code={$inviteCode->code}",
        ];
    }
}

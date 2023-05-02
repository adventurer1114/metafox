<?php

namespace MetaFox\Poll\Support;

use MetaFox\Poll\Contracts\PollSupportInterface;
use MetaFox\Poll\Repositories\PollRepositoryInterface;

class PollSupport implements PollSupportInterface
{
    public const INTEGRATION_VIEW_ID = 2;

    /**
     * @var PollRepositoryInterface
     */
    protected $repository;

    public function __construct(PollRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getIntegrationViewId(): int
    {
        return self::INTEGRATION_VIEW_ID;
    }
}

<?php

namespace MetaFox\User\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use MetaFox\Platform\Contracts\User as UserContract;
use MetaFox\User\Models\CancelFeedback as Model;

/**
 * Interface CancelFeedback.
 * @mixin BaseRepository
 */
interface CancelFeedbackRepositoryInterface
{
    /**
     * @param  UserContract         $context
     * @param  array<string, mixed> $attributes
     * @return Model
     */
    public function createFeedback(UserContract $context, array $attributes): Model;
}

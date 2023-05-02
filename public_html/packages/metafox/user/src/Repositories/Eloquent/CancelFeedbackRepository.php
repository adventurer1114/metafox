<?php

namespace MetaFox\User\Repositories\Eloquent;

use Illuminate\Support\Arr;
use MetaFox\Platform\Contracts\User as UserContract;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\User\Models\CancelFeedback;
use MetaFox\User\Models\CancelFeedback as Model;
use MetaFox\User\Repositories\CancelFeedbackRepositoryInterface;

class CancelFeedbackRepository extends AbstractRepository implements CancelFeedbackRepositoryInterface
{
    public function model(): string
    {
        return Model::class;
    }

    /**
     * @inheritDoc
     */
    public function createFeedback(UserContract $context, array $attributes): Model
    {
        $userId   = Arr::get($attributes, 'user_id', 0);
        $feedback = $this->getModel()->newModelQuery()->where('user_id', '=', $userId)->first();

        if ($feedback instanceof  Model) {
            return $feedback;
        }

        $feedback = new CancelFeedback();
        $feedback->fill($attributes);
        $feedback->save();

        return $feedback;
    }
}

<?php

namespace MetaFox\Group\Observers;

use MetaFox\Group\Models\Question;
use MetaFox\Group\Repositories\QuestionRepositoryInterface;

/**
 * Class QuestionObserver.
 * @ignore
 */
class QuestionObserver
{
    public function deleted(Question $question): void
    {
        resolve(QuestionRepositoryInterface::class)->deleteRelationsOfQuestion($question);
    }
}

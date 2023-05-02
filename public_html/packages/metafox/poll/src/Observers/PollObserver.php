<?php

namespace MetaFox\Poll\Observers;

use MetaFox\Poll\Models\Answer;
use MetaFox\Poll\Models\Poll;

/**
 * Class PollObserver.
 */
class PollObserver
{
    public function deleted(Poll $poll): void
    {
        $poll->design()->delete();

        $poll->answers()->get()->each(function (Answer $answer) {
            $answer->delete();
        });

        $poll->results()->delete();
    }
}

// end stub

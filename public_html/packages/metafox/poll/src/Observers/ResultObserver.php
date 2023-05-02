<?php

namespace MetaFox\Poll\Observers;

use Exception;
use MetaFox\Poll\Models\Result;

/**
 * Class ResultObserver.
 */
class ResultObserver
{
    public function created(Result $result): void
    {
        //Update total_count of the voted answer
        $answer = $result->answer;
        $poll   = $result->poll;

        $answer->incrementAmount('total_vote');
        $poll->incrementAmount('total_vote');
    }

    /**
     * @throws Exception
     */
    public function deleted(Result $result): void
    {
        //Update total_count of the voted answer
        $poll = $result->poll;

        if (!$poll) {
            return;
        }
        $poll->decrementAmount('total_vote');
    }
}

// end stub

import { useGlobal } from '@metafox/framework';
import { FeaturedFlag, FromNow, SponsorFlag, Statistic } from '@metafox/ui';
import { Box, Button, styled } from '@mui/material';
import React from 'react';
import PollVoteAnswer from './PollVoteAnswer';
import PollVoteForm from './PollVoteForm';
import useStyles from './PollVoteForm.styles';

const FlagWrapper = styled('span', {
  name: 'PollVoteFormRoot',
  slot: 'flagWrapper'
})(({ theme }) => ({
  marginLeft: 'auto',
  '& > .MuiFlag-root': {
    marginLeft: theme.spacing(0.5)
  }
}));

const ClosedStyled = styled(Box, {
  name: 'ClosedStyled'
})(({ theme }) => ({
  '&:before': {
    color: theme.palette.text.secondary,
    content: '"·"',
    paddingLeft: '0.25em',
    paddingRight: '0.25em'
  }
}));

export type PollVoteFormProps = {
  answers: Record<string, any>[];
  statistic: Record<string, number>;
  closeTime: string;
  pollId: number;
  isVoted: boolean;
  isMultiple?: boolean;
  publicVote?: boolean;
  identity: string;
  isPending?: boolean;
  canVoteAgain?: boolean;
  canVote?: boolean;
  canViewResult?: boolean;
  canViewResultAfter?: boolean;
  canViewResultBefore?: boolean;
  isEmbedInFeed?: boolean;
  isFeatured?: boolean;
  isSponsor?: boolean;
  isClosed?: boolean;
};

export default function PollVoteFormRoot(props: PollVoteFormProps) {
  const {
    answers: notSortAnswer,
    statistic,
    closeTime,
    pollId,
    isVoted,
    isMultiple,
    publicVote,
    identity,
    isPending,
    canVoteAgain,
    canVote,
    canViewResult,
    canViewResultAfter,
    canViewResultBefore,
    isEmbedInFeed,
    isFeatured,
    isSponsor,
    isClosed
  } = props;
  const answers = notSortAnswer.sort((a, b) => a?.ordering - b?.ordering);

  const classes = useStyles();
  const { i18n, dialogBackend } = useGlobal();

  const [showPoll, setShowPoll] = React.useState<boolean>(true);
  const [voteAgain, setVoteAgain] = React.useState<boolean>(false);
  const [isCanViewVoteAnswer, setIsCanViewVoteAnswer] =
    React.useState<boolean>(false);

  const [isCanViewResult, setIsCanViewResult] =
    React.useState<boolean>(canViewResult);

  const LIMIT_ANSWER_DISPLAY = isEmbedInFeed ? 3 : answers.length;

  const displayAnswers = answers.slice(0, LIMIT_ANSWER_DISPLAY);
  const hideAnswers = answers.slice(LIMIT_ANSWER_DISPLAY, answers.length);

  React.useEffect(() => {
    if (isCanViewResult) {
      if (isVoted) {
        setIsCanViewVoteAnswer(canViewResultAfter);
      } else {
        setIsCanViewVoteAnswer(canViewResultBefore);
      }
    }
  }, [
    isCanViewResult,
    canViewResult,
    isVoted,
    canViewResultAfter,
    canViewResultBefore
  ]);

  React.useEffect(() => {
    if (statistic.total_vote === 0) {
      setIsCanViewVoteAnswer(false);
    }
  }, [statistic]);

  React.useEffect(() => {
    setShowPoll(Boolean(!isVoted));
  }, [isVoted]);

  const handleVoteAgain = () => {
    setVoteAgain(true);
    setShowPoll(true);
  };

  return (
    <div className={classes.root}>
      {showPoll ? (
        <PollVoteForm
          voteAgain={voteAgain}
          pollId={pollId}
          identity={identity}
          displayAnswers={displayAnswers}
          isClosed={isClosed}
          hideAnswers={hideAnswers}
          isMultiple={isMultiple}
          answers={answers}
          LIMIT_ANSWER_DISPLAY={LIMIT_ANSWER_DISPLAY}
          isPending={isPending}
          canVote={canVote}
          canVoteAgain={canVoteAgain}
          isEmbedInFeed={isEmbedInFeed}
          setShowPoll={setShowPoll}
          setVoteAgain={setVoteAgain}
          setIsCanViewVoteAnswer={setIsCanViewVoteAnswer}
          setIsCanViewResult={setIsCanViewResult}
          canViewResultAfter={canViewResultAfter}
        />
      ) : (
        <PollVoteAnswer
          displayAnswers={displayAnswers}
          answers={answers}
          publicVote={publicVote}
          LIMIT_ANSWER_DISPLAY={LIMIT_ANSWER_DISPLAY}
          hideAnswers={hideAnswers}
          isMultiple={isMultiple}
          isCanViewVoteAnswer={isCanViewVoteAnswer}
          isCanViewResult={isCanViewResult}
        />
      )}
      {!showPoll && canVoteAgain && (
        <div className={classes.buttonWrapper}>
          <Button
            variant="outlined"
            size={isEmbedInFeed ? 'smaller' : 'medium'}
            color="primary"
            className={classes.button}
            onClick={handleVoteAgain}
            sx={{ fontWeight: 'bold' }}
          >
            {i18n.formatMessage({ id: 'vote_again' })}
          </Button>
        </div>
      )}
      <div className={classes.voteStatistic}>
        {isCanViewVoteAnswer && isCanViewResult ? (
          <div
            className={classes.activeTotalVote}
            onClick={() =>
              dialogBackend.present({
                component: 'poll.dialog.PeopleWhoVotedAnswer',
                props: {
                  listAnswers: answers
                }
              })
            }
          >
            {i18n.formatMessage(
              {
                id: 'total_vote'
              },
              { value: statistic.total_vote }
            )}
          </div>
        ) : (
          <Statistic
            className={classes.totalVote}
            color="textHint"
            values={statistic}
            display={'total_vote'}
            skipZero={false}
          />
        )}
        {'0' !== closeTime && !isClosed ? (
          <FromNow
            value={closeTime}
            className={classes.timeLeft}
            shorten={false}
          />
        ) : null}
        {isClosed ? (
          <ClosedStyled color={'text.hint'}>
            {i18n.formatMessage({ id: 'closed' })}
          </ClosedStyled>
        ) : null}
        {isEmbedInFeed ? (
          <FlagWrapper>
            <FeaturedFlag variant="text" value={isFeatured} color="primary" />
            <SponsorFlag variant="text" value={isSponsor} color="yellow" />
          </FlagWrapper>
        ) : null}
      </div>
    </div>
  );
}

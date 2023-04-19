import { useGlobal, useSession } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import { LinearProgress, Typography } from '@mui/material';
import clsx from 'clsx';
import React, { memo } from 'react';
import useStyles from './PollVoteForm.styles';

interface Props {
  displayAnswers: any;
  answers: any;
  publicVote: any;
  LIMIT_ANSWER_DISPLAY: any;
  hideAnswers: any;
  isMultiple: any;
  isCanViewVoteAnswer: any;
  isCanViewResult: any;
}
type ItemProps = {
  item: any;
  classes: any;
  settings?: any;
};

const PollVoteAnswerItem = ({ item, classes, settings }: ItemProps) => {
  return (
    <div className={classes.progressAnswer}>
      <Typography
        variant={item.voted ? 'h5' : 'body1'}
        className={clsx(item.voted ? classes.votedAnswer : classes.answerLabel)}
      >
        {item.answer}
      </Typography>
      <div className={classes.progressItem}>
        <LinearProgress
          variant="determinate"
          value={item.vote_percentage || 0}
          className={classes.progress}
        />
        {settings?.publicVote && (
          <Typography component="span" className={classes.progressPercent}>
            {`${item.vote_percentage}%`}
          </Typography>
        )}
      </div>
    </div>
  );
};

const PollVoteNoShowAnswer = ({ item, classes, settings }: ItemProps) => {
  const { user: authUser } = useSession();

  const checkIsOwner = () => {
    if (item?.some_votes) {
      return !!item?.some_votes?.map(user => user.id === authUser.id);
    }

    return false;
  };
  let icon = settings?.isMultiple ? 'ico-square-o' : ' ico-circle-o';

  if (settings?.isMultiple && item.voted && checkIsOwner()) {
    icon = 'ico-check-square';
  }

  if (!settings?.isMultiple && item.voted && checkIsOwner()) {
    icon = 'ico-check-circle';
  }

  return (
    <div className={classes.noShowAnswer}>
      <LineIcon className={classes.iconNoShowAnswer} icon={icon} />
      <Typography
        variant={item.voted ? 'h5' : 'body1'}
        className={clsx(item.voted ? classes.votedAnswer : classes.answerLabel)}
      >
        {item.answer}
      </Typography>
    </div>
  );
};

function PollVoteAnswer({
  displayAnswers,
  answers,
  publicVote,
  LIMIT_ANSWER_DISPLAY,
  hideAnswers,
  isMultiple,
  isCanViewVoteAnswer,
  isCanViewResult
}: Props) {
  const classes = useStyles();
  const { i18n } = useGlobal();
  const [viewMore, setViewMore] = React.useState(false);

  return (
    <div className={classes.progressAnswerWrapper}>
      {displayAnswers?.length > 0 &&
        displayAnswers.map((item, index) => {
          return isCanViewVoteAnswer && isCanViewResult ? (
            <PollVoteAnswerItem
              key={index}
              item={item}
              classes={classes}
              settings={{ publicVote }}
            />
          ) : (
            <PollVoteNoShowAnswer
              key={index}
              item={item}
              classes={classes}
              settings={{ publicVote, isMultiple }}
            />
          );
        })}
      {viewMore && hideAnswers?.length > 0
        ? hideAnswers.map((item, index) => {
            return isCanViewVoteAnswer && isCanViewResult ? (
              <PollVoteAnswerItem
                key={index}
                item={item}
                classes={classes}
                settings={{ publicVote }}
              />
            ) : (
              <PollVoteNoShowAnswer
                key={index}
                item={item}
                classes={classes}
                settings={{ publicVote, isMultiple }}
              />
            );
          })
        : null}
      {answers.length > LIMIT_ANSWER_DISPLAY ? (
        <span
          className={classes.btnToggle}
          onClick={() => setViewMore(!viewMore)}
          role="button"
        >
          {i18n.formatMessage({ id: viewMore ? 'view_less' : 'view_more' })}
        </span>
      ) : null}
    </div>
  );
}

export default memo(PollVoteAnswer);

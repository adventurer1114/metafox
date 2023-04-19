import { Link } from '@metafox/framework';
import PollVoteForm from '@metafox/poll/blocks/PollDetail/PollVoteForm';
import { FeedEmbedCard, TruncateText } from '@metafox/ui';
import React from 'react';
import useStyles from './styles';

export default function EmbedPollInFeedItemView({ item, identity, answers }) {
  const classes = useStyles();

  if (!item) return null;

  const {
    question,
    description,
    public_vote,
    close_time,
    statistic,
    id,
    is_multiple,
    extra,
    is_user_voted,
    is_featured,
    is_sponsor,
    is_closed
  } = item;

  return (
    <FeedEmbedCard variant="list" bottomSpacing="normal">
      <div className={classes.root}>
        <TruncateText variant={'h4'} lines={2} className={classes.title}>
          <Link to={`/poll/${item?.id}`} children={question} />
        </TruncateText>
        <TruncateText
          variant={'body1'}
          lines={3}
          color="textSecondary"
          className={classes.description}
        >
          <div dangerouslySetInnerHTML={{ __html: description }} />
        </TruncateText>
        <PollVoteForm
          isVoted={is_user_voted}
          pollId={id}
          isMultiple={is_multiple}
          answers={answers}
          statistic={statistic}
          closeTime={close_time}
          publicVote={public_vote}
          identity={identity}
          canVoteAgain={extra.can_change_vote}
          canVote={extra.can_vote}
          canViewResult={extra.can_view_result}
          isEmbedInFeed
          isFeatured={is_featured}
          isSponsor={is_sponsor}
          isClosed={is_closed}
          canViewResultAfter={extra.can_view_result_after_vote}
          canViewResultBefore={extra.can_view_result_before_vote}
        />
      </div>
    </FeedEmbedCard>
  );
}

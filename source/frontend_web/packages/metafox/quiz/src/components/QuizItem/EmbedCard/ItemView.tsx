import { Link } from '@metafox/framework';
import { EmbedQuizInFeedItemProps } from '@metafox/quiz';
import { FeedEmbedCard, Flag, Statistic, TruncateText } from '@metafox/ui';
import { Box } from '@mui/material';
import React from 'react';
import useStyles from './styles';

export default function EmbedQuizInFeedItemView({
  item
}: EmbedQuizInFeedItemProps) {
  const classes = useStyles();

  if (!item) return null;

  return (
    <FeedEmbedCard bottomSpacing="normal">
      <div className={classes.itemInner} data-testid="embedview">
        <Box mb={1} fontWeight={600} className={classes.title}>
          <Link to={item.link}>
            <TruncateText variant="h4" lines={3}>
              {item.title}
            </TruncateText>
          </Link>
        </Box>
        <Box className={classes.description} mb={2}>
          <TruncateText variant={'body1'} lines={3}>
            <div dangerouslySetInnerHTML={{ __html: item.description }} />
          </TruncateText>
        </Box>
        <Box
          className={classes.wrapperInfoFlag}
          display="flex"
          justifyContent="space-between"
          alignItems="flex-end"
        >
          <Statistic
            values={item.statistic}
            display="total_play"
            skipZero={false}
            color="textHint"
          />
          <div className={classes.flagWrapper}>
            {item.is_featured ? (
              <Flag data-testid="featured" type={'is_featured'} />
            ) : null}
            {item.is_sponsored_feed ? (
              <Flag data-testid="sponsor" type={'is_sponsor'} />
            ) : null}
          </div>
        </Box>
      </div>
    </FeedEmbedCard>
  );
}

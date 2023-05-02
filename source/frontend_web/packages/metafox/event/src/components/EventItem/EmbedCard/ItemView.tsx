import { EventDetailViewProps } from '@metafox/event/types';
import { isEventEnd } from '@metafox/event/utils';
import { Link, useGlobal } from '@metafox/framework';
import {
  DotSeparator,
  FeedEmbedCard,
  Flag,
  FormatDate,
  TruncateText
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { Box, styled, Typography } from '@mui/material';
import { isEqual } from 'lodash';
import React from 'react';
import InterestedButton from '../../InterestedButton';
import useStyles from './styles';

const name = 'EventEmbedItemView';

const BgCover = styled('div', {
  name,
  slot: 'bgCover'
})(({ theme }) => ({
  backgroundRepeat: 'no-repeat',
  backgroundPosition: 'center',
  backgroundSize: 'cover',
  height: 200,
  [theme.breakpoints.down('sm')]: {
    height: 160
  }
}));

const TypographyStyled = styled(Typography, {
  name,
  slot: 'TypographyStyled'
})(({ theme }) => ({
  fontSize: theme.spacing(1.625),
  fontWeight: '700',
  lineHeight: theme.spacing(2.5)
}));

export default function EventEmbedItemView({
  item,
  handleAction,
  actions,
  identity
}: EventDetailViewProps) {
  const classes = useStyles();

  const { i18n, useSession } = useGlobal();
  const { loggedIn } = useSession();

  if (!item) return null;

  const {
    title,
    location,
    image,
    link,
    statistic,
    start_time,
    end_time,
    rsvp,
    is_online,
    is_featured
  } = item;

  const isEnd = isEventEnd(end_time);

  return (
    <FeedEmbedCard variant="grid">
      {image ? (
        <Link to={link}>
          <BgCover
            style={{ backgroundImage: `url(${getImageSrc(image, '1024')})` }}
          />
        </Link>
      ) : null}
      <div className={classes.itemInner}>
        <Box mb={1.25} fontWeight={600} className={classes.title}>
          <Link to={link}>
            <TruncateText variant="h4" lines={1}>
              {title}
            </TruncateText>
          </Link>
        </Box>
        <Box mb={1.25}>
          <Typography
            component="div"
            variant="body1"
            textTransform="uppercase"
            color="primary"
          >
            <DotSeparator>
              <FormatDate
                data-testid="startedDate"
                value={start_time}
                format="LL"
              />
              <FormatDate
                data-testid="startedDate"
                value={start_time}
                format="LT"
              />
            </DotSeparator>
          </Typography>
        </Box>
        {is_online ? (
          <Box className={classes.description}>
            <TruncateText variant={'subtitle2'} lines={1}>
              {i18n.formatMessage({ id: 'online' })}
            </TruncateText>
          </Box>
        ) : (
          <Box className={classes.description}>
            <TruncateText variant={'body1'} lines={1}>
              {location?.address}
            </TruncateText>
          </Box>
        )}

        <Box
          className={classes.wrapperInfoFlag}
          display="flex"
          justifyContent="space-between"
          alignItems="center"
          mt={2}
        >
          <Box display="flex" alignItems="center">
            {loggedIn ? (
              <div className={classes.actions}>
                <InterestedButton
                  disabled={isEnd || isEqual(item.extra?.can_rsvp, false)}
                  actions={actions}
                  handleAction={handleAction}
                  identity={identity}
                  rsvp={rsvp}
                />
              </div>
            ) : null}
            <TypographyStyled variant="body2" color="text.hint">
              {statistic?.total_member ? (
                <>
                  {i18n.formatMessage(
                    { id: 'people_going' },
                    { value: statistic.total_member }
                  )}
                </>
              ) : null}
            </TypographyStyled>
          </Box>
          <div className={classes.flagWrapper}>
            {is_featured ? (
              <Flag
                data-testid="featured"
                type="is_featured"
                value={is_featured}
              />
            ) : null}
          </div>
        </Box>
      </div>
    </FeedEmbedCard>
  );
}

EventEmbedItemView.LoadingSkeleton = () => null;
EventEmbedItemView.displayName = 'EventItem_EmbedCard';

import {
  STATUS_ONGOING,
  SETTING_24H,
  EVENT_ACTIVE,
  APP_EVENT,
  STATUS_ENDED
} from '@metafox/event';
import { EventItemProps } from '@metafox/event/types';
import { isEventEnd } from '@metafox/event/utils';
import { Link, useGlobal } from '@metafox/framework';
import {
  FeaturedFlag,
  FormatDate,
  Image,
  ItemMedia,
  ItemSummary,
  ItemText,
  ItemTitle,
  ItemView,
  LineIcon,
  PendingFlag,
  SponsorFlag
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { Box, Chip, IconButton, styled, Typography } from '@mui/material';
import { get, isEqual } from 'lodash';
import React from 'react';
import InterestedButton from '../../InterestedButton';
import LoadingSkeleton from './LoadingSkeleton';
import { useSelector } from 'react-redux';

const name = 'EventItemMainCard';

const ItemViewMedia = styled(ItemMedia, { name, slot: 'itemViewMedia' })(
  () => ({
    '& .MuiImage-root': {
      maxHeight: '100%'
    },
    cursor: 'pointer'
  })
);

const ItemTitleStyled = styled(ItemTitle, { name, slot: 'itemTitleStyled' })(
  ({ theme }) => ({
    marginBottom: theme.spacing(1)
  })
);

const TimeStyled = styled(Box, { name, slot: 'timeStyled' })(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  height: theme.spacing(3),
  div: {
    marginRight: theme.spacing(1)
  }
}));

const FlagWrapper = styled('div', { name, slot: 'flagWrapper' })(
  ({ theme }) => ({
    position: 'absolute',
    top: theme.spacing(2),
    right: theme.spacing(-0.25),
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'flex-end'
  })
);

export default function EventItemMainCard({
  item,
  user,
  identity,
  itemProps,
  handleAction,
  actions,
  state,
  wrapProps,
  wrapAs
}: EventItemProps) {
  const { ItemActionMenu, assetUrl, i18n, getSetting, dispatch } = useGlobal();
  const itemActive = useSelector(state =>
    get(state, `${APP_EVENT}.${EVENT_ACTIVE}`)
  );

  const settingTime = getSetting('event.default_time_format');

  if (!item) return null;

  const { itemOnMap } = itemProps;

  const {
    id,
    image,
    title,
    location,
    rsvp,
    is_featured,
    is_sponsor,
    is_online,
    is_pending,
    status
  } = item;

  const to = `/event/${id}`;

  const cover = getImageSrc(image, '500', assetUrl('event.no_image'));

  const isEnd = isEventEnd(item.end_time);

  const onClickItem = id => {
    if (itemOnMap && `${EVENT_ACTIVE}${id}` !== itemActive)
      dispatch({ type: 'event/hover', payload: id });
  };

  return (
    <ItemView
      id={`${EVENT_ACTIVE}${id}`}
      onClick={() => onClickItem(`${EVENT_ACTIVE}${id}`)}
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
    >
      <ItemViewMedia>
        <Image link={!itemOnMap ? to : ''} src={cover} aspectRatio={'32'} />
      </ItemViewMedia>
      <FlagWrapper>
        <FeaturedFlag variant="itemView" value={is_featured} />
        <SponsorFlag variant="itemView" value={is_sponsor} />
        <PendingFlag variant="itemView" value={is_pending} />
      </FlagWrapper>
      <ItemText>
        <ItemTitleStyled>
          <Link
            color={
              itemOnMap && `${EVENT_ACTIVE}${id}` === itemActive
                ? 'primary'
                : 'inherit'
            }
            to={to}
            children={title}
          />
        </ItemTitleStyled>
        <TimeStyled>
          {status === STATUS_ONGOING && (
            <Chip
              size="small"
              label={i18n.formatMessage({ id: 'ongoing' })}
              variant="filled"
              sx={{
                color: 'default.contrastText',
                backgroundColor: 'success.main',
                fontSize: '13px'
              }}
            />
          )}
          {status === STATUS_ENDED && (
            <Chip
              size="small"
              label={i18n.formatMessage({ id: 'finished' })}
              variant="filled"
              sx={{
                color: 'default.contrastText',
                backgroundColor: 'text.secondary',
                fontSize: '13px'
              }}
            />
          )}
          <Typography
            component="div"
            variant="body2"
            textTransform="uppercase"
            color="primary"
          >
            <FormatDate
              data-testid="startedDate"
              value={item.start_time}
              format={
                settingTime === SETTING_24H
                  ? 'ddd, MMMM DD, YYYY HH:mm'
                  : 'llll'
              }
            />
          </Typography>
        </TimeStyled>
        {is_online ? (
          <ItemSummary>{i18n.formatMessage({ id: 'online' })}</ItemSummary>
        ) : (
          <ItemSummary>{location?.address}</ItemSummary>
        )}
        <Box display="flex" mt={0.5}>
          <Box sx={{ flex: 1, minWidth: 0 }}>
            <InterestedButton
              disabled={!!isEnd || isEqual(item.extra?.can_rsvp, false)}
              actions={actions}
              handleAction={handleAction}
              identity={identity}
              rsvp={rsvp}
            />
          </Box>
          {itemProps.showActionMenu ? (
            <ItemActionMenu
              identity={identity}
              state={state}
              handleAction={handleAction}
              sx={{ ml: 1 }}
              control={
                <IconButton
                  color="primary"
                  variant="outlined-square"
                  size="medium"
                >
                  <LineIcon icon={'ico-dottedmore-o'} />
                </IconButton>
              }
            />
          ) : null}
        </Box>
      </ItemText>
    </ItemView>
  );
}

EventItemMainCard.LoadingSkeleton = LoadingSkeleton;
EventItemMainCard.displayName = 'EventItem_MainCard';

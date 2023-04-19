import { Link, useGlobal } from '@metafox/framework';
import { FriendItemProps } from '@metafox/friend/types';
import {
  ButtonList,
  FormatDate,
  ItemMedia,
  ItemSummary,
  ItemText,
  ItemTitle,
  ItemView,
  LineIcon,
  Statistic,
  UserAvatar
} from '@metafox/ui';
import { Box, IconButton, Typography } from '@mui/material';
import * as React from 'react';

export default function FriendItem({
  item,
  user,
  identity,
  handleAction,
  state,
  wrapAs,
  wrapProps,
  actions
}: FriendItemProps) {
  const { ItemActionMenu, dispatch, useSession, i18n } = useGlobal();
  const { user: authUser } = useSession();

  if (!item) return null;

  const isAuthUser = authUser?.id === item.id;
  const { statistic, link: to } = item;
  const can_message = item?.extra?.can_message;

  const handleOpenChatRoom = () => {
    dispatch({
      type: 'chat/room/openChatRoom',
      payload: {
        identity: item._identity
      }
    });
  };

  const actionButton = !isAuthUser ? (
    <ButtonList>
      {can_message ? (
        <IconButton
          aria-label="message"
          size="medium"
          color="primary"
          variant="outlined-square"
          onClick={handleOpenChatRoom}
        >
          <LineIcon icon={'ico-comment-o'} />
        </IconButton>
      ) : null}
      <ItemActionMenu
        identity={identity}
        state={state}
        handleAction={handleAction}
        size="medium"
        color="primary"
        variant="outlined-square"
        icon={'ico-dottedmore-o'}
      />
    </ButtonList>
  ) : null;

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <ItemMedia>
        <UserAvatar user={item} size={80} />
      </ItemMedia>
      <ItemText>
        <ItemTitle>
          <Link
            to={to}
            asChildPage
            hoverCard={`/user/${item.id}`}
            children={item.full_name}
            color={'inherit'}
          />
        </ItemTitle>
        {!isAuthUser && statistic.total_mutual > 0 ? (
          <ItemSummary role="button" onClick={actions.showMutualFriends}>
            <Statistic values={statistic} display="total_mutual" />
          </ItemSummary>
        ) : (
          <ItemSummary>
            <Typography variant="body2" color="text.secondary">
              {item.country_name && item.country_state_name
                ? `${item.country_state_name}, ${item.country_name}`
                : i18n.formatMessage(
                    {
                      id: 'joined_at'
                    },
                    {
                      joined_date: () => (
                        <FormatDate
                          data-testid="joinedDate"
                          value={item.joined}
                          format="MMMM DD, yyyy"
                        />
                      )
                    }
                  )}
            </Typography>
          </ItemSummary>
        )}
        <Box sx={{ display: { xs: 'none' } }}>{actionButton}</Box>
      </ItemText>
      <Box sx={{ display: { xs: 'block' } }}>{actionButton}</Box>
    </ItemView>
  );
}

FriendItem.displayName = 'FriendItemViewMainCard';

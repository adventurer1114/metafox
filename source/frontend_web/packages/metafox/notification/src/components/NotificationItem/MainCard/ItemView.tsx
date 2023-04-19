import { Link, useGlobal } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { NotificationItemProps } from '@metafox/notification/types';
import {
  FromNow,
  ItemMedia,
  ItemSummary,
  ItemText,
  ItemView,
  UserAvatar
} from '@metafox/ui';
import { styled } from '@mui/material';
import { alpha } from '@mui/system/colorManipulator';
import React from 'react';

const ItemViewStyled = styled(ItemView, {
  shouldForwardProp: prop => {
    return prop !== 'isRead';
  }
})<{ isRead?: boolean }>(({ isRead, theme }) => ({
  cursor: 'pointer',
  backgroundColor: !isRead && alpha(theme.palette.primary.light, 0.2)
}));

const FromNowStyled = styled('div')(({ theme }) => ({
  fontSize: 13,
  lineHeight: 1.2
}));

const StyledLink = styled(Link)(({ theme }) => ({
  height: '100%',
  width: '100%',
  zIndex: 1,
  position: 'absolute'
}));

const TextNotification = styled(ItemSummary)(({ theme }) => ({
  '& a': {
    color: 'unset'
  }
}));

export default function NotificationItemMainCard({
  item,
  user,
  state,
  identity,
  handleAction,
  wrapProps,
  wrapAs,
  actions
}: NotificationItemProps) {
  const { ItemActionMenu } = useGlobal();

  if (!item) return null;

  const { message, creation_date, is_read } = item;

  const handleClick = () => {
    actions.markAsRead();
  };

  return (
    <ItemViewStyled
      wrapAs={wrapAs}
      isRead={is_read}
      wrapProps={wrapProps}
      {...wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <StyledLink to={item.link} onClick={handleClick} />
      <ItemMedia>
        <UserAvatar user={user as any} size={48} />
      </ItemMedia>
      <ItemText>
        <TextNotification>
          <HtmlViewer html={message} />
        </TextNotification>
        <FromNowStyled>
          <FromNow value={creation_date} />
        </FromNowStyled>
      </ItemText>
      <ItemActionMenu
        sx={{ ml: 1, zIndex: 2 }}
        identity={identity}
        state={state}
        icon={'ico-dottedmore-vertical-o'}
        handleAction={handleAction}
      />
    </ItemViewStyled>
  );
}

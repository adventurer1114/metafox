import HtmlViewer from '@metafox/html-viewer';
import { NotificationItemProps } from '@metafox/notification/types';
import {
  FromNow,
  ItemMedia,
  ItemSummary,
  ItemText,
  ItemView,
  LineIcon,
  UserAvatar
} from '@metafox/ui';
import { IconButton, styled } from '@mui/material';
import React from 'react';
import { alpha } from '@mui/material/styles';
import { NotificationPopperContext } from '@metafox/notification/poppers/Notification/Notifications';

const ActionButtonStyled = styled(IconButton)(({ theme }) => ({
  right: theme.spacing(0),
  top: '50%',
  transform: 'translateY(-50%)',
  position: 'absolute',
  opacity: 0
}));

const FromNowStyled = styled('div')(({ theme }) => ({
  fontSize: 13,
  lineHeight: 1.2
}));

const ItemViewStyled = styled(ItemView, {
  shouldForwardProp: prop => {
    return prop !== 'isRead';
  }
})<{ isRead?: boolean }>(({ isRead, theme }) => ({
  cursor: 'pointer',
  ...(!isRead && {
    backgroundColor: alpha(theme.palette.primary.light, 0.1)
  }),
  '&:hover': {
    '.action-close': {
      opacity: 1
    }
  },
  '& a': {
    fontWeight: theme.typography.fontWeightBold,
    color: `${theme.palette.text.primary} !important`
  }
}));

const TextNotification = styled(ItemSummary)(({ theme }) => ({
  '& a': {
    color: 'unset'
  }
}));

export default function NotificationItemSmallCard({
  item,
  identity,
  user,
  actions,
  wrapProps,
  wrapAs
}: NotificationItemProps) {
  const { closePopover } = React.useContext(NotificationPopperContext);

  if (!item) return null;

  const { message, creation_date, link, is_read } = item;
  const target =
    process.env.MFOX_BUILD_TYPE === 'admincp' ? '_blank' : undefined;

  const isLinkAdmincp = link?.startsWith('/admincp/');

  const handleClick = () => {
    actions.markAsRead();

    if (target === '_blank' || isLinkAdmincp) {
      window.open(link);
    } else {
      actions.viewItem();
    }

    closePopover();
  };

  const removeNotificationItem = e => {
    actions.deleteItem();
    e.stopPropagation();
  };

  return (
    <ItemViewStyled
      {...wrapProps}
      wrapAs={wrapAs}
      isRead={is_read}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
      onClick={handleClick}
    >
      <ItemMedia>
        <UserAvatar user={user as any} size={48} />
      </ItemMedia>
      <ItemText>
        <TextNotification lines={3}>
          <HtmlViewer html={message} />
        </TextNotification>
        <FromNowStyled>
          <FromNow value={creation_date} />
        </FromNowStyled>
        <ActionButtonStyled
          onClick={removeNotificationItem}
          aria-label="action"
          size="small"
          color="primary"
          variant="white-contained"
          className="action-close"
        >
          <LineIcon icon={'ico-close'} />
        </ActionButtonStyled>
      </ItemText>
    </ItemViewStyled>
  );
}

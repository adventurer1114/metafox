/**
 * @type: ui
 * name: appbar.item.user
 */
import { RouteLink, useSession } from '@metafox/framework';
import { MenuItemViewProps as Props, UserAvatar } from '@metafox/ui';
import { styled } from '@mui/material/styles';
import React from 'react';

const UserName = styled('span')(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(15),
  color: theme.palette.text.primary,
  display: 'inline-block',
  marginLeft: theme.spacing(1),
  fontWeight: 'bold',
  borderBottom: 'solid 1px',
  borderBottomColor: 'transparent'
}));

export default function AsUserItem({ item, classes }: Props) {
  const { user } = useSession();

  return (
    <RouteLink
      role="button"
      data-testid={item.testid || item.name}
      to={`/${user?.user_name}`}
      className={classes.userAvatarButton}
    >
      <UserAvatar user={user} size={40} noLink />
      <UserName>{user?.first_name || user?.full_name}</UserName>
    </RouteLink>
  );
}

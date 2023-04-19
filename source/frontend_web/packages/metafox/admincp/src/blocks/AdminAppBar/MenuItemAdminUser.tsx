/**
 * @type: ui
 * name: appbar.item.adminUser
 */
import { useSession } from '@metafox/framework';
import { MenuItemViewProps as Props, UserAvatar } from '@metafox/ui';
import clsx from 'clsx';
import React from 'react';

export default function AsUserItem({ item, classes }: Props) {
  const { user } = useSession();

  return (
    <a
      role="button"
      target="_blank"
      rel="noopener noreferrer"
      data-testid={item.testid || item.name}
      href={`/${user.user_name}`}
      className={classes.userAvatarButton}
    >
      <UserAvatar
        alt="User"
        user={user}
        className={classes.userAvatar}
        size={40}
        noLink
      />
      <div className={clsx(classes.userAvatarInfo, 'hidden')}>
        <div className={classes.userAvatarName}>{'Admin'}</div>
        <div className={classes.userAvatarRole}>{'Administrator'}</div>
      </div>
    </a>
  );
}

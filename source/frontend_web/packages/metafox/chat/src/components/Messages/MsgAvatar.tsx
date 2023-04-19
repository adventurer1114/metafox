import { useGlobal } from '@metafox/framework';
import { UserAvatar } from '@metafox/ui';
import { UserItemShape } from '@metafox/user';
import React, { memo } from 'react';
interface AvatarProps {
  identity?: string;
  user?: UserItemShape;
  size?: number;
}

function MsgAvatar({ identity, size = 30, user }: AvatarProps) {
  const { useGetItem } = useGlobal();

  const userItem = useGetItem(identity);

  if (!user && !userItem) return null;

  return <UserAvatar user={user || userItem} size={size} noLink />;
}

export default memo(MsgAvatar);

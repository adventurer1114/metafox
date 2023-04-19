import { Link, useGlobal, useSession } from '@metafox/framework';
import { FriendRequestItemProps } from '@metafox/friend/types';
import {
  ItemMedia,
  ItemSummary,
  ItemText,
  ItemTitle,
  ItemView,
  Statistic,
  UserAvatar
} from '@metafox/ui';
import { styled } from '@mui/material';
import * as React from 'react';
import LoadingSkeleton from './LoadingSkeleton';

const Root = styled(ItemView, { slot: 'root', name: 'FriendItem' })(
  ({ theme }) => ({
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center'
  })
);
const ItemContent = styled('div', { slot: 'ItemContent', name: 'FriendItem' })(
  ({ theme }) => ({
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center'
  })
);

export default function HostItemSmallCard({
  identity,
  user,
  wrapProps,
  wrapAs,
  actions
}: FriendRequestItemProps) {
  const { useLoggedIn } = useGlobal();
  const isLoggedIn = useLoggedIn();
  const { user: authUser } = useSession();

  if (!user) return null;

  const { statistic, link: to } = user;
  const isAuthUser = authUser?.id === user?.id;

  return (
    <Root
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${user.resource_name}`}
      data-eid={identity}
    >
      <ItemContent>
        <ItemMedia>
          <UserAvatar user={user} size={48} />
        </ItemMedia>
        <ItemText>
          <ItemTitle>
            <Link to={to} children={user.full_name} color={'inherit'} />
          </ItemTitle>
          {isLoggedIn && !isAuthUser ? (
            <ItemSummary role="button" onClick={actions.showMutualFriends}>
              <Statistic values={statistic} display="total_mutual" />
            </ItemSummary>
          ) : null}
        </ItemText>
      </ItemContent>
    </Root>
  );
}

HostItemSmallCard.LoadingSkeleton = LoadingSkeleton;
HostItemSmallCard.displayName = 'HostItemSmallCard';

import { EventDetailViewProps } from '@metafox/event';
import { Link, useGlobal, useSession } from '@metafox/framework';
import {
  ItemAction,
  ItemMedia,
  ItemSummary,
  ItemText,
  ItemTitle,
  ItemView,
  Statistic,
  UserAvatar
} from '@metafox/ui';
import { styled, Button } from '@mui/material';
import * as React from 'react';
import LoadingSkeleton from './LoadingSkeleton';

const Root = styled(ItemView, { slot: 'root', name: 'FriendItem' })(
  ({ theme }) => ({
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    '.MuiGrid-root.MuiGrid-item:last-child &': {
      borderBottom: 'none'
    }
  })
);
const ItemContent = styled('div', { slot: 'ItemContent', name: 'FriendItem' })(
  () => ({
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    width: '100%'
  })
);

const UserStyle = styled('div', { slot: 'UserStyle', name: 'FriendItem' })(
  () => ({
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center'
  })
);

const ButtonInviteStyled = styled(Button, { name: 'ButtonInviteStyled' })(
  ({ theme }) => ({
    fontWeight: theme.typography.fontWeightBold
  })
);

export default function HostSmallerCard({
  identity,
  user,
  item,
  wrapProps,
  wrapAs,
  actions
}: EventDetailViewProps) {
  const { i18n } = useGlobal();
  const { user: authUser } = useSession();

  if (!user || !item) return null;

  const { statistic, link: to } = user;
  const { extra } = item;
  const isAuthUser = authUser?.id === user.id;

  return (
    <Root
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${user.resource_name}`}
      data-eid={identity}
    >
      <ItemContent>
        <UserStyle>
          <ItemMedia>
            <UserAvatar user={user} size={48} />
          </ItemMedia>
          <ItemText>
            <ItemTitle>
              <Link to={to} children={user.full_name} color={'inherit'} />
            </ItemTitle>
            {!isAuthUser ? (
              <ItemSummary role="button">
                <Statistic values={statistic} display="total_mutual" />
              </ItemSummary>
            ) : null}
          </ItemText>
        </UserStyle>
        {extra?.can_remove_host ? (
          <ItemAction>
            <ButtonInviteStyled
              onClick={actions.removeHost}
              size="small"
              variant="outlined"
            >
              {i18n.formatMessage({ id: 'remove_host' })}
            </ButtonInviteStyled>
          </ItemAction>
        ) : null}
      </ItemContent>
    </Root>
  );
}

HostSmallerCard.LoadingSkeleton = LoadingSkeleton;

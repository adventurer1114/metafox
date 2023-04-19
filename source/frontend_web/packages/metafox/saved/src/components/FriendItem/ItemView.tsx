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
import { Button, styled } from '@mui/material';
import * as React from 'react';

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
const ButtonStyled = styled(Button, {
  slot: 'ButtonStyled',
  name: 'FriendItem'
})(({ theme }) => ({
  fontSize: theme.spacing(1.625)
}));

export default function FriendItem({
  identity,
  item,
  wrapProps,
  wrapAs,
  actions
}: FriendRequestItemProps) {
  const { user: authUser } = useSession();
  const { i18n, dispatch } = useGlobal();

  if (!item) return null;

  const { statistic, link: to, extra } = item;
  const isAuthUser = authUser.id === item.id;

  return (
    <Root
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <ItemContent>
        <ItemMedia>
          <UserAvatar user={item} size={48} />
        </ItemMedia>
        <ItemText>
          <ItemTitle>
            <Link to={to} children={item.full_name} color={'inherit'} />
          </ItemTitle>
          {!isAuthUser && statistic?.total_mutual > 0 ? (
            <ItemSummary role="button">
              <Statistic values={statistic} display="total_mutual" />
            </ItemSummary>
          ) : null}
        </ItemText>
      </ItemContent>
      {extra?.can_remove && (
        <ButtonStyled
          variant="contained"
          color="primary"
          onClick={() =>
            dispatch({
              type: 'saved/removeMemberInCollection',
              payload: { identity }
            })
          }
        >
          {i18n.formatMessage({ id: 'remove' })}
        </ButtonStyled>
      )}
    </Root>
  );
}

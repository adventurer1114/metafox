import { Link, useGetItem, useGlobal } from '@metafox/framework';
import { GroupItemProps } from '@metafox/group';
import {
  ItemMedia,
  ItemText,
  ItemView,
  Statistic,
  UserAvatar,
  ItemTitle,
  ItemSummary,
  FormatDate,
  LineIcon,
  ButtonList
} from '@metafox/ui';
import { IconButton, Tooltip } from '@mui/material';
import * as React from 'react';

export default function UserItem({
  item,
  identity,
  state,
  handleAction,
  actions,
  itemProps,
  wrapAs,
  wrapProps
}: GroupItemProps) {
  const { i18n } = useGlobal();
  const user = useGetItem(item?.user);

  if (!user) return null;

  const { statistic, full_name, user_name, city_location, joined } = user;
  const to = `/${user_name}`;

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      data-testid={`itemview ${item.resource_name}`}
      data-eid={identity}
    >
      <ItemMedia>
        <UserAvatar user={user} size={80} />
      </ItemMedia>
      <ItemText>
        <ItemTitle>
          <Link
            to={to}
            children={full_name}
            color={'inherit'}
            hoverCard={`/user/${user.id}`}
          />
        </ItemTitle>
        <ItemSummary>
          {statistic?.total_friend ? (
            <div role="button" onClick={actions.presentMutualFriends}>
              <Statistic values={statistic} display="total_friend" />
            </div>
          ) : (
            city_location ||
            i18n.formatMessage(
              {
                id: 'joined_at'
              },
              {
                joined_date: () => (
                  <FormatDate
                    data-testid="publishedDate"
                    value={joined}
                    format="MMMM DD, yyyy"
                  />
                )
              }
            )
          )}
        </ItemSummary>
      </ItemText>
      <ButtonList>
        <Tooltip title={i18n.formatMessage({ id: 'unblock_member' })}>
          <IconButton
            variant="outlined-square"
            size="medium"
            color="primary"
            onClick={actions.unBlockMember}
          >
            <LineIcon icon={'ico-ban'} />
          </IconButton>
        </Tooltip>
      </ButtonList>
    </ItemView>
  );
}

UserItem.displayName = 'BlockedUserMainCard';

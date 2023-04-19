import { Link, useGetItem, useGlobal } from '@metafox/framework';
import { GroupItemProps } from '@metafox/group';
import {
  ItemMedia,
  ItemText,
  ItemView,
  UserAvatar,
  ItemTitle,
  ItemSummary,
  FormatDate,
  LineIcon,
  ButtonList
} from '@metafox/ui';
import { IconButton, Tooltip, Typography } from '@mui/material';
import * as React from 'react';
import LoadingSkeleton from './LoadingSkeleton';

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

  const { full_name, user_name, city_location, joined } = user;
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
          {item?.expired_at ? (
            i18n.formatMessage(
              {
                id: 'muted_until'
              },
              {
                muted_until: () => (
                  <FormatDate
                    data-testid="publishedDate"
                    value={item?.expired_at}
                    format="MMMM DD, yyyy"
                  />
                )
              }
            )
          ) : (
            <Typography variant="body2">
              {city_location ||
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
                )}
            </Typography>
          )}
        </ItemSummary>
      </ItemText>
      <ButtonList>
        <Tooltip title={i18n.formatMessage({ id: 'unmute_member' })}>
          <IconButton
            variant="outlined-square"
            size="medium"
            color="primary"
            onClick={actions.unMuteMember}
          >
            <LineIcon icon={'ico-comment-del-o'} />
          </IconButton>
        </Tooltip>
      </ButtonList>
    </ItemView>
  );
}

UserItem.LoadingSkeleton = LoadingSkeleton;
UserItem.displayName = 'MutedUserMainCard';

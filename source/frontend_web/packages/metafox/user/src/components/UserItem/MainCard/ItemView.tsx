import { Link, useGlobal } from '@metafox/framework';
import {
  ButtonList,
  FeaturedFlag,
  FormatDate,
  ItemAction,
  ItemMedia,
  ItemSummary,
  ItemText,
  ItemTitle,
  ItemView,
  Statistic,
  UserAvatar
} from '@metafox/ui';
import { UserItemProps } from '@metafox/user/types';
import { filterShowWhen } from '@metafox/utils';
import * as React from 'react';
import { Box } from '@mui/material';

export default function UserItem({
  item,
  identity,
  state,
  handleAction,
  actions,
  itemProps,
  wrapAs,
  wrapProps,
  itemActionMenu
}: UserItemProps) {
  const { ItemActionMenu, i18n, useSession, getAcl, getSetting } = useGlobal();
  const { user: userAuth } = useSession();

  const acl = getAcl();
  const setting = getSetting();
  const condition = {
    item,
    acl,
    setting,
    isAuth: userAuth?.id === item?.id
  };

  const actionMenuItems = filterShowWhen(itemActionMenu, condition);

  if (!item) return null;

  const {
    statistic,
    full_name,
    user_name,
    city_location,
    joined,
    is_featured,
    id
  } = item;
  const to = `/${user_name}`;
  const aliasPath = id ? `/user/${id}` : '';

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <ItemMedia>
        <UserAvatar user={item} size={80} hoverCard={`/user/${item.id}`} />
      </ItemMedia>
      <ItemText>
        <ItemTitle>
          <Box sx={{ display: 'flex', alignItems: 'center', maxWidth: '100%' }}>
            <FeaturedFlag variant="itemView" value={is_featured} />
            <Link
              sx={{ overflow: 'hidden', textOverflow: 'ellipsis' }}
              to={to}
              aliasPath={aliasPath}
              children={full_name}
              color={'inherit'}
              hoverCard={`/user/${item.id}`}
            />
          </Box>
        </ItemTitle>
        {userAuth?.id !== item?.id ? (
          <ItemSummary>
            {statistic?.total_mutual ? (
              <div role="button" onClick={actions.presentMutualFriends}>
                <Statistic values={statistic} display="total_mutual" />
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
        ) : null}
      </ItemText>
      <ItemAction>
        {itemProps.showActionMenu ? (
          <ButtonList>
            <ItemActionMenu
              identity={identity}
              state={state}
              items={actionMenuItems}
              handleAction={handleAction}
              size="medium"
              variant="outlined-square"
              color="primary"
              icon="ico-dottedmore-o"
              tooltipTitle={i18n.formatMessage({ id: 'more_options' })}
            />
          </ButtonList>
        ) : null}
      </ItemAction>
    </ItemView>
  );
}

UserItem.displayName = 'UserItemMainCard';

import { Link, useGetItem, useGlobal } from '@metafox/framework';
import { GroupItemProps } from '@metafox/group';
import {
  ItemMedia,
  ItemText,
  ItemView,
  UserAvatar,
  ItemTitle,
  ItemSummary,
  LineIcon,
  ButtonList
} from '@metafox/ui';
import { IconButton } from '@mui/material';
import * as React from 'react';
import LoadingSkeleton from './LoadingSkeleton';

export default function UserItem({
  item,
  identity,
  actions,
  wrapAs,
  wrapProps
}: GroupItemProps) {
  const { i18n } = useGlobal();
  const user = useGetItem(item?.user);

  if (!user) return null;

  const { full_name, user_name } = user;
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
          <div role="button" onClick={actions.viewMemberQuestions}>
            {i18n.formatMessage({ id: 'view_membership_questions' })}
          </div>
        </ItemSummary>
      </ItemText>
      <ButtonList>
        <IconButton
          variant="outlined-square"
          size="medium"
          color="primary"
          onClick={actions.declinePendingRequest}
        >
          <LineIcon icon={'ico-close'} />
        </IconButton>
        <IconButton
          variant="outlined-square"
          size="medium"
          color="primary"
          onClick={actions.approvePendingRequest}
        >
          <LineIcon icon={'ico-check'} />
        </IconButton>
      </ButtonList>
    </ItemView>
  );
}

UserItem.LoadingSkeleton = LoadingSkeleton;
UserItem.displayName = 'PendingUserMainCard';

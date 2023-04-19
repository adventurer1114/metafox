import { useGlobal } from '@metafox/framework';
import { Tooltip } from '@mui/material';
import * as React from 'react';
import ProfileLink from './ProfileLink';

function WithTaggedOthersLink({
  children,
  className,
  item_type,
  item_id,
  users
}) {
  const { dialogBackend } = useGlobal();
  const onClick = React.useCallback(
    (evt: React.MouseEvent) => {
      if (evt) {
        evt.stopPropagation();
        evt.preventDefault();
      }

      dialogBackend.present({
        component: 'friend.dialog.taggedFriendDialogs',
        props: {
          item_type,
          item_id
        }
      });
    },
    [dialogBackend, item_id, item_type]
  );

  const ListUserTagged = () => {
    const items = users.slice(1);

    return (
      <div>
        {items.map(item => (
          <div key={item.id}>{item.full_name}</div>
        ))}
      </div>
    );
  };

  return (
    <Tooltip title={<ListUserTagged />}>
      <a href="/" onClick={onClick} className={className}>
        {children}
      </a>
    </Tooltip>
  );
}

export default function TaggedFriends({
  users,
  className,
  total,
  item_type,
  item_id
}) {
  const { i18n } = useGlobal();

  if (1 === total)
    return (
      <span>
        {i18n.formatMessage(
          {
            id: 'with_tagged_friend'
          },
          {
            user0: () => <ProfileLink user={users[0]} className={className} />,
            value: total - 1
          }
        )}
      </span>
    );

  if (2 === total) {
    return (
      <span>
        {i18n.formatMessage(
          {
            id: 'with_tagged_friend_and_user'
          },
          {
            user0: () => <ProfileLink user={users[0]} className={className} />,
            user1: () => <ProfileLink user={users[1]} className={className} />
          }
        )}
      </span>
    );
  }

  return (
    <span>
      {i18n.formatMessage(
        {
          id: 'with_tagged_friend_and_others'
        },
        {
          user0: () => <ProfileLink user={users[0]} className={className} />,
          others: str => (
            <WithTaggedOthersLink
              children={str}
              className={className}
              item_type={item_type}
              item_id={item_id}
              users={users}
            />
          ),
          value: total - 1
        }
      )}
    </span>
  );
}

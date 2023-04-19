/**
 * @type: ui
 * name: StatusComposerControlTaggedFriends
 */
import { StatusComposerControlProps, useGlobal } from '@metafox/framework';
import React from 'react';

type Props = StatusComposerControlProps;

function ProfileLink({ children, onClick }) {
  return (
    <b children={children} onClick={onClick} style={{ cursor: 'pointer' }} />
  );
}

export default function StatusComposerControlTaggedFriends(props: Props) {
  const { i18n, dispatch } = useGlobal();
  const { value: users, parentType, parentIdentity } = props;

  if (!users?.length) return null;

  const total = users.length;

  const handlePickedValue = value => {
    const { setTags } = props.composerRef.current;

    setTags('friends', {
      as: 'StatusComposerControlTaggedFriends',
      priority: 1,
      value
    });
  };

  const handleClick = () => {
    dispatch({
      type: 'friend/friendPicker',
      payload: {
        users,
        parentIdentity,
        parentType
      },
      meta: { onSuccess: handlePickedValue }
    });
  };

  if (1 === total)
    return (
      <span>
        {i18n.formatMessage(
          {
            id: 'with_tagged_friend'
          },
          {
            user0: () => (
              <ProfileLink
                onClick={handleClick}
                children={users[0].full_name}
              />
            )
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
            user0: () => (
              <ProfileLink
                onClick={handleClick}
                children={users[0].full_name}
              />
            ),
            user1: () => (
              <ProfileLink
                onClick={handleClick}
                children={users[1].full_name}
              />
            )
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
          user0: () => (
            <ProfileLink children={users[0].full_name} onClick={handleClick} />
          ),
          others: str => <ProfileLink children={str} onClick={handleClick} />,
          value: total - 1
        }
      )}
    </span>
  );
}

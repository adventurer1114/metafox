import { GlobalState, Link, useGlobal } from '@metafox/framework';
import {
  FRIENDSHIP_CAN_ADD_FRIEND,
  FRIENDSHIP_REQUEST_SENT
} from '@metafox/friend/constant';
import { FriendRequestItemProps } from '@metafox/friend/types';
import { getReactionItemSelector } from '@metafox/reaction/selectors/geReactionItem';
import { ItemView, UserAvatar } from '@metafox/ui';
import { Button } from '@mui/material';
import * as React from 'react';
import { useSelector } from 'react-redux';
import LoadingSkeleton from './LoadingSkeleton';
import useStyles from './styles';

const actionButtonList = [
  {
    id: FRIENDSHIP_CAN_ADD_FRIEND,
    text: 'add_friend',
    value: 'user/addFriend'
  },
  {
    id: FRIENDSHIP_REQUEST_SENT,
    text: 'cancel_request',
    value: 'user/cancelRequest'
  }
];

const ReactedUserItemView = ({
  item,
  user,
  wrapAs,
  wrapProps
}: FriendRequestItemProps) => {
  const classes = useStyles();
  const { i18n, useDialog, dispatch } = useGlobal();
  const { closeDialog } = useDialog();

  const reactionItem = useSelector((state: GlobalState) =>
    getReactionItemSelector(state, item.react_id)
  );

  if (!item) return null;

  const { mutual_friends } = item;
  const to = `/${user.user_name}`;

  const actionButton = actionButtonList.find(
    button => button.id === user.friendship
  );

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
    >
      <div className={classes.root}>
        <div className={classes.itemOuter}>
          <div className={classes.itemSmallInner}>
            <div className={classes.itemMainContent}>
              <div className={classes.itemSmallMedia}>
                <div className={classes.imgSmallWrapper}>
                  <UserAvatar user={user} size={48} />
                </div>
                <div className={classes.itemReactSmall}>
                  <img
                    className={classes.imgSmallReactionIcon}
                    src={reactionItem.icon}
                    alt="reaction"
                  />
                </div>
              </div>
              <div className={classes.userSmallInfo}>
                <div className={classes.userSmallTitle} onClick={closeDialog}>
                  <Link to={to} children={user.full_name} color={'inherit'} />
                </div>
                {mutual_friends?.total ? (
                  <div className={classes.friendSmallInfo} role="button">
                    <span className={classes.mutualFriend}>
                      {mutual_friends?.total}
                    </span>
                    {i18n.formatMessage(
                      { id: 'total_mutual_friend' },
                      { value: mutual_friends?.total }
                    )}
                  </div>
                ) : null}
              </div>
            </div>
            {actionButton && (
              <Button
                className={classes.actionContent}
                variant="outlined"
                color="primary"
                onClick={() =>
                  dispatch({
                    type: actionButton.value,
                    payload: { identity: `user.entities.user.${user.id}` }
                  })
                }
              >
                {i18n.formatMessage({ id: actionButton.text })}
              </Button>
            )}
          </div>
        </div>
      </div>
    </ItemView>
  );
};

ReactedUserItemView.LoadingSkeleton = LoadingSkeleton;

export default ReactedUserItemView;

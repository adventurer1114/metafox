import MsgAvatar from '@metafox/chat/components/Messages/MsgAvatar';
import { useGlobal } from '@metafox/framework';
import { Button } from '@mui/material';
import React from 'react';
import useStyles from './stylesListItem';

const ListItemUserReaction = ({ data, unsetReaction }: any) => {
  const classes = useStyles();
  const { useDialog, i18n, useSession } = useGlobal();
  const { closeDialog } = useDialog();

  const { user } = useSession();

  const me = user.user_name;

  if (!data) return null;

  return data.usernames.map((user, key) => (
    <div className={classes.root} key={key}>
      <div className={classes.itemOuter}>
        <div className={classes.itemSmallInner}>
          <div className={classes.itemMainContent}>
            <div className={classes.itemSmallMedia}>
              <div className={classes.imgSmallWrapper}>
                <MsgAvatar user={user.user} size={40} />
              </div>
              <div className={classes.itemReactSmall}>
                <img
                  className={classes.imgSmallReactionIcon}
                  src={user.icon}
                  alt="reaction"
                />
              </div>
            </div>
            <div className={classes.userSmallInfo}>
              <div className={classes.userSmallTitle} onClick={closeDialog}>
                {user.user?.full_name || user.username}
              </div>
            </div>
          </div>
          {me === user.username && (
            <Button
              className={classes.actionContent}
              variant="outlined"
              color="primary"
              onClick={() => unsetReaction(user.id)}
            >
              {i18n.formatMessage({ id: 'remove' })}
            </Button>
          )}
        </div>
      </div>
    </div>
  ));
};

export default ListItemUserReaction;

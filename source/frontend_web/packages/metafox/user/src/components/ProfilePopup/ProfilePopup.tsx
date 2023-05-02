import { Link, useGlobal } from '@metafox/framework';
import {
  ButtonList,
  LineIcon,
  MenuItemShape,
  TruncateText,
  UserAvatar
} from '@metafox/ui';
import { getMenuAction } from '@metafox/user/utils';
import { filterShowWhen } from '@metafox/utils';
import { Button, Paper, Popper, styled, Box } from '@mui/material';
import * as React from 'react';
import useStyles from './ProfilePopup.styles';

const AvatarWrapper = styled('div', { name: 'AvatarWrapper' })(({ theme }) => ({
  marginRight: theme.spacing(1.5)
}));

export default function ProfilePopup({
  open,
  anchorEl,
  item,
  actions,
  loaded,
  identity,
  state,
  handleAction
}) {
  const {
    i18n,
    ItemActionMenu,
    useSession,
    popoverBackend,
    getAcl,
    getSetting
  } = useGlobal();
  const classes = useStyles();

  const acl = getAcl();
  const setting = getSetting();

  const { loggedIn, userAuth } = useSession();
  const menus = getMenuAction('profilePopoverMenu');
  const [isHovering, setIsHovering] = React.useState(false);

  if (!loggedIn || !item) return null;

  const { statistic } = item;
  const friendshipCanAddFriend = 0 === item.friendship;
  const friendshipIsFriend = 1 === item.friendship;
  const friendshipConfirmAwait = 2 === item.friendship;
  const friendshipRequesting = 3 === item.friendship;
  const friendshipIsOwner = 5 === item.friendship;
  const to = `/${item.user_name}`;
  const total_mutual = statistic?.total_mutual || 0;
  const total_friend = statistic?.total_friend || 0;

  const condition = { item, acl, setting, isAuth: userAuth?.id === item?.id };
  const actionMenuItemsFull = filterShowWhen(menus, condition);

  const menuIsFriend: MenuItemShape[] = actionMenuItemsFull.filter(
    item => item.name !== 'delete'
  );

  const menuRequesting: MenuItemShape[] = actionMenuItemsFull.filter(
    item => item.name !== 'cancel_request'
  );

  return (
    <Popper
      open={open}
      style={{ zIndex: 1300 }}
      anchorEl={anchorEl}
      onMouseEnter={popoverBackend.onEnterContent}
      onMouseLeave={popoverBackend.onLeaveContent}
    >
      <Paper className={classes.paper}>
        <Box mb={2} className={classes.header}>
          <AvatarWrapper>
            <Link to={to}>
              <UserAvatar user={item} size={80} className={classes.avatar} />
            </Link>
          </AvatarWrapper>
          <div className={classes.profileLink}>
            <Link to={to}>{item.full_name}</Link>
          </div>
        </Box>
        {item.about_me ? (
          <Box mb={2}>
            <TruncateText variant={'body1'} lines={3}>
              {item.about_me}
            </TruncateText>
          </Box>
        ) : null}
        {item.address ? (
          <>
            <LineIcon icon="ico-globe-o" /> {item.address}
          </>
        ) : null}
        <div className={classes.friends}>
          <div>
            <span>
              {i18n.formatMessage(
                { id: 'total_friend' },
                { value: total_friend }
              )}
            </span>
            {total_mutual && !friendshipIsOwner ? (
              <span role="button" onClick={actions.presentMutualFriends}>
                <span children=" (" />
                {i18n.formatMessage(
                  { id: 'total_mutual' },
                  { value: total_mutual }
                )}
                <span children=")" />
              </span>
            ) : null}
          </div>
        </div>
        {loggedIn && friendshipIsFriend && (
          <ButtonList variant="fillWidth" spacing="medium">
            {item.extra?.can_message && (
              <Button
                sx={{ flex: 1 }}
                size="medium"
                variant="contained"
                color="primary"
                startIcon={<LineIcon icon={'ico-comment-o'} />}
                onClick={actions.chatWithFriend}
              >
                {i18n.formatMessage({ id: 'message' })}
              </Button>
            )}
            <Button
              sx={{ flex: 1 }}
              size="medium"
              variant="outlined"
              color="primary"
              startIcon={
                <LineIcon
                  icon={isHovering ? 'ico-user3-minus-o' : 'ico-user3-check-o'}
                />
              }
              onClick={actions.unfriend}
              onMouseOver={() => setIsHovering(true)}
              onMouseOut={() => setIsHovering(false)}
            >
              {i18n.formatMessage({ id: isHovering ? 'unfriend' : 'friend' })}
            </Button>
            <ItemActionMenu
              items={menuIsFriend}
              menuName="profilePopoverMenu"
              identity={identity}
              state={state}
              handleAction={handleAction}
              className={classes.actionsDropdown}
              size="medium"
              variant="outlined-square"
              color="primary"
              icon="ico-dottedmore-o"
              zIndex={1300}
              tooltipTitle={i18n.formatMessage({ id: 'more_options' })}
            />
          </ButtonList>
        )}
        {loggedIn && friendshipRequesting && (
          <ButtonList variant="fillWidth" spacing="medium">
            {item.extra?.can_message && (
              <Button
                size="medium"
                variant="contained"
                color="primary"
                startIcon={<LineIcon icon={'ico-comment-o'} />}
                onClick={actions.chatWithFriend}
              >
                {i18n.formatMessage({ id: 'message' })}
              </Button>
            )}
            <Button
              size="medium"
              variant="outlined"
              color="primary"
              startIcon={<LineIcon icon={'ico-user2-del-o'} />}
              onClick={actions.cancelRequest}
            >
              {i18n.formatMessage({ id: 'cancel_request' })}
            </Button>

            <ItemActionMenu
              items={menuRequesting}
              identity={identity}
              state={state}
              handleAction={handleAction}
              className={classes.actionsDropdown}
              size="medium"
              variant="outlined-square"
              color="primary"
              icon="ico-dottedmore-o"
              zIndex={1300}
              tooltipTitle={i18n.formatMessage({ id: 'more_options' })}
            />
          </ButtonList>
        )}
        {loggedIn && friendshipCanAddFriend && (
          <ButtonList variant="fillWidth" spacing="medium">
            {item.extra?.can_message && (
              <Button
                className={classes.buttonDisplay}
                color="primary"
                variant="contained"
                size="medium"
                startIcon={<LineIcon icon={'ico-comment-o'} />}
                onClick={actions.chatWithFriend}
              >
                {i18n.formatMessage({ id: 'message' })}
              </Button>
            )}
            <Button
              className={classes.buttonDisplay}
              color="primary"
              variant="outlined"
              size="medium"
              startIcon={<LineIcon icon={'ico-plus'} />}
              onClick={actions.addFriend}
            >
              {i18n.formatMessage({ id: 'add_as_friend' })}
            </Button>
            <ItemActionMenu
              menuName="profilePopoverMenu"
              identity={identity}
              state={state}
              handleAction={handleAction}
              className={classes.actionsDropdown}
              size="medium"
              variant="outlined-square"
              color="primary"
              icon="ico-dottedmore-o"
              zIndex={1300}
              tooltipTitle={i18n.formatMessage({ id: 'more_options' })}
            />
          </ButtonList>
        )}
        {loggedIn && friendshipConfirmAwait && (
          <div>
            <div className={classes.sendRequest}>
              {i18n.formatMessage({
                id: 'This person sent you a friend request'
              })}
            </div>
            <ButtonList variant="fillWidth" spacing="medium">
              <Button
                variant="contained"
                color="primary"
                size="medium"
                type="submit"
                onClick={actions.acceptFriend}
              >
                {i18n.formatMessage({ id: 'confirm' })}
              </Button>
              <Button
                variant="outlined"
                color="primary"
                size="medium"
                type="submit"
                onClick={actions.denyFriend}
              >
                {i18n.formatMessage({ id: 'decline' })}
              </Button>
              <ItemActionMenu
                menuName="profilePopoverMenu"
                identity={identity}
                state={state}
                handleAction={handleAction}
                className={classes.actionsDropdown}
                size="medium"
                variant="outlined-square"
                color="primary"
                icon="ico-dottedmore-o"
                zIndex={1300}
                tooltipTitle={i18n.formatMessage({ id: 'more_options' })}
              />
            </ButtonList>
          </div>
        )}
      </Paper>
    </Popper>
  );
}

ProfilePopup.displayName = 'UserItem_ProfilePopup';

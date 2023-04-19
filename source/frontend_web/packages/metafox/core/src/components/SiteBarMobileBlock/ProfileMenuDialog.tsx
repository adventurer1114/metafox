/**
 * @type: dialog
 * name: core.dialog.profileMenuMobile
 */
import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import {
  Link,
  useActionControl,
  useAppMenu,
  useGlobal
} from '@metafox/framework';
import { UserAvatar } from '@metafox/ui';
import { Divider as MenuDivider } from '@mui/material';
import useStyles from './SiteBarMobileBlock.styles';
import React from 'react';

function ProfileMenuMobile() {
  const classes = useStyles();
  const { useDialog, i18n, useSession, jsxBackend } = useGlobal();
  const { dialogProps } = useDialog();
  const { user: authUser } = useSession();
  const accountMenu = useAppMenu('core', 'accountMenu');
  const [handleAction] = useActionControl(null, {});
  const title = i18n.formatMessage({ id: 'profile' });

  return (
    <Dialog
      maxWidth="sm"
      fullWidth
      data-testid="popupProfileMenu"
      {...dialogProps}
    >
      <DialogTitle data-testid="popupTitle" enableBack disableClose>
        {title}
      </DialogTitle>
      <DialogContent className={classes.dialog}>
        <div className={classes.profile}>
          <MenuDivider {...{ variant: 'fullWidth' }} />
          <div className={classes.userBlock}>
            <div className={classes.userAvatar}>
              <UserAvatar user={authUser} size={48} />
            </div>
            <div className={classes.userInner}>
              <div className={classes.userName}>{authUser.full_name}</div>
              <Link className={classes.linkInfo} to={authUser.link}>
                {i18n.formatMessage({ id: 'view_profile' })}
              </Link>
            </div>
          </div>
          {accountMenu.items.filter(Boolean).map((item, index) =>
            jsxBackend.render({
              component: `menuItem.as.${item.as || 'normal'}`,
              props: {
                key: index.toString(),
                item,
                variant: 'contained',
                handleAction
              }
            })
          )}
        </div>
      </DialogContent>
    </Dialog>
  );
}

export default ProfileMenuMobile;

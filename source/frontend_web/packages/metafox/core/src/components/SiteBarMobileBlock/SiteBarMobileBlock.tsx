/**
 * @type: block
 * name: core.siteBarMobileBlock
 * title: SiteBar Mobile
 * keywords: sidebar
 * mobile: true
 */

import SideAppMenuBlock from '@metafox/core/blocks/SidebarAppMenu/Base';
import {
  BlockViewProps,
  createBlock,
  Link,
  useGlobal,
  useLocation
} from '@metafox/framework';
import { Block, BlockContent } from '@metafox/layout';
import { LineIcon, UserAvatar } from '@metafox/ui';
import { filterShowWhen } from '@metafox/utils';
import { Button, Popover, useTheme } from '@mui/material';
import clsx from 'clsx';
import { isEmpty } from 'lodash';
import React from 'react';
import AppBarSearch from '../../blocks/AppBarBlock/AppBarSearch';
import useStyles from './SiteBarMobileBlock.styles';

const TabMenuData = [
  {
    to: '/home',
    icon: 'ico-home-alt',
    appName: 'feed'
  },
  {
    to: '/messages',
    icon: 'ico-comment-o',
    appName: 'chatplus',
    showWhen: ['and', ['truthy', 'setting.chatplus.server']]
  },
  {
    to: '/messages',
    icon: 'ico-comment-o',
    appName: 'chat',
    showWhen: [
      'and',
      ['truthy', 'setting.broadcast.connections.pusher.key'],
      ['falsy', 'setting.chatplus.server']
    ]
  },
  {
    to: '/notification',
    icon: 'ico-bell-o',
    appName: 'notification'
  },
  {
    icon: 'ico-search-o',
    appName: '',
    style: 'search'
  }
];

function BaseBlock({ blockProps }: BlockViewProps) {
  const classes = useStyles();
  const {
    i18n,
    useSession,
    navigate,
    assetUrl,
    usePageParams,
    dialogBackend,
    getSetting
  } = useGlobal();
  const location = useLocation();
  const { user: authUser } = useSession();
  const [open, setOpen] = React.useState<Boolean>(false);
  const [openSearch, setOpenSearch] = React.useState<Boolean>(false);
  const anchorRef = React.useRef<HTMLDivElement>();
  const theme = useTheme();
  const setting: any = getSetting();

  const TabMenu = filterShowWhen(TabMenuData, {
    setting
  });

  const logo =
    theme.palette.mode === 'dark'
      ? assetUrl('layout.image_logo_dark')
      : assetUrl('layout.image_logo');

  const { appName = 'feed', soft = null } = usePageParams();

  const handleClick = () => {
    setOpen(prev => !prev);
  };

  const handleClose = () => {
    setOpen(prev => !prev);
  };

  const toggleOpen = () => {
    setOpenSearch(prev => !prev);
  };

  React.useEffect(() => {
    setOpen(false);
  }, [location.pathname]);

  const id = open ? 'dropdownMenuMobile' : undefined;

  const handleShowProfileMenu = () => {
    dialogBackend.present({
      component: 'core.dialog.profileMenuMobile',
      props: {}
    });
  };

  const signInButtonOnClick = () => {
    navigate({
      pathname: '/login'
    });
  };

  if (isEmpty(authUser)) {
    return (
      <Block>
        <BlockContent>
          <div className={classes.blockHeader}>
            <div className={classes.menuGuestWrapper}>
              <Link
                to="/"
                className={classes.logo}
                title={i18n.formatMessage({ id: 'home' })}
              >
                <img src={logo} height="35" alt="home" />
              </Link>
              <Button
                variant="contained"
                color="primary"
                size="small"
                onClick={signInButtonOnClick}
                disableElevation
                type="submit"
                className={classes.button}
              >
                {i18n.formatMessage({ id: 'sign_in' })}
              </Button>
            </div>
          </div>
        </BlockContent>
      </Block>
    );
  }

  return (
    <Block>
      <BlockContent>
        <div className={classes.blockHeader}>
          <div className={classes.menuWrapper}>
            {TabMenu.map((item, index) =>
              item.style !== 'search' ? (
                <Link
                  key={index}
                  role="button"
                  to={item.to}
                  className={clsx(
                    classes.menuButton,
                    appName === item.appName && !soft && classes.active
                  )}
                  underline="none"
                >
                  <LineIcon
                    className={classes.menuButtonIcon}
                    icon={item.icon}
                  />
                </Link>
              ) : (
                <>
                  <Link
                    key={index}
                    role="button"
                    to={item.to}
                    className={clsx(
                      classes.menuButton,
                      appName === item.appName && !soft && classes.active
                    )}
                    underline="none"
                    onClick={toggleOpen}
                  >
                    <LineIcon
                      className={classes.menuButtonIcon}
                      icon={item.icon}
                    />
                  </Link>
                  {openSearch ? (
                    <div className={clsx(classes.searchMobile)}>
                      <AppBarSearch
                        openSearch={openSearch}
                        closeSearch={() => setOpenSearch(false)}
                      />
                      <Link
                        onClick={toggleOpen}
                        className={clsx(classes.cancelButton)}
                      >
                        {i18n.formatMessage({ id: 'Cancel' })}
                      </Link>
                    </div>
                  ) : null}
                </>
              )
            )}
            <Link
              role="button"
              ref={anchorRef}
              className={clsx(classes.menuButton, soft && classes.active)}
              onClick={handleClick}
              underline="none"
            >
              <LineIcon className={classes.menuButtonIcon} icon="ico-navbar" />
            </Link>
          </div>
        </div>
        <Popover
          id={id}
          open={Boolean(open)}
          anchorEl={anchorRef.current}
          onClose={handleClose}
          disableScrollLock
          anchorReference="anchorPosition"
          anchorPosition={{ top: 60, left: 0 }}
          style={{ maxWidth: '100%' }}
          marginThreshold={0}
          transitionDuration={0}
          className={classes.popover}
          anchorOrigin={{
            vertical: 'top',
            horizontal: 'right'
          }}
          transformOrigin={{
            vertical: 'top',
            horizontal: 'right'
          }}
        >
          <div className={classes.dropdownMenuWrapper}>
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
              <div className={classes.userAction}>
                <LineIcon
                  icon={'ico ico-angle-right'}
                  onClick={handleShowProfileMenu}
                />
              </div>
            </div>
            <div className={classes.menuApp}>
              <SideAppMenuBlock appName="core" menuName="primaryMenu" />
            </div>
          </div>
        </Popover>
      </BlockContent>
    </Block>
  );
}

const SiteBarMobileBlock = createBlock<BlockViewProps>({
  name: 'SiteBarMobileBlock',
  extendBlock: BaseBlock
});

export default SiteBarMobileBlock;

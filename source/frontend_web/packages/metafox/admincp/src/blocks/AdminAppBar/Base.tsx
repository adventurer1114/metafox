import { RouteLink, useGlobal } from '@metafox/framework';
import { UIBlockProps } from '@metafox/layout';
import { LineIcon, MenuItems } from '@metafox/ui';
import { filterShowWhen } from '@metafox/utils';
import { AppBar, IconButton, Toolbar, Tooltip, useTheme } from '@mui/material';
import { styled } from '@mui/material/styles';
import React from 'react';
import AdminSearchForm from './AdminSearchForm';
import subMenu from './adminSubMenu';
import useStyles from './styles';

const ToggleNavButton = styled(IconButton, {
  name: 'ToggleNavButton',
  slot: 'Root'
})({
  width: '48px',
  height: '48px',
  display: 'inline-flex',
  WebkitBoxAlign: 'center',
  msFlexAlign: 'center',
  alignItems: 'center',
  WebkitBoxPack: 'center',
  msFlexPack: 'center',
  justifyContent: 'center',
  color: '#555',
  fontSize: '16px',
  fontWeight: 'bold',
  cursor: 'pointer'
});

const AdminAppBarLogo = styled('div', {
  name: 'AdminAppBarLogo',
  slot: 'Root'
})(({ theme }) => ({
  whiteSpace: 'nowrap',
  overflow: 'hidden',
  width: '220px',
  height: '60px',
  padding: '13px 8px',
  [theme.breakpoints.down('sm')]: {
    display: 'none'
  }
}));
const AdminAppBarLogoImg = styled('i', {
  name: 'AdminAppBarLogo',
  slot: 'Image'
})({
  backgroundSize: 'contain',
  backgroundRepeat: 'no-repeat',
  backgroundPosition: 'center left',
  display: 'block',
  height: '32px'
});

const SubMenu = styled('div', {
  name: 'AdminAppBar',
  slot: 'SubMenu'
})({
  display: 'inline-flex',
  justifyContent: 'flex-end',
  flex: 1,
  position: 'relative'
});

const FixSpace = styled('div', {
  name: 'AdminAppBar',
  slot: 'FixSpace'
})({ height: 58 });

export type Props = UIBlockProps & {
  toggleDrawer: () => void;
};

export default function AdminAppBar({ toggleDrawer, drawerVisible }: Props) {
  const classes = useStyles();
  const { assetUrl, i18n, setting, acl, useLoggedIn } = useGlobal();
  const theme = useTheme();
  const logo =
    theme.palette.mode === 'dark'
      ? assetUrl('layout.image_logo_dark')
      : assetUrl('layout.image_logo');

  const handleToggleDrawer = () => {
    if (toggleDrawer) toggleDrawer();
  };

  const handleAction = () => {};

  const pathname = '/admincp';

  const items = filterShowWhen(subMenu.items, {
    setting,
    acl
  });

  const loggedIn = useLoggedIn();

  if (!loggedIn) return null;

  return (
    <>
      <AppBar color="inherit" position="fixed" data-testid="layoutSlotHeader">
        <Toolbar sx={{ p: '0 !important' }}>
          <Tooltip
            title={i18n.formatMessage({
              id: drawerVisible ? 'collapse_menu' : 'expand_menu'
            })}
          >
            <ToggleNavButton
              onClick={handleToggleDrawer}
              data-testid="toggleMenu"
            >
              <LineIcon icon="ico-navbar" />
            </ToggleNavButton>
          </Tooltip>
          <AdminAppBarLogo>
            <RouteLink to="/admincp" data-testid="imgLogo">
              <AdminAppBarLogoImg
                style={{
                  backgroundImage: `url(${logo})`
                }}
              />
            </RouteLink>
          </AdminAppBarLogo>
          <AdminSearchForm />
          <SubMenu data-testid="menuAppBar">
            <MenuItems
              prefixName="appbar.item."
              fallbackName="link"
              items={items}
              handleAction={handleAction}
              classes={classes}
              pathname={pathname}
            />
          </SubMenu>
        </Toolbar>
      </AppBar>
      <FixSpace />
    </>
  );
}

import { Backdrop } from '@mui/material';
import React, { useState } from 'react';
import useGlobal from '../hooks/useGlobal';
import useStyles from './styles';

const STORAGE_KEY = 'sidebar_toggle';

export default function Template({ children }) {
  const classes = useStyles();
  const { jsxBackend, localStore, getAcl } = useGlobal();
  const initClose = localStore.get(STORAGE_KEY);
  const isDesktop = Boolean(window.innerWidth > 1200);

  const hasAdminAccess = getAcl('core.admincp.has_admin_access');

  const [open, setOpen] = useState<boolean>(Boolean(isDesktop && !initClose));

  const toggleDrawer = () => {
    setOpen(value => !value);
  };

  React.useEffect(() => {
    localStore.set(STORAGE_KEY, open ? undefined : '1');
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open]);

  if (!hasAdminAccess) return children;

  return (
    <div className={classes.siteWide}>
      {jsxBackend.render({
        component: 'core.block.AdminAppBar',
        props: { toggleDrawer, drawerVisible: open, isDesktop }
      })}
      {isDesktop ? null : (
        <Backdrop
          className={classes.backdrop}
          open={open}
          onClick={toggleDrawer}
        />
      )}
      <div className={classes.body}>
        {jsxBackend.render({
          component: 'core.block.AdminSideMenu',
          props: { toggleDrawer, drawerVisible: open, isDesktop }
        })}
        <div className={classes.content}>{children}</div>
      </div>
    </div>
  );
}

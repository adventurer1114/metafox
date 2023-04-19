import {
  SideMenuBlockProps,
  useAppUI,
  useGlobal,
  useLocation,
  HistoryState
} from '@metafox/framework';
import { Block, ScrollContainer } from '@metafox/layout';
import { LineIcon } from '@metafox/ui';
import { IconButton, Popover } from '@mui/material';
import React, { useEffect, useRef, useState } from 'react';
import useStyles from './styles';

export type Props = SideMenuBlockProps & {
  menuName: string;
  appName?: string;
  contents: { name: string; props?: any }[];
};

export default function SideAppMobileBlock({
  blockProps,
  appName,
  menuName,
  contents,
  title: initialTitle
}: Props) {
  const classes = useStyles();
  const { usePageParams, jsxBackend, i18n } = useGlobal();
  const { appName: pageAppName } = usePageParams();
  const sidebarHeader = useAppUI(pageAppName, 'homepageHeader');
  const { pathname: _pathname, search, state } = useLocation<HistoryState>();

  const [open, setOpen] = useState(false);
  const anchorRef = useRef(null);
  const pathname = state?.as || _pathname;

  const Components =
    contents?.length > 0
      ? contents
          .map(item => ({
            name: jsxBackend.get(item.name),
            props: item.props || {}
          }))
          .filter(item => item.name)
      : [];

  useEffect(() => {
    return () => {
      setOpen(false);
    };
  }, [pathname, search]);

  if (!sidebarHeader) return null;

  const { title } = sidebarHeader;

  const handleClose = () => {
    setOpen(false);
  };

  const handleClick = () => {
    setOpen(prev => !prev);
  };

  return (
    <Block>
      <div className={classes.root}>
        <div className={classes.headerBlock}>
          <div className={classes.title}>
            {i18n.formatMessage({ id: initialTitle || title })}
          </div>
          <IconButton
            onClick={handleClick}
            variant="outlined-square"
            size="small"
            color="primary"
            className={classes.btn}
            ref={anchorRef}
          >
            <LineIcon
              icon={open ? 'ico-angle-up' : 'ico-angle-down'}
            ></LineIcon>
          </IconButton>
          <Popover
            open={open}
            anchorEl={anchorRef.current}
            onClose={handleClose}
            disablePortal
            style={{ minWidth: '100%' }}
            transitionDuration={0}
            className={classes.popover}
            anchorOrigin={{
              vertical: 'bottom',
              horizontal: 'left'
            }}
            transformOrigin={{
              vertical: 'bottom',
              horizontal: 'left'
            }}
          >
            <ScrollContainer autoHide autoHeightMax={'100%'}>
              <div className={classes.contentWrapper}>
                {Components.map((Component, index) => (
                  <Component.name key={index.toString()} {...Component.props} />
                ))}
              </div>
            </ScrollContainer>
          </Popover>
        </div>
      </div>
    </Block>
  );
}

/**
 * @type: ui
 * name: adminSideMenu.as.subMenu
 */
import { RouteLink, useGlobal } from '@metafox/framework';
import { LineIcon, MenuItemViewProps, ClickOutsideListener } from '@metafox/ui';
import React, { useEffect } from 'react';
import clsx from 'clsx';
import { orderBy } from 'lodash';
import { filterShowWhen } from '@metafox/utils';
import { Paper, Popper, Typography } from '@mui/material';
import { ScrollContainer } from '@metafox/layout';

export default function MenuItem(props: MenuItemViewProps) {
  const {
    item,
    classes,
    pathname,
    active: showActive,
    minimize,
    activeHover,
    handlePopoverClose,
    handlePopoverOpen
  } = props;
  const anchorRef = React.useRef<HTMLDivElement>();
  const { useSession, getAcl, getSetting, useTheme } = useGlobal();

  const [open, setOpen] = React.useState<boolean>(showActive);
  const session = useSession();
  const acl = getAcl();
  const setting = getSetting();
  const paperRef = React.useRef();
  const theme = useTheme();
  const scrollRef = React.useRef<HTMLDivElement>();

  const handleToggle = () => setOpen(open => !open);

  useEffect(() => {
    setOpen(showActive);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [showActive]);

  const items = filterShowWhen(item?.items, { session, acl, setting });

  const openTypeMinimize = minimize && activeHover;
  const openTypeFull = open && !minimize;

  if (!items?.length) return null;

  return (
    <>
      <div
        className={clsx(classes.menuItem, {
          [classes.menuItemMinimize]: minimize,
          [classes.menuItemMinimizeOpen]: openTypeMinimize,
          [classes.menuItemActive]: showActive && minimize
        })}
        onClick={minimize ? handlePopoverOpen : handleToggle}
        data-testid={item.testid || item.name}
        onMouseEnter={handlePopoverOpen}
        onMouseLeave={handlePopoverClose}
        ref={anchorRef}
      >
        <div className={classes.menuItemLink}>
          <LineIcon icon={item.icon} className={classes.menuItemIcon} />
          {!minimize ? <span>{item.label}</span> : null}
        </div>
        {!minimize ? (
          <LineIcon
            icon={open ? 'ico-angle-up' : 'ico-angle-down'}
            className={classes.iconArrow}
          />
        ) : null}
      </div>
      {openTypeFull ? (
        <div className={classes.subMenu}>
          {orderBy(items, ['label']).map((subItem, index) => (
            <div
              className={clsx(
                classes.subMenuItem,
                showActive &&
                  subItem.to === pathname &&
                  classes.subMenuItemActive
              )}
              key={index.toString()}
              onClick={null}
            >
              <RouteLink
                to={subItem.to}
                data-testid={subItem.testid || subItem.name}
                target={subItem.target}
                className={classes.subMenuItemLink}
              >
                <span>{subItem.label}</span>
              </RouteLink>
            </div>
          ))}
        </div>
      ) : null}
      {openTypeMinimize ? (
        <ClickOutsideListener
          onClickAway={handlePopoverClose}
          excludeRef={anchorRef}
        >
          <Popper
            id={`k${item.name}`}
            open={openTypeMinimize}
            placement={'right-start'}
            anchorEl={anchorRef.current}
            style={{
              zIndex: theme.zIndex.modal
            }}
            onMouseEnter={handlePopoverOpen}
          >
            <Paper
              className={classes.subMenu}
              ref={paperRef}
              sx={{ background: 'rgb(35, 48, 68)', width: '208px' }}
            >
              <ScrollContainer
                autoHide
                autoHeight
                autoHeightMax={'70vh'}
                ref={scrollRef}
              >
                <div onMouseLeave={handlePopoverClose}>
                  <div
                    className={clsx(
                      classes.menuItemLinkPoper,
                      classes.subMenuItemLink,
                      classes.menuItemLinkPoperParent
                    )}
                  >
                    <Typography
                      fontWeight={600}
                      sx={{ color: '#fff !important' }}
                    >
                      {item.label}
                    </Typography>
                  </div>
                  <div>
                    {orderBy(items, ['label']).map((subItem, index) => (
                      <div
                        className={clsx(
                          classes.subMenuItem,
                          showActive &&
                            subItem.to === pathname &&
                            classes.subMenuItemActive
                        )}
                        key={index.toString()}
                        onClick={null}
                      >
                        <RouteLink
                          to={subItem.to}
                          data-testid={subItem.testid || subItem.name}
                          target={subItem.target}
                          className={clsx(
                            classes.subMenuItemLink,
                            classes.menuItemLinkPoper
                          )}
                        >
                          <span>{subItem.label}</span>
                        </RouteLink>
                      </div>
                    ))}
                  </div>
                </div>
              </ScrollContainer>
            </Paper>
          </Popper>
        </ClickOutsideListener>
      ) : null}
    </>
  );
}

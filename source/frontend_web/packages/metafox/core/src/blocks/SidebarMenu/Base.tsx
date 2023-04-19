import { useAppMenu, useGlobal, useLocation } from '@metafox/framework';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import { SidebarMenuItem, UIBlockViewProps } from '@metafox/ui';
import { filterShowWhen } from '@metafox/utils';
import clsx from 'clsx';
import React, { useCallback, useMemo } from 'react';
import useStyles from './styles';

export interface Props extends UIBlockViewProps {
  displayLimit: number;
}

export default function SidebarMenuBase({
  title,
  blockProps,
  displayLimit: limit = 8
}) {
  const { i18n, getAcl, useSession, getSetting, usePreference } = useGlobal();
  const session = useSession();
  const acl = getAcl();
  const setting = getSetting();
  const [open, setOpen] = React.useState<boolean>(false);
  const classes = useStyles();
  const primaryMenu = useAppMenu('core', 'primaryMenu');
  const { themeId } = usePreference();
  const location = useLocation();

  const toggleOpen = useCallback(() => {
    setOpen(prev => !prev);
  }, []);

  const items = useMemo(() => {
    if (!primaryMenu) return [];

    const { items } = primaryMenu;

    if (items.length < limit) {
      return items;
    } else if (open) {
      return items.filter(Boolean).concat({
        icon: 'ico-angle-up',
        testid: 'less',
        label: i18n.formatMessage({ id: 'less' }),
        onClick: toggleOpen
      });
    } else {
      return items.slice(0, limit).concat({
        icon: 'ico-angle-down',
        testid: 'more',
        label: i18n.formatMessage({ id: 'more' }),
        onClick: toggleOpen
      });
    }
  }, [primaryMenu, limit, open, i18n, toggleOpen]);

  if (!primaryMenu?.items) return null;

  const filteredItems = filterShowWhen(items, { acl, setting, session });

  const themeClass =
    themeId === 'transparency' ? classes.transparency : classes.paper;

  return (
    <Block testid="blockSidebarMenu">
      <BlockHeader title={title} />
      <BlockContent>
        <nav role="navigation">
          <div className={clsx(classes.menuList, themeClass)} role="menu">
            {filteredItems.map((item, index) => (
              <SidebarMenuItem
                {...item}
                variant="contained"
                key={index.toString()}
                iconVariant="iconVariantCircled"
                classes={classes}
                active={location.pathname === item.to}
              />
            ))}
          </div>
        </nav>
      </BlockContent>
    </Block>
  );
}

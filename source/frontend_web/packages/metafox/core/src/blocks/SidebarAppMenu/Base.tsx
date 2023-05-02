import { SideMenuBlockProps, useAppMenu, useGlobal } from '@metafox/framework';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import { filterShowWhen } from '@metafox/utils';
import React, { useCallback, useMemo } from 'react';
import useStyles from './styles';

export type Props = Partial<SideMenuBlockProps> & {
  menuName?: string;
  appName?: string;
  displayTitle?: boolean;
  displayViewMore?: boolean;
  item: Record<string, any>;
  displayLimit?: number;
};

export default function SideAppMenuBlock({
  displayTitle,
  displayViewMore = true,
  title,
  blockProps,
  appName,
  menuName,
  item,
  displayLimit = 5
}: Props) {
  const classes = useStyles();
  const {
    usePageParams,
    useSession,
    getAcl,
    jsxBackend,
    compactUrl,
    getSetting,
    i18n,
    location
  } = useGlobal();
  const acl = getAcl();
  const { tab, pathname, id } = usePageParams();
  const path = pathname || location?.pathname;
  const session = useSession();
  const setting = getSetting();
  const [open, setOpen] = React.useState<boolean>(false);

  const toggleOpen = useCallback(() => {
    setOpen(prev => !prev);
  }, []);

  const { appName: pageAppName, module_name } = usePageParams();
  const menu = useAppMenu(
    appName || pageAppName || module_name || 'core',
    menuName
  );

  // filter based on showWhen property
  const itemFilter = filterShowWhen(menu?.items, {
    setting,
    session,
    acl,
    item
  });

  const countButton = itemFilter.filter(
    item => item.as === 'sidebarButton'
  ).length;

  const items = useMemo(() => {
    const items = itemFilter;

    // has any items ?
    if (!items.length) return [];

    if (items.length <= displayLimit + countButton || !displayViewMore)
      return items;

    if (open) {
      items.splice(items.length - countButton, 0, {
        icon: 'ico-angle-up',
        testid: 'less',
        label: i18n.formatMessage({ id: 'less' }),
        onClick: toggleOpen,
        to: null
      });
    } else {
      items.splice(displayLimit, items.length - (displayLimit + countButton), {
        icon: 'ico-angle-down',
        testid: 'more',
        label: i18n.formatMessage({ id: 'more' }),
        onClick: toggleOpen,
        to: null
      });
    }

    return items;
  }, [
    itemFilter,
    displayLimit,
    countButton,
    displayViewMore,
    open,
    i18n,
    toggleOpen
  ]);

  return (
    <Block testid="blockSidebarMenu">
      {displayTitle && <BlockHeader title={title} />}
      <BlockContent>
        <div role="menu">
          {items.map((item, index) =>
            jsxBackend.render({
              component: `menuItem.as.${item.as || 'sidebarLink'}`,
              props: {
                key: index.toString(),
                variant: 'contained',
                item: {
                  ...item,
                  to: item.to ? compactUrl(item.to, { id }) : undefined
                },
                classes,
                active: (tab && tab === item.tab) || path === item.to
              }
            })
          )}
        </div>
      </BlockContent>
    </Block>
  );
}

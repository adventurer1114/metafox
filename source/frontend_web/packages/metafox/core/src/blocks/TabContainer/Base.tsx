import {
  ListViewBlockProps,
  useGetItem,
  useGlobal,
  useLocation
} from '@metafox/framework';
import { Block, BlockContent, BlockHeader, SearchBox } from '@metafox/layout';
import { LineIcon, UIBlockViewProps } from '@metafox/ui';
import { UserItemShape } from '@metafox/user';
import { filterShowWhen } from '@metafox/utils';
import { Menu, Tab, Tabs } from '@mui/material';
import clsx from 'clsx';
import produce from 'immer';
import { isArray } from 'lodash';
import qs from 'querystring';
import React, { useCallback, useEffect, useRef } from 'react';
import useStyles from './styles';

type TabContainerProps = UIBlockViewProps &
  ListViewBlockProps & {
    profileActionMenu?: string;
    hasSearchBox?: string;
    item: any;
    user: UserItemShape;
  };

const convertTabToArray = (tabs: any[]) => {
  if (!isArray(tabs)) return [];

  return tabs.filter(Boolean);
};

export default function TabContainer({
  title,
  tabProps = {
    tabs: [],
    tabsNoSearchBox: [],
    disableGutter: true,
    activeTab: '',
    placeholderSearch: 'search_dot'
  },
  elements,
  hasSearchBox,
  item,
  user,
  compose
}: TabContainerProps) {
  const {
    tabs: items,
    tabsNoSearchBox = [],
    disableGutter,
    activeTab
  } = tabProps;

  const {
    navigate,
    jsxBackend,
    useSession,
    usePageParams,
    i18n,
    useIsMobile,
    getAcl
  } = useGlobal();
  const isMobile = useIsMobile();
  const acl = getAcl();
  const defaultTab = React.useMemo(
    () => activeTab || convertTabToArray(tabProps.tabs)[0]?.tab || '',
    [activeTab, tabProps.tabs]
  );
  const [tab, setTab] = React.useState<string>(defaultTab);

  const location = useLocation();
  const refMenuMore = useRef(null);
  const [query, setQuery] = React.useState('');
  const session = useSession();
  const { user: authUser, loggedIn } = session;
  const pageParams = usePageParams();
  const classes = useStyles();
  const detailUser = useGetItem(`user.entities.user.${authUser?.id}`);
  const className = clsx(disableGutter && classes.disableGutter);
  const [open, setOpen] = React.useState<boolean>(false);
  const element = elements.find(element => element.props.name === tab);

  const isAuthUser =
    (authUser?.id === item?.id || user?.id === authUser?.id) && loggedIn;
  const [state, setState] = React.useState<number>(0);
  const searchRef = useRef<HTMLDivElement>();
  const searchHiddenRef = useRef<HTMLDivElement>();
  const totalRef = useRef<HTMLDivElement>();
  const hiddenTabsRef = useRef<HTMLDivElement>();
  const moreTabRef = useRef<HTMLDivElement>();
  const tabRef = useRef<HTMLDivElement>();
  const [anchorEl, setAnchorEl] = React.useState<null | HTMLElement>(null);

  const selectMoreTabItem = (event: any, tab: any) => {
    event.stopPropagation();
    setTab(tab);
    const stab = qs.stringify({ stab: tab });

    navigate(
      {
        pathname: location.pathname,
        search: `?${stab}`
      },
      {
        keepScroll: true
      }
    );

    closeMenu();
  };

  const displayTab = filterShowWhen(items, {
    isAuthUser,
    session,
    item,
    authUser: detailUser,
    acl
  });

  const tabs = displayTab.map(item => item.tab);
  const tabValue = items.find(item => item.tab === tab);

  React.useEffect(() => {
    if (
      !tabs.includes(tab) &&
      tabValue &&
      tabValue?.redirectWhenNoPermission &&
      tabs.includes(tabValue?.redirectWhenNoPermission)
    ) {
      navigate({
        pathname: location.pathname,
        search: `?stab=${tabValue?.redirectWhenNoPermission || defaultTab}`
      });
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [tab, displayTab, items]);

  const moreTab = useCallback(
    (isHidden: Boolean) => (
      <div className={classes.secondMenu} ref={refMenuMore}>
        <div className={classes.tabItem}>
          {i18n.formatMessage({ id: 'more' })}&nbsp;
          <LineIcon icon="ico-caret-down" />
        </div>
        <Menu open={open} anchorEl={anchorEl} onClose={closeMenu}>
          {(state >= displayTab.length
            ? displayTab
            : displayTab.slice(state)
          ).map((itemTab, index) => (
            <div
              className={clsx(
                classes.menuItem,
                itemTab.tab === tab && classes.tabItemActive
              )}
              key={index.toString()}
              onClick={event => selectMoreTabItem(event, itemTab.tab)}
            >
              {i18n.formatMessage({ id: itemTab.label })}
            </div>
          ))}
        </Menu>
      </div>
    ),
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [anchorEl, displayTab, open, selectMoreTabItem, state, tab]
  );

  const itemsNoSearchBox = convertTabToArray(tabsNoSearchBox);

  const hasSearch = hasSearchBox && !itemsNoSearchBox.includes(tab);

  const onResize = React.useCallback(() => {
    const cc: HTMLCollection[] = hiddenTabsRef.current?.children;

    if (!cc || !cc.length) return;

    const maxWidth = totalRef.current.getBoundingClientRect().width;
    const tabMoreWidth = moreTabRef.current?.getBoundingClientRect().width;

    const searchWidth =
      hasSearch && !isMobile
        ? searchHiddenRef.current?.getBoundingClientRect().width
        : 0;

    let totalWidth = 0;
    let index = -1;

    while (totalWidth + tabMoreWidth + searchWidth < maxWidth) {
      index++;
      totalWidth += cc[index]?.getBoundingClientRect()?.width;
    }

    const maxTabsWhenHasMore = 3;
    const countTabHasMore =
      index < maxTabsWhenHasMore ? index : maxTabsWhenHasMore;

    if (index === maxTabsWhenHasMore && cc.length === maxTabsWhenHasMore + 1) {
      setState(maxTabsWhenHasMore - 1);
    } else {
      setState(index >= cc.length ? cc.length : countTabHasMore);
    }
  }, [hasSearch, isMobile]);

  useEffect(() => {
    onResize();
  }, [onResize, tab, displayTab]);

  useEffect(() => {
    const { stab } = pageParams;
    const isTabOnDisplayTab = displayTab.some(tab => tab.tab === stab);

    if (!isTabOnDisplayTab && stab) {
      setTab(activeTab || convertTabToArray(tabProps.tabs)[0].tab);

      navigate({
        search: `?stab=${activeTab || convertTabToArray(tabProps.tabs)[0].tab}`
      });

      return;
    }

    if (!isArray(tabProps?.tabs)) return;

    setTab(stab || activeTab || convertTabToArray(tabProps.tabs)[0].tab);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [tabProps.tabs, activeTab, pageParams]);

  const closeMenu = () => setOpen(false);

  const toggleOpen = (event: React.MouseEvent<HTMLButtonElement>) => {
    setOpen(true);
    setAnchorEl(event.currentTarget);
  };

  const handleChange = (_: any, tab: any) => {
    if (tab !== 'more') {
      const stab = qs.stringify({ stab: tab });

      navigate(
        {
          pathname: location.pathname,
          search: `?${stab}`
        },
        {
          keepScroll: true
        }
      );
    }
  };

  const modifiedElement =
    element?.props?.elements?.length &&
    produce(element, draft => {
      for (let i = 0; i < draft.props.elements.length; i++) {
        // draft.props.elements[i].props.hasSearchBox = true;
        draft.props.elements[i].props.query = query;
      }
    });

  compose(props => {
    props.hasSearchBox = false;
  });

  return (
    <Block>
      <BlockHeader title={title}></BlockHeader>
      <BlockContent>
        <div className={classes.subTabWrapper} ref={totalRef}>
          <div ref={hiddenTabsRef} className={classes.hiddenTabs}>
            {displayTab.map((tab, index) => (
              <Tab
                ref={tabRef}
                aria-label={tab.tab}
                value={tab.tab}
                label={i18n.formatMessage({ id: tab.label })}
                key={index.toString()}
                className={classes.tab}
              />
            ))}
          </div>
          <Tabs
            value={tab}
            onChange={handleChange}
            textColor="primary"
            indicatorColor="primary"
          >
            {(state >= displayTab.length
              ? displayTab
              : displayTab.slice(0, state)
            ).map((tab, index) => (
              <Tab
                aria-label={tab.tab}
                value={tab.tab}
                label={i18n.formatMessage({ id: tab.label })}
                key={index.toString()}
                className={classes.tab}
              />
            ))}
            {state < displayTab.length && (
              <Tab
                onClick={toggleOpen}
                value={'more'}
                label={moreTab(true)}
                ref={refMenuMore}
              />
            )}
          </Tabs>
          <div className={classes.hiddenTabs} ref={moreTabRef}>
            <Tab value={'more'} label={moreTab(false)} ref={moreTabRef} />
          </div>

          <div className={classes.hiddenTabs} ref={searchHiddenRef}>
            <SearchBox
              placeholder={tabProps?.placeholderSearch}
              onQueryChange={setQuery}
              sx={{
                width: { sm: 'auto', xs: '100%' },
                margin: { sm: 'initial', xs: '16px 0 0 0' },
                padding: '10px'
              }}
            />
          </div>
          {hasSearch ? (
            <SearchBox
              ref={searchRef}
              placeholder={
                tabValue?.placeholderSearch || tabProps?.placeholderSearch
              }
              onQueryChange={setQuery}
              sx={{
                width: { sm: 'auto', xs: '100%' },
                margin: { sm: 'initial', xs: '16px 0 0 0' }
              }}
            />
          ) : null}
        </div>
        <div className={className}>
          {modifiedElement ? jsxBackend.render(modifiedElement) : null}
        </div>
      </BlockContent>
    </Block>
  );
}

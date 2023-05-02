import {
  MenuShape,
  useAppMenu,
  useGlobal,
  useIsMobile,
  useLocation,
  useResourceAction
} from '@metafox/framework';
import { Block, BlockContent, usePageParams } from '@metafox/layout';
import * as React from 'react';
import { APP_SEARCH } from '@metafox/search';
import { compactData } from '@metafox/utils';
import { upperFirst, omit } from 'lodash';
import { Button, Box, styled, Typography } from '@mui/material';
import qs from 'query-string';

const mapingItemLayout = {
  music: 'Music - Main Card',
  music_song: 'Music - Main Card',
  music_album: 'Music - Main Card',
  music_playlist: 'Music - Main Card'
};

const SearchItemStyled = styled(Box, { name: 'SearchItem' })(({ theme }) => ({
  paddingTop: theme.spacing(2),
  paddingBottom: theme.spacing(1),
  [theme.breakpoints.up('sm')]: {
    width: '600px'
  },
  [theme.breakpoints.down('sm')]: {
    paddingLeft: theme.spacing(2),
    paddingRight: theme.spacing(2)
  }
}));

const APP_FEED = 'feed';

export default function SearchItem({
  item,
  searchSectionAction = 'viewAll'
}: {
  item?: Record<string, any>;
  searchSectionAction?: string;
}) {
  const pageParams = usePageParams();
  const { ListView, navigate, i18n } = useGlobal();
  const { appName, resourceName, is_hashtag } = pageParams;
  const isMobile = useIsMobile();

  const menu: MenuShape = useAppMenu(APP_SEARCH, 'webCategoryMenu');

  const { view, id } = usePageParams();
  const location = useLocation();

  const itemParam = item ? omit(item, ['id']) : { item_type: view };

  const dataSource = useResourceAction(
    appName,
    resourceName,
    searchSectionAction
  );
  const { item_type } = Object.assign({}, itemParam);

  const handleViewAll = () => {
    const search = qs.stringify(
      Object.assign({}, qs.parse(location.search), {
        view: item_type
      })
    );

    let pathname = id
      ? `/${appName}/search/${id}?${search}`
      : `/search/${item_type}${location.search}`;

    if (is_hashtag) {
      pathname = `/hashtag/search?${search}`;
    }

    navigate(pathname);
  };

  const label =
    menu?.items?.find(item => item?.name === view)?.label ||
    i18n.formatMessage({ id: 'search_results' });

  return (
    <SearchItemStyled>
      <Block>
        <BlockContent>
          {view !== 'all' && (
            <Typography variant="h4" sx={{ pb: 2 }}>
              {label}
            </Typography>
          )}
          <ListView
            testid="searchListing"
            blockLayout="Search Main Lists"
            gridLayout="Search - Main Card"
            itemLayout={
              (mapingItemLayout[item_type] && isMobile
                ? `${mapingItemLayout[item_type]} - Mobile`
                : mapingItemLayout[item_type]) ||
              `${upperFirst(item_type)} - Main Card`
            }
            itemView={`${item_type}.itemView.mainCard`}
            dataSource={{
              apiUrl: dataSource.apiUrl,
              apiParams: compactData(dataSource.apiParams, {
                ...pageParams,
                ...itemParam
              })
            }}
            canLoadMore={item_type === APP_FEED || !!item}
          />
          {item && item_type !== 'feed' && (
            <Button variant="link" onClick={handleViewAll}>
              {i18n.formatMessage(
                { id: 'view_all_results_for_item' },
                { label: item.label }
              )}
            </Button>
          )}
        </BlockContent>
      </Block>
    </SearchItemStyled>
  );
}

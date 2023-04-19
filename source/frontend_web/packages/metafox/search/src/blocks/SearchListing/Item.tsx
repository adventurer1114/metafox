import {
  MenuShape,
  useAppMenu,
  useGlobal,
  useLocation,
  useResourceAction
} from '@metafox/framework';
import { Block, BlockContent, usePageParams } from '@metafox/layout';
import * as React from 'react';
import { APP_SEARCH } from '@metafox/search';
import { compactData } from '@metafox/utils';
import { upperFirst, omit } from 'lodash';
import { Button, Box, styled } from '@mui/material';
import qs from 'query-string';

const SearchItemStyled = styled(Box, { name: 'SearchItem' })(({ theme }) => ({
  paddingTop: 2,
  paddingBottom: 1,
  width: '600px'
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
          <ListView
            testid="searchListing"
            blockLayout="Search Main Lists"
            gridLayout="Search - Main Card"
            itemLayout={`${upperFirst(item_type)} - Main Card`}
            itemView={`${item_type}.itemView.mainCard`}
            dataSource={{
              apiUrl: dataSource.apiUrl,
              apiParams: compactData(dataSource.apiParams, {
                ...pageParams,
                ...itemParam
              })
            }}
            blockProps={{
              titleStyle: {
                component: 'h4',
                sx: {
                  typography: 'h4',
                  marginBottom: '16px',
                  marginTop: '16px'
                }
              }
            }}
            canLoadMore={item_type === APP_FEED || !!item}
            title={view !== 'all' && label}
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

import {
  getPagingSelector,
  GlobalState,
  ListViewBlockProps,
  PAGINATION,
  PAGINATION_CLEAR,
  PAGINATION_FULFILL_PAGE,
  PAGINATION_INIT,
  PAGINATION_REFRESH,
  PAGINATION_UPDATE_LAST_READ,
  useAbortControl,
  useGlobal,
  useResourceAction,
  useScrollEnd
} from '@metafox/framework';
import qs from 'query-string';
import React from 'react';
import { useSelector } from 'react-redux';
import { omit } from 'lodash';

export default function withPagination<
  T extends ListViewBlockProps = ListViewBlockProps
>(ListView: React.FC<T>): React.FC<T> {
  function WithPagination(props: T) {
    const { usePageParams, layoutBackend, dispatch, compactUrl, compactData } =
      useGlobal();

    const {
      dataSource,
      clearDataOnUnMount,
      canLoadMore,
      loadMoreType = 'scroll',
      lastReadMode,
      maxPageNumber,
      numberOfItemsPerPage,
      hasSearchBox,
      prefixPagingId,
      pagingId: pageId,
      query: queryProp = '',
      compose,
      moduleName,
      resourceName,
      actionName,
      acceptQuerySearch,
      pageParamsDefault
    } = props;

    const styles = layoutBackend.normalizeDisplayingPresets(props);

    const pageParams = usePageParams();
    const dataSourceConfig = useResourceAction(
      moduleName,
      resourceName,
      actionName
    );
    const [query, onQueryChange] = React.useState<string>(queryProp);
    const isLoadMoreScroll = canLoadMore && loadMoreType === 'scroll';
    const isLoadMoreButton = canLoadMore && loadMoreType === 'button';
    const isLoadMorePagination = canLoadMore && loadMoreType === 'pagination';

    const config = dataSourceConfig || dataSource;

    const apiParams = compactData(
      config.apiParams,
      { ...pageParamsDefault, ...pageParams },
      config.apiRules
    );

    if (compose)
      compose(props => {
        props.onQueryChange = onQueryChange;
      });

    if (query && (hasSearchBox || acceptQuerySearch)) {
      apiParams.q = query;
    }

    let pagingId = pageId;

    if (!pagingId) {
      if (config.pagingId) {
        pagingId = compactUrl(`${config.pagingId}`, pageParams);
      } else {
        pagingId = `${compactUrl(config.apiUrl, pageParams)}?${qs.stringify(
          omit(apiParams, ['page'])
        )}`;
      }
    }

    const abortId = useAbortControl(pagingId);

    const pagingData = useSelector((state: GlobalState) =>
      getPagingSelector(state, pagingId)
    );

    React.useEffect(() => {
      if (!pagingData) return;

      if (
        (pagingData.dirty && !pagingData.ids.length) ||
        (pagingData.dirty && pagingData.refreshing)
      ) {
        dispatch({
          type: PAGINATION_REFRESH,
          payload: { pagingId }
          // meta: { abortId }
        });

        return;
      }

      if (
        !pagingData.loading &&
        pagingData.dirty &&
        pagingData.paginationType === 'pagination'
      ) {
        dispatch({
          type: PAGINATION_FULFILL_PAGE,
          payload: { pagingId, currentPage: pageParams?.page }
          // meta: { abortId }
        });

        return;
      }
      // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [pagingData, pagingId]);

    const loadMore = React.useCallback(
      (type: string = PAGINATION) => {
        dispatch({
          type,
          payload: {
            apiUrl: compactUrl(config.apiUrl, pageParams),
            apiParams,
            pagingId,
            isLoadMorePagination,
            isLoadMoreButton,
            maxPageNumber,
            canLoadMore,
            lastReadMode,
            numberOfItemsPerPage
          },
          meta: { abortId }
        });
      },
      // eslint-disable-next-line react-hooks/exhaustive-deps
      [config.apiUrl, query, pagingId, abortId, pageParams?.page]
    );

    const handleUpdateLastRead = React.useCallback(
      (type: string = PAGINATION_UPDATE_LAST_READ) => {
        dispatch({
          type,
          payload: {
            pagingId
          }
        });
      },
      // eslint-disable-next-line react-hooks/exhaustive-deps
      [pagingId]
    );

    useScrollEnd(isLoadMoreScroll ? loadMore : undefined);
    React.useEffect(() => {
      onQueryChange(queryProp);
    }, [queryProp]);

    // ensure there are no paging is cached or not ?
    React.useEffect(() => {
      loadMore(PAGINATION_INIT);

      if (clearDataOnUnMount) {
        return () => {
          dispatch({
            type: PAGINATION_CLEAR,
            payload: { pagingId, prefixPagingId },
            meta: { abortId }
          });
        };
      }
      // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [pagingId, query, clearDataOnUnMount, abortId, pageParams]);

    React.useEffect(() => {
      if (!pagingData?.error) return;

      // throw new Error('core.block.error404');
    }, [pagingData]);

    return (
      <ListView
        onQueryChange={onQueryChange as any}
        pagingId={pagingId}
        loadMore={loadMore}
        handleUpdateLastRead={
          lastReadMode && isLoadMorePagination ? handleUpdateLastRead : false
        }
        isLoadMoreScroll={isLoadMoreScroll}
        isLoadMoreButton={isLoadMoreButton}
        isLoadMorePagination={isLoadMorePagination}
        {...styles}
        {...props}
      />
    );
  }

  WithPagination.displayName = `withPagination(${ListView.displayName})`;

  return WithPagination;
}

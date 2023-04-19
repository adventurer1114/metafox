/**
 * @type: service
 * name: ListView
 */

import {
  getPagingSelector,
  GlobalState,
  initPagingState,
  ListViewBlockProps,
  PagingState,
  useGlobal,
  useScrollEnd,
  withPagination
} from '@metafox/framework';
import {
  Block,
  BlockContent,
  BlockContext,
  BlockHeader
} from '@metafox/layout';
import { Grid } from '@mui/material';
import { get, isArray, range } from 'lodash';
import React from 'react';
import { useSelector } from 'react-redux';

function ListView({
  itemView,
  itemProps = {},
  blockProps,
  gridItemProps = {},
  gridContainerProps = { spacing: 2 },
  displayLimit,
  pagingId,
  canLoadMore,
  canLoadSmooth,
  loadMore,
  numberOfItemsPerPage,
  emptyPage = 'core.block.no_results',
  emptyPageProps,
  limitItemsLoadSmooth,
  title
}: ListViewBlockProps) {
  const { jsxBackend, i18n } = useGlobal();
  const ItemView = jsxBackend.get(itemView);
  const Skeleton = jsxBackend.get(`${itemView}.skeleton`);

  const [currentPage, setCurrentPage] = React.useState<number>(1);

  const paging =
    useSelector<GlobalState, PagingState>((state: GlobalState) =>
      getPagingSelector(state, pagingId)
    ) || initPagingState();

  useScrollEnd(
    canLoadMore ? () => setCurrentPage(prev => prev + 1) : undefined
  );

  const perPage = numberOfItemsPerPage || 20;
  const limit = displayLimit || 4;
  const { loading, refreshing, error, ended, initialized, noResultProps } =
    paging ?? {};
  const showLoadSmooth = canLoadSmooth && !ended;

  const data = canLoadMore
    ? paging.ids.slice(0, currentPage * perPage)
    : paging.ids.slice(0, displayLimit || paging.ids.length);

  React.useEffect(() => {
    if (
      (initialized && canLoadMore && !loading && loadMore && !ended) ||
      refreshing
    ) {
      loadMore();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [initialized, refreshing]);

  if (!ItemView) return null;

  if (error) {
    const message =
      get(error, 'response.data.error') || get(error, 'response.data.message');

    const errorName =
      get(error, 'response.status') === 403
        ? 'core.block.error403'
        : 'core.block.error404';
    const ErrorBlock = jsxBackend.get(errorName);

    return <ErrorBlock title={message} />;
  }

  if (!gridItemProps.xs) {
    gridItemProps.xs = 12;
  }

  if (!ItemView) return null;

  if (!initialized) {
    if (!Skeleton) {
      return <div>{i18n.formatMessage({ id: 'loading_dots' })}</div>;
    }

    return (
      <BlockContext.Provider
        value={{ itemProps, gridContainerProps, blockProps }}
      >
        <Block>
          <BlockHeader title={title} />
          <BlockContent>
            <Grid container {...gridContainerProps}>
              {range(0, limit).map(index => (
                <Skeleton
                  wrapAs={Grid}
                  wrapProps={gridItemProps}
                  itemProps={itemProps}
                  key={index.toString()}
                />
              ))}
            </Grid>
          </BlockContent>
        </Block>
      </BlockContext.Provider>
    );
  }

  if (!data.length && ended) {
    if (emptyPage === 'hide') return null;

    const NoResultsBlock = jsxBackend.get(emptyPage);
    const emptyProps = { ...emptyPageProps, ...noResultProps };

    return <NoResultsBlock {...emptyProps} />;
  }

  return (
    <BlockContext.Provider
      value={{ itemProps, gridContainerProps, blockProps }}
    >
      <Block>
        <BlockHeader title={title} />
        <BlockContent>
          <Grid container {...gridContainerProps}>
            {isArray(data) &&
              data.map(id => (
                <ItemView
                  identity={id}
                  itemProps={itemProps}
                  key={id.toString()}
                  wrapAs={Grid}
                  wrapProps={gridItemProps}
                />
              ))}
            {showLoadSmooth
              ? range(1, limitItemsLoadSmooth || limit).map(index => (
                  <Skeleton
                    wrapAs={Grid}
                    wrapProps={gridItemProps}
                    itemProps={itemProps}
                    key={index.toString()}
                  />
                ))
              : null}
          </Grid>
        </BlockContent>
      </Block>
    </BlockContext.Provider>
  );
}

export default withPagination(ListView);

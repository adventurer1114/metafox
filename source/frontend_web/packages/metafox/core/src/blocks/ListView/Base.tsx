import {
  getPagingSelector,
  GlobalState,
  initPagingState,
  ListViewBlockProps,
  PagingState,
  useGlobal,
  useScrollEnd,
  useHasScroll
} from '@metafox/framework';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
// layout
import { Grid, Skeleton as SkeletonDefault } from '@mui/material';
// components
import { isArray, range, get } from 'lodash';
import React from 'react';
import { useSelector } from 'react-redux';
import { Pagination, LoadMoreListingButton } from '@metafox/ui';
import { Box } from '@mui/system';

export default function ListViewBase({
  title,
  itemView,
  itemProps = {},
  gridItemProps = {},
  gridContainerProps = { spacing: 2 },
  displayLimit = 4,
  pagingId,
  canLoadMore,
  buttonMessageLoadmore = 'view_more',
  collapsible,
  messagePagination,
  handleUpdateLastRead,
  canLoadSmooth,
  loadMore,
  numberOfItemsPerPage,
  emptyPage = 'core.block.no_content',
  emptyPageProps,
  isLoadMoreScroll,
  isLoadMoreButton,
  isLoadMorePagination,
  errorPage,
  hasSort = false,
  titleBackend = false,
  moduleName,
  resourceName,
  actionSortName
}: ListViewBlockProps) {
  const { jsxBackend, usePageParams, i18n, useWidthBreakpoint } = useGlobal();
  const pageParams = usePageParams();
  const { page } = pageParams;
  const ItemView = jsxBackend.get(itemView);
  const Skeleton = jsxBackend.get(`${itemView}.skeleton`);
  const currentPageInitial = isLoadMorePagination ? parseInt(page || 1, 10) : 1;
  const [hasSCroll, checkSCrollExist] = useHasScroll(true);
  const mediaBreakpoint: string = useWidthBreakpoint();
  const { limit: limitCollapse = 10, as: uiButtonToggle } = collapsible || {};
  const ButtonToggle = jsxBackend.get(uiButtonToggle);
  const [collapsed, setCollapsed] = React.useState(true);
  // number skeleton loadmore is 2 line of grid
  const numberSkeleton =
    (12 / parseInt(get(gridItemProps, mediaBreakpoint) || 12, 10)) * 2;

  const [currentPage, setCurrentPage] =
    React.useState<number>(currentPageInitial);

  const paging =
    useSelector<GlobalState, PagingState>((state: GlobalState) =>
      getPagingSelector(state, pagingId)
    ) || initPagingState();
  const callbackScrollEnd = React.useCallback(() => {
    if (isLoadMoreScroll) {
      setCurrentPage(prev => prev + 1);
    }
  }, [isLoadMoreScroll]);

  useScrollEnd(callbackScrollEnd);

  const triggerLoadmore = React.useCallback(() => {
    loadMore();
    setCurrentPage(prev => prev + 1);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [pagingId]);

  React.useEffect(() => {
    if (checkSCrollExist) {
      checkSCrollExist();
    }
  }, [paging?.page, checkSCrollExist]);

  React.useEffect(() => {
    // triggerloadmore is container not have scroll bar
    if (
      isLoadMoreScroll &&
      !paging?.ended &&
      !paging?.loading &&
      !hasSCroll &&
      !paging?.dirty
    ) {
      triggerLoadmore();
    }
  }, [
    paging?.loading,
    paging?.page,
    isLoadMoreScroll,
    paging?.ended,
    paging?.dirty,
    hasSCroll,
    triggerLoadmore
  ]);
  const perPage = numberOfItemsPerPage || 20;
  let limitSkeleton = isLoadMorePagination ? perPage : numberSkeleton;

  if (!canLoadMore && displayLimit) {
    limitSkeleton = displayLimit;
  }

  const { loading, refreshing, error, ended, initialized, pagesOffset, dirty } =
    paging ?? {};
  const isLoadingPagination = isLoadMorePagination && loading;
  const isLoadingLoadMoreScroll = isLoadMoreScroll && !ended;
  const isLoadingLoadMoreButton = isLoadMoreButton && loading;
  const showLoadSmooth =
    canLoadSmooth &&
    (isLoadingLoadMoreScroll || isLoadingPagination || isLoadingLoadMoreButton);

  let data = canLoadMore
    ? paging.ids.slice(0, currentPage * perPage)
    : paging.ids.slice(0, displayLimit || paging.ids.length);

  if (isLoadMorePagination) {
    data = paging.pages[currentPage]?.ids || [];
  }

  const canCollapsed =
    !canLoadMore && collapsible && paging.ids.length > limitCollapse;

  const isCollapsed = canCollapsed && collapsed;

  if (canCollapsed) {
    data = isCollapsed ? paging.ids.slice(0, limitCollapse) : paging.ids;
  }

  React.useEffect(() => {
    if (handleUpdateLastRead) {
      handleUpdateLastRead();
    }

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [currentPage]);

  React.useEffect(() => {
    if (!isLoadMorePagination) return;

    const current_page = parseInt(
      pageParams?.page || pagesOffset?.current_page,
      10
    );
    setCurrentPage(current_page);
    loadMore();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [isLoadMorePagination, pageParams?.page, pagesOffset?.current_page]);

  React.useEffect(() => {
    if (!refreshing || dirty) return;

    loadMore();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [initialized, refreshing]);

  if (!ItemView) return null;

  if (!gridItemProps.xs) {
    gridItemProps.xs = 12;
  }

  if (error) {
    if (errorPage === 'hide') return null;

    const message =
      get(error, 'response.data.error') || get(error, 'response.data.message');

    const errorName =
      get(error, 'response.status') === 403
        ? 'core.block.error403'
        : 'core.block.error404';
    const ErrorBlock = jsxBackend.get(errorName);

    return <ErrorBlock title={message} />;
  }

  const blockHeaderBackend = (
    <Box sx={{ display: 'block' }}>
      <Box component={'h2'} mt={0} mb={0.5}>
        {pagesOffset?.title}
      </Box>
      <Box color="text.secondary">{pagesOffset?.description}</Box>
    </Box>
  );

  if ((!data.length && ended) || error) {
    if (emptyPage === 'hide') return null;

    const NoResultsBlock = jsxBackend.get(emptyPage);

    const { noBlock, contentStyle } = emptyPageProps || {};

    if (noBlock) {
      return React.createElement(NoResultsBlock, { ...emptyPageProps });
    }

    return (
      <Block>
        {titleBackend ? (
          <BlockHeader children={blockHeaderBackend} />
        ) : (
          <BlockHeader title={title} />
        )}
        <BlockContent {...contentStyle}>
          {isLoadMorePagination ? (
            <Pagination
              currentPage={currentPage}
              from={paging.pages[currentPage]?.from}
              to={paging.pages[currentPage]?.to}
              total={pagesOffset?.total_item || pagesOffset?.total}
              itemsPerPage={perPage}
              message={messagePagination}
              hasSort={hasSort}
              moduleName={moduleName}
              resourceName={resourceName}
              actionName={actionSortName}
            />
          ) : null}
          {React.createElement(NoResultsBlock, { ...emptyPageProps })}
        </BlockContent>
      </Block>
    );
  }

  if (!initialized) {
    if (!Skeleton) {
      return <div>{i18n.formatMessage({ id: 'loading_dots' })}</div>;
    }

    return (
      <Block>
        {titleBackend ? (
          <BlockHeader>
            <SkeletonDefault height={24} width={160} />
          </BlockHeader>
        ) : (
          <BlockHeader title={title} />
        )}
        <BlockContent>
          <Grid container {...gridContainerProps}>
            {range(0, limitSkeleton).map(index => (
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
    );
  }

  return (
    <Block>
      {titleBackend ? (
        <BlockHeader children={blockHeaderBackend} />
      ) : (
        <BlockHeader title={title} />
      )}
      <BlockContent>
        {isLoadMorePagination ? (
          <Pagination
            currentPage={currentPage}
            from={paging.pages[currentPage]?.from}
            to={paging.pages[currentPage]?.to}
            total={pagesOffset?.total_item || pagesOffset?.total}
            itemsPerPage={perPage}
            message={messagePagination}
            hasSort={hasSort}
            moduleName={moduleName}
            resourceName={resourceName}
            actionName={actionSortName}
          />
        ) : null}
        <Grid container {...gridContainerProps}>
          {isArray(data) &&
            data.map(id => (
              <ItemView
                identity={id}
                itemProps={itemProps}
                key={id.toString()}
                wrapAs={Grid}
                wrapProps={gridItemProps}
                pagingId={pagingId}
              />
            ))}
          {showLoadSmooth && Skeleton
            ? range(
                0,
                isLoadMorePagination && data.length ? 1 : limitSkeleton
              ).map(index => (
                <Skeleton
                  wrapAs={Grid}
                  wrapProps={gridItemProps}
                  itemProps={itemProps}
                  key={index.toString()}
                />
              ))
            : null}
        </Grid>
        {isLoadMorePagination ? (
          <Pagination
            currentPage={currentPage}
            from={paging.pages[currentPage]?.from}
            to={paging.pages[currentPage]?.to}
            total={pagesOffset?.total_item || pagesOffset?.total}
            itemsPerPage={perPage}
            message={messagePagination}
          />
        ) : null}
        {isLoadMoreButton &&
        ((!ended && !loading) || currentPage < paging?.page) ? (
          <LoadMoreListingButton
            handleClick={triggerLoadmore}
            message={buttonMessageLoadmore}
          />
        ) : null}
        {canCollapsed && ButtonToggle ? (
          <ButtonToggle setCollapsed={setCollapsed} collapsed={collapsed} />
        ) : null}
      </BlockContent>
    </Block>
  );
}

// types
import {
  getPagingSelector,
  GlobalState,
  initPagingState,
  ListViewBlockProps,
  PagingState,
  useGetItem,
  useGlobal,
  useScrollEnd,
  useSession
} from '@metafox/framework';
// layout
import {
  Block,
  BlockContent,
  BlockHeader,
  usePageParams
} from '@metafox/layout';
// components
import { GridItem } from '@metafox/ui';
import { filterShowWhen } from '@metafox/utils';
import { Box, CircularProgress, Grid } from '@mui/material';
// utils
import { fill, get, indexOf, isArray, min, range } from 'lodash';
import React, { MutableRefObject } from 'react';
import { useSelector } from 'react-redux';
import Column, { ColumnHandle, PinItem } from './Column';
import useStyles from './styles';
import CustomItem from './CustomItem';

export interface TItemBase {
  id: number;
  resource_name?: string;
  module_name?: string;
}

export interface Props extends ListViewBlockProps {
  numColumns?: number;
  margin?: number;
}

type RefArray = MutableRefObject<ColumnHandle>;

const wrapProps = {};

export default function PinListView({
  title,
  numColumns = 4,
  numberOfItemsPerPage,
  displayLimit,
  pagingId,
  itemProps = {},
  gridItemProps = {},
  gridContainerProps,
  itemView,
  canLoadMore,
  canLoadSmooth,
  loadMore,
  emptyPage = 'no_content',
  emptyPageProps,
  startItemView
}: Props) {
  const classes = useStyles();
  const { jsxBackend } = useGlobal();
  const { loggedIn, user: authUser } = useSession();
  const pageParams = usePageParams();
  let identity = pageParams?.identity;

  if (
    'user' === pageParams?.resource_name &&
    pageParams?.profile_page &&
    pageParams?.album_id
  ) {
    identity = `photo.entities.photo_album.${pageParams.album_id}`;
  }

  const item = useGetItem(identity);

  const ItemView = jsxBackend.get(itemView);
  const Skeleton = jsxBackend.get(`${itemView}.skeleton`);

  // check show startItemView
  let startItemViews = [];

  if (startItemView) {
    const isAuthUser =
      authUser?.id && pageParams?.id && authUser.id === parseInt(pageParams.id);

    const condition = { pageParams, authUser, loggedIn, isAuthUser, item };

    startItemViews = filterShowWhen(startItemView, condition).map(
      ({ as: c, ...props }, index) => ({
        component: c,
        props: {
          key: index.toString(),
          ...props
        }
      })
    );
  }

  const [currentPage, setCurrentPage] = React.useState<number>(1);
  const perPage = numberOfItemsPerPage || 20;
  const limit = displayLimit || 4;
  const paging =
    useSelector<GlobalState, PagingState>((state: GlobalState) =>
      getPagingSelector(state, pagingId)
    ) || initPagingState();

  useScrollEnd(
    canLoadMore ? () => setCurrentPage(prev => prev + 1) : undefined
  );

  const columns = React.useRef<number[]>(range(0, numColumns));
  const heightMap = React.useRef<number[]>(fill(range(0, numColumns), 0));
  const itemMap = React.useRef<PinItem[][]>(fill(range(0, numColumns), []));
  const listKey = React.useRef({});

  const [columnRefs] = React.useState<RefArray[]>(
    range(0, numColumns).map(() => React.createRef())
  );

  const handleAddItem = React.useCallback(
    (identity: string, width: number, height: number) => {
      const index = indexOf(heightMap.current, min(heightMap.current));
      heightMap.current[index] = heightMap.current[index] + height / width;
      itemMap.current[index].push({ identity, width, height });
      columnRefs[index].current?.addItem({ identity, width, height });
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  );

  if (!paging?.ids) paging.ids = [];

  const data = canLoadMore
    ? paging.ids.slice(0, currentPage * perPage)
    : paging.ids.slice(0, limit || 4);

  const dataRender = [...startItemViews, ...data];

  const { loading, refreshing, error, ended, initialized } = paging ?? {};

  React.useEffect(() => {
    if (refreshing) {
      listKey.current = {};
      heightMap.current = fill(range(0, numColumns), 0);
    }

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [refreshing]);

  if (!itemProps) {
    itemProps = { xs: 12 };
  }

  const pushItem = (
    identity: string,
    width: number,
    height: number,
    key: number
  ) => {
    if (
      Object.keys(listKey.current).length === dataRender?.length ||
      Object.keys(listKey.current).length > dataRender?.length
    )
      return;

    listKey.current[key] = { identity, width, height };

    if (Object.keys(listKey.current).length < dataRender?.length) return;

    Object.keys(listKey.current)
      .filter(key => listKey.current[key].width)
      .forEach(key => {
        const { identity, width, height } = listKey.current[key];
        handleAddItem(identity, width, height);
      });
  };

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

  if (!initialized) {
    if (!Skeleton) {
      return (
        <Box
          sx={{
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center'
          }}
        >
          <CircularProgress size={30} />
        </Box>
      );
    }

    return (
      <Block>
        <BlockHeader title={title} />
        <BlockContent>
          <Grid container {...gridContainerProps}>
            {range(1, limit).map(index => (
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

  if (!data.length && ended) {
    if (emptyPage === 'hide') return null;

    const NoResultsBlock = jsxBackend.get(emptyPage);

    const { noBlock } = emptyPageProps || {};

    if (noBlock) {
      return <NoResultsBlock {...emptyPageProps} />;
    }

    return (
      <Block>
        <BlockHeader title={title} />
        <BlockContent>
          <NoResultsBlock {...emptyPageProps} />
        </BlockContent>
      </Block>
    );
  }

  const spacing = gridContainerProps?.spacing;

  return (
    <Block>
      <BlockHeader title={title} />
      <BlockContent>
        <Grid container spacing={spacing} className={classes.root}>
          {isArray(columns.current) &&
            columns.current.map(index => (
              <Column
                spacing={spacing}
                startItemViews={startItemViews}
                indexColumn={index}
                wrapAs={GridItem}
                wrapProps={wrapProps}
                ItemView={ItemView}
                itemProps={itemProps}
                ref={columnRefs[index]}
                key={index.toString()}
                className={classes.column}
              />
            ))}
        </Grid>
        <div className="srOnly">
          {isArray(dataRender) &&
            dataRender.map((id, index) =>
              (id?.component ? (
                <CustomItem order={index} item={id} pushItem={pushItem} />
              ) : (
                <ItemView
                  identity={id}
                  key={index}
                  order={index}
                  pushItem={pushItem}
                  wrapAs={GridItem}
                  wrapProps={wrapProps}
                  itemProps={itemProps}
                />
              ))
            )}
        </div>
      </BlockContent>
    </Block>
  );
}

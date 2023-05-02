import {
  BlockViewProps,
  useGlobal,
  useResourceAction
} from '@metafox/framework';
import React from 'react';
import {
  APP_SAVED,
  PAGINGID_SAVED_LIST_DATA,
  RESOURCE_SAVED_LIST
} from '@metafox/saved/constant';
import { compactData, compactUrl } from '@metafox/utils';

export type Props = BlockViewProps;

const Base = ({
  title,
  canLoadMore,
  gridVariant = 'listView',
  gridLayout,
  itemLayout,
  itemView,
  emptyPage,
  emptyPageProps,
  ...rest
}: Props) => {
  const { jsxBackend, usePageParams } = useGlobal();
  const dataSource = useResourceAction(
    APP_SAVED,
    RESOURCE_SAVED_LIST,
    'viewItem'
  );
  const pageParams = usePageParams();

  const ListView = jsxBackend.get('core.block.listview');

  const EmptyPage = jsxBackend.get('core.block.no_results');

  if (!dataSource) return React.createElement(EmptyPage);

  return (
    <ListView
      title={title}
      emptyPage={emptyPage}
      emptyPageProps={emptyPageProps}
      canLoadMore
      dataSource={{
        apiUrl: compactUrl(dataSource?.apiUrl, {
          id: pageParams.collection_id
        }),
        apiParams: compactData(dataSource.apiParams, pageParams)
      }}
      clearDataOnUnMount
      gridVariant={gridVariant}
      gridLayout={gridLayout}
      itemLayout={itemLayout}
      itemView={itemView}
      blockLayout={rest.blockLayout}
      pagingId={PAGINGID_SAVED_LIST_DATA}
    />
  );
};

export default Base;

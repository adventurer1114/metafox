import { APP_EVENT } from '@metafox/event';
import {
  BlockViewProps,
  useGlobal,
  useResourceAction
} from '@metafox/framework';
import { usePageParams } from '@metafox/layout';
import React from 'react';

export type Props = BlockViewProps;

const GroupEventOverview = ({
  gridVariant = 'listView',
  dataSource,
  ...rest
}: Props) => {
  const { ListView } = useGlobal();

  dataSource = useResourceAction(APP_EVENT, APP_EVENT, 'viewUpcoming');
  const pageParams = usePageParams();

  return (
    <ListView
      dataSource={{
        apiUrl: dataSource.apiUrl,
        apiParams: { ...dataSource.apiParams, user_id: pageParams.id }
      }}
      gridVariant={gridVariant}
      {...rest}
    />
  );
};

export default GroupEventOverview;

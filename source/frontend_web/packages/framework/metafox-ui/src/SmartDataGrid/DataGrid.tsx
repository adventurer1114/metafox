import LoadingComponent from '@metafox/admincp/blocks/AdminForm/LoadingComponent';
import { SmartDataGridProps, useGlobal } from '@metafox/framework';
import { Alert, Box } from '@mui/material';
import React from 'react';
import DataGridBase from './DataGridBase';
import OptionCell from './OptionCell';

/**
 * Keep data grid config cache time to life milliseconds.
 */
const GRID_CONFIG_TTL = 300000;

type Props = Omit<
  SmartDataGridProps,
  'fetchAction' | 'getRowId' | 'batchActionMenu' | 'itemActionMenu' | 'columns'
> & {
  appName: string;
  resourceName: string;
};

function RemoteDataGrid({
  config,
  appName,
  resourceName,
  gridName,
  ...rest
}: Props) {
  const { i18n, jsxBackend } = useGlobal();

  const columns: any[] = React.useMemo(() => {
    const columns = config.columns.map(column => {
      return {
        ...column,
        renderCell: jsxBackend.get(
          `dataGrid.cell.${column.renderCell ?? 'BasicCell'}`
        ),
        renderHeader: jsxBackend.get('dataGrid.header.Basic')
      };
    });
    // dataGrid.cell.HeaderBasicCell

    if (config.sortable) {
      columns.unshift({
        field: 'sortable',
        width: 32,
        minWidth: 32,
        renderHeader: undefined,
        renderCell: jsxBackend.get('dataGrid.cell.SortIconCell')
      });
    }

    if (config.checkboxSelection) {
      columns.unshift({
        field: 'checkbox',
        width: 56,
        minWidth: 56,
        renderCell: jsxBackend.get('dataGrid.cell.Checkbox'),
        renderHeader: jsxBackend.get('dataGrid.header.Checkbox')
      });
    }

    if (config.itemActionMenu?.items.length) {
      columns.push({
        field: 'options',
        width: 120,
        minWidth: 100,
        align: 'center',
        headerName: i18n.formatMessage({ id: 'options' }),
        renderCell: OptionCell,
        renderHeader: undefined
      });
    }

    return columns;

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [appName, resourceName, gridName]);

  if (!config) {
    return (
      <Box px={2} py={2} component="div">
        <Alert variant="standard" color="error">
          Oops!, could not find configuration.
        </Alert>
      </Box>
    );
  }

  const cloneConf = { ...config, appName, resourceName };

  return (
    <DataGridBase
      key={`${appName}.${resourceName}.${gridName}`}
      gridName={gridName}
      config={cloneConf}
      columns={columns}
      rows={[]}
      {...rest}
    />
  );
}

function LoadDataGrid(props) {
  const { useFetchDetail } = useGlobal();
  const { gridName: name } = props;

  // how to handle this use cached data

  const [config, loading, error] = useFetchDetail({
    dataSource: {
      apiUrl: `admincp/core/grid/${name}`
    },
    ttl: GRID_CONFIG_TTL, // cache in 10 seconds
    cachePrefix: 'datagrid',
    cacheKey: name,
    forceReload: false
  });

  if (loading || error)
    return <LoadingComponent loading={loading} error={error} />;

  return <RemoteDataGrid {...props} config={config} />;
}

function SmartDataGrid(props) {
  const { gridName, resourceName, appName } = props;

  return (
    <LoadDataGrid {...props} key={`${appName}.${resourceName}.${gridName}`} />
  );
}

export default React.memo(
  SmartDataGrid,
  (prev, next) =>
    prev.appName === next.appName &&
    prev.resourceName === next.resourceName &&
    prev.gridName === next.gridName
);

import {
  ListViewBlockProps,
  useGlobal,
  useResourceAction,
  GlobalState
} from '@metafox/framework';
import { Block, BlockContent, BlockHeader, BlockTitle } from '@metafox/layout';
import React from 'react';
import {
  RESOURCE_SUBSCRIPTION_COMPARISON,
  APP_SUBSCRIPTION
} from '@metafox/subscription';
import {
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableRow,
  styled,
  Box,
  Typography,
  Skeleton
} from '@mui/material';
import { LineIcon, TruncateText } from '@metafox/ui';
import { useSelector } from 'react-redux';
import { isEmpty } from 'lodash';
import { SubscriptionPackageItemShape } from '@metafox/subscription/types';

export type Props = ListViewBlockProps;

const name = 'ComparisonBlock';

const TableCustom = styled(TableBody, { name, slot: 'tableCustom' })(
  ({ theme }) => ({
    minWidth: 958,
    overflow: 'hidden',
    display: 'inline-block',
    '& .MuiTableCell-root': {
      fontSize: theme.mixins.pxToRem(13),
      color: theme.palette.text.secondary,
      minWidth: '195px',
      maxWidth: '195px',
      height: '56px',
      borderBottom: 'none',
      cursor: 'pointer',
      position: 'relative',
      '&:first-of-type': {
        paddingLeft: 0,
        position: 'absolute',
        marginLeft: '-375px',
        minWidth: '375px',
        maxWidth: '375px'
      },
      '&:hover:not(:first-of-type):after': {
        background: theme.palette.action.hover,
        content: '""',
        height: '10000px',
        left: '0',
        position: 'absolute',
        top: '-5000px',
        width: '100%',
        zIndex: '-1'
      }
    },
    '& .MuiTableRow-root': {
      verticalAlign: 'top'
    },
    '& .ico': {
      color: theme.palette.primary.main,
      fontSize: theme.mixins.pxToRem(15)
    }
  })
);

const TableContainerScroll = styled(TableContainer, {
  name,
  slot: 'tableContainerScroll'
})(({ theme }) => ({
  overflowX: 'auto',
  overflowY: 'visible',
  marginLeft: '375px',
  width: 'calc(100% - 375px)',
  cursor: 'pointer',
  '&::-webkit-scrollbar': {
    height: '6px'
  },
  '&::-webkit-scrollbar-thumb': {
    borderRadius: '10px',
    backgroundColor: 'rgba(0,0,0,.2)'
  }
}));

const RowTitle = styled(TableRow, { name, slot: 'RowTitle' })(({ theme }) => ({
  zIndex: '2',
  '& .MuiTableCell-root': {
    color: theme.palette.text.primary,
    fontSize: theme.mixins.pxToRem(15),
    fontWeight: theme.typography.fontWeightBold,
    borderBottom: 'none',
    '&:first-of-type': {
      fontSize: theme.mixins.pxToRem(18),
      fontWeight: 'bold'
    }
  }
}));
type ValueComparisonPackage = {
  title: string;
  type: string;
  value: string;
};
const renderValueComparison = (data: ValueComparisonPackage) => {
  if (isEmpty(data)) return null;

  const { type, value } = data;

  if (type === 'no') return null;

  if (type === 'yes') return <LineIcon icon="ico-check" />;

  return (
    <Typography variant="body2" color="text.hint">
      {value}
    </Typography>
  );
};

const TableCellCustom = styled(TableCell, {
  name,
  slot: 'TableCellCustom',
  shouldForwardProp: prop => prop !== 'active'
})<{ active?: boolean }>(({ theme, active }) => ({
  color: theme.palette.text.hint,
  ...(active && {
    background: theme.palette.action.hover
  })
}));

export default function ComparisonBlock({ title }: Props) {
  const { useFetchItems, usePageParams, i18n } = useGlobal();
  const pageParams = usePageParams();
  const dataSource = useResourceAction(
    APP_SUBSCRIPTION,
    RESOURCE_SUBSCRIPTION_COMPARISON,
    'viewAll'
  );
  const [items, loading] = useFetchItems({
    dataSource,
    pageParams,
    normalize: true,
    data: [],
    cache: true
  });
  const packagesData = useSelector(
    (state: GlobalState) =>
      state.subscription.entities.subscription_package || {}
  );
  const packages: SubscriptionPackageItemShape[] = !isEmpty(packagesData)
    ? Object.values(packagesData)
    : [];

  if (loading) {
    return <Skeleton width={'100%'} />;
  }

  if (isEmpty(packages) || isEmpty(items)) {
    return null;
  }

  return (
    <Block>
      <BlockHeader>
        <BlockTitle>{title}</BlockTitle>
      </BlockHeader>
      <BlockContent>
        <Box sx={{ position: 'relative' }}>
          <TableContainerScroll>
            <Table sx={{ width: '100%' }}>
              <TableCustom>
                <RowTitle>
                  <TableCellCustom>
                    {i18n.formatMessage({ id: 'features' })}
                  </TableCellCustom>
                  {packages.map(packageItem => (
                    <TableCellCustom
                      key={packageItem.id}
                      active={packageItem.is_purchased}
                    >
                      <TruncateText
                        lines={1}
                        variant="body1"
                        sx={{ maxWidth: '300px' }}
                        fontWeight={600}
                      >
                        {packageItem.title}
                      </TruncateText>
                    </TableCellCustom>
                  ))}
                </RowTitle>
                {items.map(row => (
                  <TableRow key={row.id}>
                    <TableCellCustom>{row.title}</TableCellCustom>
                    {packages.map(packageItem => (
                      <TableCellCustom
                        key={`${row.id}${packageItem.id}`}
                        active={packageItem.is_purchased}
                      >
                        {renderValueComparison(row?.packages[packageItem.id])}
                      </TableCellCustom>
                    ))}
                  </TableRow>
                ))}
              </TableCustom>
            </Table>
          </TableContainerScroll>
        </Box>
      </BlockContent>
    </Block>
  );
}
ComparisonBlock.displayName = 'ComparisonBlockBlock';

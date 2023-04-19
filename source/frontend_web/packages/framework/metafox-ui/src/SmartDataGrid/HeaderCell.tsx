import React from 'react';
import { Box, styled } from '@mui/material';
import useDataGridContext from './useDataGridContext';
import { useGlobal } from '@metafox/framework';
import LineIcon from '../LineIcon';
import qs from 'query-string';
import { Hint } from '@metafox/ui';

interface Props {
  colDef: any;
  children: any;
}

const Root = styled('div', {
  name: 'DataGrid',
  slot: 'Cell',
  shouldForwardProp(propName: string): boolean {
    return !/width|height|flex|align|minWidth|sortable/i.test(propName);
  }
})<{
  sortable?: boolean;
  width?: number;
  height?: number;
  minWidth?: number;
  flex?: number;
  align: 'left' | 'right' | 'center' | undefined;
}>(({ sortable, align, width, height, minWidth, flex }) => ({
  display: 'flex',
  alignItems: 'center',
  padding: '10px 4px',
  overflow: 'hidden',
  justifyContent: align ?? 'start',
  minWidth,
  flex,
  width,
  height,
  textAlign: align ?? 'left',
  fontWeight: 'bold',
  cursor: sortable ? 'pointer' : 'unset'
}));

const getSortIcon = (sortType: string) => {
  if (sortType === 'asc') return 'ico-arrow-up';

  if (sortType === 'desc') return 'ico-arrow-down';

  return undefined;
};

export default function HeaderCell({ colDef, children }: Props) {
  const { location } = useGlobal();
  const { handleColumnAction } = useDataGridContext();
  const {
    headerAlign,
    align,
    minWidth,
    width,
    flex,
    headerHeight,
    action,
    sortable,
    description,
    sortableField
  } = colDef;

  const { search } = location;

  const { order, order_by } = qs.parse(search);

  return (
    <Root
      align={headerAlign ?? align}
      minWidth={minWidth}
      width={width}
      flex={flex}
      sortable={sortable}
      height={headerHeight}
      onClick={() =>
        sortable &&
        handleColumnAction('column/sortable', {
          action,
          sortableField,
          type: order_by === 'desc' ? 'asc' : 'desc'
        })
      }
    >
      {children}
      {sortable && order_by && order === sortableField && (
        <LineIcon icon={getSortIcon(order_by as string)} />
      )}
      <Box sx={{ pl: 0.5 }}>
        <Hint>{description}</Hint>
      </Box>
    </Root>
  );
}

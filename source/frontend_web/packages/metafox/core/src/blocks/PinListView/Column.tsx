import { RefOf } from '@metafox/framework';
import { Grid } from '@mui/material';
import * as React from 'react';
import CustomItem from './CustomItem';

export interface PinItem {
  startItemView?: any;
  identity: string | Record<string, any>;
  width: number;
  height: number;
}

export interface ColumnProps {
  className: any;
  ItemView: React.FC<any>;
  itemProps: any;
  spacing?: any;
  wrapAs: React.FC<any>;
  wrapProps: Record<string, any>;
  ItemAddPhotoAlbum?: React.FC<any>;
  startItemViews?: any;
  indexColumn?: number;
}

export interface ColumnHandle {
  height: () => number;
  addItem: (item: PinItem) => void;
}

const Column = (props: ColumnProps, ref: RefOf<ColumnHandle>) => {
  const { className, ItemView, spacing, itemProps, wrapAs, wrapProps } = props;
  const [items, setItems] = React.useState<PinItem[]>([]);
  const innerRef = React.useRef<HTMLDivElement>();

  React.useImperativeHandle(
    ref,
    () => ({
      height: () => innerRef.current?.offsetHeight,
      addItem: (item: PinItem) => setItems(prev => prev.concat(item))
    }),
    []
  );

  return (
    <Grid item ref={innerRef} className={className}>
      <Grid container spacing={spacing}>
        {items.map(({ identity, width, height }, index) => {
          return identity?.component ? (
            <CustomItem item={identity} />
          ) : (
            <ItemView
              identity={identity}
              key={`${identity}.c0`}
              width={width}
              height={height}
              itemProps={itemProps}
              wrapAs={wrapAs}
              wrapProps={wrapProps}
            />
          );
        })}
      </Grid>
    </Grid>
  );
};

export default React.forwardRef<ColumnHandle, ColumnProps>(Column);

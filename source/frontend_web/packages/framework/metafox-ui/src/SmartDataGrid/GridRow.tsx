import React from 'react';
import { styled } from '@mui/material';

const Root = styled('div', {
  name: 'DataGrid',
  slot: 'Row',
  shouldForwardProp: (prop: string) =>
    !/padding|selected|disabled|minHeight/i.test(prop)
})<{
  disabled?: boolean;
  minHeight?: number;
  selected?: boolean;
  padding?: string;
}>(({ selected, disabled, minHeight, theme, padding }) => ({
  display: 'flex',
  flexDirection: 'row',
  flexWrap: 'nowrap',
  position: 'relative',
  overflow: 'visible',
  width: '100%',
  minHeight,
  minWidth: '100%',
  maxWidth: '100%',
  borderBottom: theme.mixins.border('secondary'),
  padding,
  ...(selected && {
    backgroundColor: theme.palette.action.selected
  }),
  ...(disabled && {
    backgroundColor: theme.palette.action.disabledBackground
  }),
  '&:hover': {
    backgroundColor: theme.palette.action.hover
  }
}));

interface Props {
  readonly style: React.CSSProperties;
  readonly children: any;
  readonly onClick?: () => void;
  readonly minHeight: number;
  readonly selected?: boolean;
  readonly disabled?: boolean;
  readonly height?: number;
  readonly index?: number;
  readonly width?: number;
  readonly padding?: string;
  readonly setItemSize: (index: number, height: number) => void;
}

function GridRow({
  style,
  selected,
  disabled,
  onClick,
  children,
  index,
  height,
  minHeight,
  setItemSize,
  padding,
  width
}: Props) {
  const ref = React.useRef<HTMLDivElement>();

  React.useEffect(() => {
    if (height || !setItemSize) {
      return;
    }

    setTimeout(() => {
      const clientHeight = ref.current?.getBoundingClientRect().height;
      setItemSize(index, clientHeight);
    }, 100);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [width]);

  return (
    <div style={style}>
      <Root
        minHeight={minHeight}
        ref={ref}
        disabled={disabled}
        padding={padding}
        selected={selected}
        onClick={onClick}
      >
        {children}
      </Root>
    </div>
  );
}

export default GridRow;

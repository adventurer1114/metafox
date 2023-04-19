import { RefOf } from '@metafox/framework';
import { TruncateTextProps } from '@metafox/ui';
import { Box, Typography } from '@mui/material';
import { styled } from '@mui/material/styles';
import React from 'react';

const TruncateTextRoot = styled(Box, {
  name: 'MuiTruncateText',
  slot: 'Root',
  shouldForwardProp(prop: string) {
    return prop !== 'lines' && prop !== 'fixHeight' && prop !== 'showFull';
  }
})<TruncateTextProps>(({ theme }) => ({
  display: 'block',
  maxWidth: '100%'
}));

const TruncateText = styled(Typography, {
  name: 'MuiTruncateText',
  slot: 'Content',
  shouldForwardProp(prop: string) {
    return prop !== 'lines' && prop !== 'fixHeight' && prop !== 'showFull';
  }
})<TruncateTextProps>(({ theme, lines, variant, fixHeight }) => ({
  display: 'block',
  ...(lines > 1 && {
    display: '-webkit-box',
    padding: '0',
    overflow: 'hidden',
    maxWidth: '100%',
    whiteSpace: 'normal',
    textOverflow: 'ellipsis',
    WebkitBoxOrient: 'vertical'
  }),
  // eslint-disable-next-line eqeqeq
  ...(lines == 1 && {
    overflow: 'hidden',
    textOverflow: 'ellipsis',
    whiteSpace: 'nowrap',
    maxWidth: '100%'
  }),
  ...(lines > 1 &&
    theme.typography[variant] &&
    !fixHeight && {
      WebkitLineClamp: lines,
      maxHeight: `calc(${theme.typography[variant].lineHeight} * ${theme.typography[variant].fontSize} * ${lines})`
    }),
  ...(lines > 1 &&
    theme.typography[variant] &&
    fixHeight && {
      WebkitLineClamp: lines,
      height: `calc(${theme.typography[variant].lineHeight} * ${theme.typography[variant].fontSize} * ${lines})`,
      maxHeight: `calc(${theme.typography[variant].lineHeight} * ${theme.typography[variant].fontSize} * ${lines})`
    })
}));

export default React.forwardRef<HTMLElement, TruncateTextProps>(
  (
    {
      lines = 2,
      fixHeight = false,
      variant = 'body1',
      className,
      sx,
      showFull,
      children,
      ...rest
    }: TruncateTextProps,
    ref: RefOf<HTMLElement>
  ) => {
    return (
      <TruncateTextRoot className={className} sx={sx}>
        <TruncateText
          variant={variant}
          lines={showFull ? 0 : lines}
          showFull={showFull}
          ref={ref}
          fixHeight={fixHeight}
          {...rest}
        >
          {children}
        </TruncateText>
      </TruncateTextRoot>
    );
  }
);

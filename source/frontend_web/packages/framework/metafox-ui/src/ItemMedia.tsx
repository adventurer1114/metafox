import { MediaWidthVariant, RefOf } from '@metafox/framework';
import { useBlock } from '@metafox/layout';
import { Box } from '@mui/material';
import { styled } from '@mui/material/styles';
import clsx from 'clsx';
import React from 'react';
import { Image } from '.';

export interface ItemMediaProps {
  link?: string;
  src?: any;
  backgroundImage?: boolean;
  children?: React.ReactNode;
  alt?: string;
  className?: string;
  fillHeight?: boolean;
  width?: MediaWidthVariant;
  asModal?: boolean;
}

export interface ItemMediaClassName {
  root: string;
}

const ItemMedia = styled(Box, {
  name: 'ItemView',
  slot: 'Media',
  shouldForwardProp: (prop: string) =>
    !/aspectRatio|fillHeight|width|height/i.test(prop)
})<ItemMediaProps>(({ width, fillHeight }) => ({
  overflow: 'hidden',
  ...(width && {
    maxWidth: width,
    width
  }),
  ...(fillHeight && {
    '& .MuiImage-root': {
      height: '100%'
    }
  })
}));

const ItemMediaRoot = React.forwardRef(
  (
    { children, src, className, asModal, ...props }: ItemMediaProps,
    ref: RefOf<HTMLDivElement>
  ) => {
    const { itemProps: { media } = {} } = useBlock();

    if (!children && src) {
      children = (
        <Image
          {...props}
          src={src}
          aspectRatio={media?.aspectRatio}
          asModal={asModal}
        />
      );
    }

    if (!children) return null;

    return (
      <ItemMedia
        data-testid="itemMedia"
        ref={ref}
        className={clsx('ItemView-media', className)}
        {...media}
      >
        {children}
      </ItemMedia>
    );
  }
);
export default ItemMediaRoot;

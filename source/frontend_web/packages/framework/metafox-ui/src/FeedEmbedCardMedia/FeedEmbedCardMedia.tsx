import { FeedEmbedCardMediaProps, Image } from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { styled, Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';
import clsx from 'clsx';
import React from 'react';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {}
    }),
  { name: 'MuiFeedEmbedCardMedia' }
);

const Root = styled('div', {
  name: 'MuiFeedEmbedCardMedia',
  slot: 'bgCover',
  shouldForwardProp: prop => prop !== 'widthImage' && prop !== 'heightImage'
})<{ widthImage?: string; heightImage?: string }>(
  ({ theme, widthImage, heightImage }) => ({
    width: widthImage,
    height: heightImage,
    '& img': {
      border: '1px solid transparent',
      borderRightColor: theme.palette.border?.secondary,
      borderTop: 'none',
      borderBottom: 'none',
      borderLeft: 'none'
    }
  })
);

export default function FeedEmbedCardMedia({
  image,
  widthImage = '200px',
  heightImage = 'auto',
  mediaRatio = '11',
  link,
  playerOverlay = false,
  playerOverlayProps = {},
  host
}: FeedEmbedCardMediaProps) {
  const classes = useStyles();

  return (
    <Root
      widthImage={widthImage}
      heightImage={heightImage}
      className={clsx(classes.root, 'media')}
    >
      <Image
        link={link}
        src={getImageSrc(image)}
        host={host}
        aspectRatio={mediaRatio}
        playerOverlay={playerOverlay}
        playerOverlayProps={playerOverlayProps}
      />
    </Root>
  );
}

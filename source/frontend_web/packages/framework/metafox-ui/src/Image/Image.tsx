/**
 * @type: ui
 * name: ui.image.default
 */
import { RouteLink } from '@metafox/framework';
import { ImageShape } from '@metafox/ui';
import { styled, Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';
import clsx from 'clsx';
import React from 'react';
import PlayerOverlay from './PlayerOverlay';
import fallbackSrc from './fallbackSrc.png';

const StyledBgImg = styled('span')({
  display: 'block',
  width: '100%',
  height: '100%',
  position: 'relative',
  backgroundPosition: 'center center',
  backgroundRepeat: 'no-repeat',
  backgroundOrigin: 'border-box',
  backgroundSize: 'cover'
});

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        display: 'block',
        position: 'relative',
        width: '100%',
        '& img': {
          maxWidth: '100%',
          border: '1px solid transparent',
          borderColor: theme.palette.border?.secondary,
          background: 'white'
        },
        '&:before': {
          content: '""',
          display: 'block',
          paddingBottom: '100%'
        }
      },
      ratiofixed: {
        width: '100%',
        height: '100%',
        '&:before': {
          display: 'none'
        }
      },
      ratioauto: {
        '&:before': {
          display: 'none'
        },
        '& img': {
          maxHeight: '750px',
          width: '100%'
        }
      },
      ratio169: {
        '&:before': {
          paddingBottom: '56.25%'
        }
      },
      ratio32: {
        '&:before': {
          paddingBottom: '66.66%'
        }
      },
      ratio23: {
        '&:before': {
          paddingBottom: '150%'
        }
      },
      ratio43: {
        '&:before': {
          paddingBottom: '75%'
        }
      },
      ratio34: {
        '&:before': {
          paddingBottom: '133.33%'
        }
      },
      ratio13: {
        '&:before': {
          paddingBottom: '31%'
        }
      },
      ratio11: {
        '&:before': {
          paddingBottom: '100%'
        }
      },
      square: {
        borderRadius: '0',
        '& img': {
          borderRadius: '0'
        }
      },
      circle: {
        borderRadius: '100%',
        '& img': {
          borderRadius: '100%'
        }
      },
      radius: {
        borderRadius: theme.shape.borderRadius,
        '& img': {
          borderRadius: theme.shape.borderRadius
        }
      },
      inner: {
        position: 'absolute',
        left: 0,
        right: 0,
        top: 0,
        bottom: 0,
        '& img': {
          width: '100%',
          height: '100%',
          maxWidth: '100%'
        }
      },
      bgImage: {
        display: 'block',
        width: '100%',
        height: '100%',
        position: 'relative',
        backgroundPosition: 'center center',
        backgroundRepeat: 'no-repeat',
        backgroundOrigin: 'border-box'
      },
      cover: {
        '& $bgImage': {
          backgroundSize: 'cover'
        },
        '& img': {
          objectFit: 'cover'
        }
      },
      square100px: {
        '& img': {
          with: '100px',
          height: '100px'
        }
      },
      contain: {
        '& $bgImage': {
          backgroundSize: 'contain'
        },
        '& img': {
          objectFit: 'contain'
        }
      }
    }),
  { name: 'MuiImage' }
);

export default function Image(props: ImageShape) {
  const {
    alt,
    aspectRatio = 'auto',
    src,
    imgClass,
    squareImg,
    shape = 'square',
    link,
    asModal,
    backgroundImage = false,
    playerOverlay = false,
    playerOverlayProps = {},
    imageFit = 'cover',
    className,
    host,
    'data-testid': testid
  } = props;

  const classes = useStyles();
  const ImageBlock = (
    <>
      {'auto' === aspectRatio ? (
        <img src={src || fallbackSrc} alt={alt} className={imgClass} />
      ) : (
        <div className={classes.inner}>
          {backgroundImage ? (
            <StyledBgImg style={{ backgroundImage: `url(${src})` }} />
          ) : (
            <img
              src={src || fallbackSrc}
              alt={alt}
              draggable={false}
              className={imgClass}
            />
          )}
        </div>
      )}
      {playerOverlay && <PlayerOverlay {...playerOverlayProps} />}
    </>
  );

  const linkParams = !host
    ? { to: link }
    : {
        to: { pathname: link },
        target: '_blank'
      };

  return link ? (
    <RouteLink
      {...linkParams}
      asModal={asModal}
      data-testid={testid}
      className={clsx(
        classes.root,
        classes[shape],
        classes[squareImg],
        classes[`ratio${aspectRatio}`],
        classes[imageFit],
        className
      )}
    >
      {ImageBlock}
    </RouteLink>
  ) : (
    <div
      data-testid={testid}
      className={clsx(
        classes.root,
        classes[shape],
        classes[squareImg],
        classes[`ratio${aspectRatio}`],
        classes[imageFit],
        className
      )}
    >
      {ImageBlock}
    </div>
  );
}

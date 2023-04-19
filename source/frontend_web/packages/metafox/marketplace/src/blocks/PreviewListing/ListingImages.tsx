import { useGlobal } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import { Button, styled } from '@mui/material';
import transform from 'lodash/transform';
import React, { useCallback, useMemo, useState } from 'react';
import ImageGallery from 'react-image-gallery';
import 'react-image-gallery/styles/css/image-gallery.css';

const name = 'ListingImages';

const ImageGalleryStyled = styled(ImageGallery, { name, slot: 'root' })(
  ({ theme }) => ({
    position: 'relative',
    background: theme.palette.background.paper,
    width: 400,
    '& .image-gallery .image-gallery-thumbnail': {
      width: 73,
      height: 73,
      border: 'none',
      borderRadius: 4,
      marginRight: theme.spacing(1),
      minWidth: 73
    },
    '& .image-gallery .image-gallery-thumbnail.active': {
      backgroundColor: theme.palette.primary.main,
      padding: 2
    },
    '& .image-gallery .image-gallery-thumbnail.active $ThumbBgOverplay': {
      opacity: 0
    },
    '& .image-gallery-thumbnails .image-gallery-thumbnails-container': {
      display: 'flex'
    },
    '& .image-gallery .image-gallery-thumbnail + .image-gallery-thumbnail': {
      marginLeft: 0
    },
    '& .image-gallery .image-gallery-thumbnails': {
      padding: theme.spacing(1, 0)
    },
    '& .image-gallery-slides': {
      borderRadius: 4
    },
    [theme.breakpoints.down('xs')]: {
      width: '100%',
      '& .image-gallery-thumbnails-wrapper': {
        display: 'none'
      },
      '& .image-gallery-slides': {
        borderRadius: 0
      },
      '& $ImageMain': {
        borderRadius: 0
      }
    }
  })
);
const ImageMain = styled('div', { name, slot: 'imageMain' })(({ theme }) => ({
  width: '100%',
  height: 400,
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  backgroundColor: '#000',
  borderRadius: 4,
  overflow: 'hidden',
  [theme.breakpoints.down('xs')]: {
    height: 'auto',
    paddingTop: '100%',
    position: 'relative'
  }
}));
const ImageBox = styled('div', { name, slot: 'imageBox' })(({ theme }) => ({
  [theme.breakpoints.down('xs')]: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center'
  }
}));
const ImageThumb = styled('div', { name, slot: 'imageThumb' })(({ theme }) => ({
  backgroundPosition: 'center',
  backgroundRepeat: 'no-repeat',
  backgroundSize: 'cover',
  cursor: 'pointer',
  position: 'relative',
  overflow: 'hidden'
}));
const ThumbBg = styled('span', { name, slot: 'thumbBg' })(({ theme }) => ({
  display: 'block',
  paddingTop: '100%',
  backgroundPosition: 'center',
  backgroundRepeat: 'no-repeat',
  backgroundSize: 'cover',
  cursor: 'pointer',
  borderRadius: 4
}));
const ThumbBgOverplay = styled('span', { name, slot: 'thumbBgOverplay' })(
  ({ theme }) => ({
    backgroundColor: theme.palette.background.default,
    position: 'absolute',
    top: 0,
    right: 0,
    left: 0,
    bottom: 0,
    opacity: 0.5
  })
);
const NavButton = styled(Button, {
  name,
  slot: 'navButton',
  shouldForwardProp: prop => prop !== 'navDirection'
})<{ navDirection: 'left' | 'right' }>(({ theme, navDirection }) => ({
  width: theme.spacing(4),
  height: theme.spacing(4),
  backgroundColor: 'rgba(0,0,0,.4)',
  borderRadius: theme.spacing(4),
  color: theme.palette.background.paper,
  fontSize: theme.mixins.pxToRem(13),
  textAlign: 'center',
  lineHeight: 32,
  position: 'absolute',
  transform: 'translate(0, -50%)',
  top: '50%',
  zIndex: 1,
  border: 'none',
  display: 'flex',
  cursor: 'pointer',
  minWidth: 32,
  '&:hover': {
    backgroundColor: 'rgba(0,0,0,.6)'
  },
  ...(navDirection === 'left' && {
    left: theme.spacing(1)
  }),
  ...(navDirection === 'right' && {
    right: theme.spacing(1)
  })
}));

function ListingImages({ images, alt }) {
  const { dialogBackend } = useGlobal();
  const [currentIndex, setCurrentIndex] = useState(0);

  const renderItem = useCallback(
    image => (
      <ImageMain>
        <ImageBox>
          <img
            style={{ maxWidth: '100%', maxHeight: '100%' }}
            alt={image.originalAlt}
            src={image.original}
          />
        </ImageBox>
      </ImageMain>
    ),
    []
  );

  const renderThumbInner = useCallback(image => {
    return (
      <ImageThumb>
        <ThumbBg
          style={{ backgroundImage: `url(${image.original})` }}
        ></ThumbBg>
        <ThumbBgOverplay />
      </ImageThumb>
    );
  }, []);

  const renderLeftNav = (onClick, disabled) => {
    return (
      <NavButton navDirection="left" disabled={disabled} onClick={onClick}>
        <LineIcon icon={'ico-angle-left'} />
      </NavButton>
    );
  };

  const renderRightNav = (onClick, disabled) => {
    return (
      <NavButton navDirection="right" disabled={disabled} onClick={onClick}>
        <LineIcon icon={'ico-angle-right'} />
      </NavButton>
    );
  };

  // useMemo for avoid rendering ImageGallery cause bug
  // (auto back first Image when change state open, currentIndex  )
  const renderImages = useMemo(
    () =>
      transform(
        images,
        (result, image) => {
          const newItem = {
            original: image[400],
            thumbnail: image[50],
            renderItem,
            renderThumbInner,
            originalAlt: alt
          };
          result.push(newItem);
        },
        []
      ),
    [alt, images, renderItem, renderThumbInner]
  );

  const onImageLoad = () => {
    return <div>Loading</div>;
  };

  const handleClick = () => {
    dialogBackend.present({
      component: 'ui.dialog.photoViewer',
      props: { src: renderImages[currentIndex].original }
    });
  };

  const handleSlide = currentIndex => {
    setCurrentIndex(currentIndex);
  };

  if (!images) return null;

  return (
    <ImageGalleryStyled
      items={renderImages}
      showFullscreenButton={false}
      showPlayButton={false}
      onSlide={handleSlide}
      onImageLoad={onImageLoad}
      renderLeftNav={renderLeftNav}
      renderRightNav={renderRightNav}
      onClick={handleClick}
    />
  );
}

export default ListingImages;

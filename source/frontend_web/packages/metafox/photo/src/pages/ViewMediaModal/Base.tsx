import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { Link, useGlobal } from '@metafox/framework';
import { MediaViewModalProps } from '@metafox/photo/types';
import { CategoryList, LineIcon } from '@metafox/ui';
import { Box, Divider, styled } from '@mui/material';
import * as React from 'react';

const name = 'ViewMediaModal';

const Root = styled(DialogContent, { name, slot: 'root' })(({ theme }) => ({
  height: '100%',
  padding: '0 !important',
  paddingTop: '0 !important',
  display: 'flex',
  overflowY: 'visible',
  [theme.breakpoints.down('sm')]: {
    flexFlow: 'column',
    flexDirection: 'column',
    '& > div': {
      overflow: 'inherit'
    }
  }
}));

const DialogStatistic = styled('div', {
  name,
  slot: 'dialogStatistic',
  shouldForwardProp: prop => prop !== 'isExpand'
})<{
  isExpand: boolean;
}>(({ theme, isExpand }) => ({
  display: isExpand ? 'none' : 'block',
  height: '100%',
  width: '480px',
  flexGrow: 1,
  [theme.breakpoints.down('md')]: {
    width: '35%'
  },
  [theme.breakpoints.down('sm')]: {
    width: '45%'
  },
  [theme.breakpoints.down('xs')]: {
    width: '100%',
    height: '420px'
  }
}));

const DialogVideo = styled('div', { name, slot: 'dialogVideo' })(
  ({ theme }) => ({
    position: 'relative',
    backgroundColor: '#000',
    width: '100%',
    overflow: 'hidden',
    '& iframe': {
      width: '100%',
      height: '100%'
    },
    [theme.breakpoints.down('md')]: {
      width: '65%'
    },
    [theme.breakpoints.down('sm')]: {
      width: '100%',
      height: 'auto',
      borderRadius: 0,
      overflow: 'initial'
    }
  })
);

const DialogImage = styled('div', { name, slot: 'dialogImage' })(
  ({ theme }) => ({
    height: '100%',
    display: 'flex',
    alignItems: 'center',
    backgroundColor: '#000',
    width: '100%',
    justifyContent: 'center',
    position: 'relative',
    [theme.breakpoints.down('md')]: {
      width: '65%'
    },
    [theme.breakpoints.down('sm')]: {
      width: '55%'
    },
    [theme.breakpoints.down('xs')]: {
      width: '100%',
      height: 'auto',
      minHeight: '250px',
      maxHeight: '350px'
    }
  })
);

const ControlPhotoIcon = styled('div', {
  name,
  slot: 'nextPhoto',
  shouldForwardProp: prop => prop !== 'displayPosition'
})<{
  displayPosition: 'left' | 'right';
}>(({ theme, displayPosition }) => ({
  left: displayPosition === 'left' ? theme.spacing(2) : 'unset',
  right: displayPosition === 'right' ? theme.spacing(2) : 'unset',
  display: 'flex',
  alignItems: 'center',
  backgroundColor: 'rgba(0,0,0,0.4)',
  justifyContent: 'center',
  position: 'absolute',
  zIndex: 1,
  padding: '10px',
  borderRadius: '50%',
  width: '30px',
  height: '30px',
  cursor: 'pointer',
  color: '#FFF',
  top: '50%',
  transform: 'translateY(-50%)',
  [theme.breakpoints.down('md')]: {
    width: '65%'
  },
  [theme.breakpoints.down('sm')]: {
    width: '55%'
  },
  [theme.breakpoints.down('xs')]: {
    width: '100%',
    height: 'auto',
    minHeight: '250px',
    maxHeight: '350px'
  }
}));

const StyledHeaderItemAlbum = styled('div', { name, slot: 'HeaderAlbum' })(
  ({ theme }) => ({
    display: 'flex',
    flexDirection: 'column',
    padding: theme.spacing(0, 2)
  })
);
const StyledAlbumNameWrapper = styled('div', {
  name,
  slot: 'AlbumNameWrapper'
})(({ theme }) => ({
  '& .ico.ico-photos-o': {
    fontSize: theme.mixins.pxToRem(18),
    marginRight: theme.spacing(1)
  },
  display: 'flex',
  alignItems: 'center'
}));
const AlbumName = styled('div', { name, slot: 'AlbumName' })(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(15)
}));

const StyledWrapperStatistic = styled(Box, { name, slot: 'WrapperStatistic' })(
  ({ theme }) => ({
    display: 'flex',
    flexDirection: 'column',
    height: '100%'
  })
);

export default function ViewMediaModal({
  item,
  identity,
  nextUrl,
  prevUrl,
  mediaType,
  loading
}: MediaViewModalProps) {
  const {
    jsxBackend,
    navigate,
    ItemDetailInteractionInModal,
    useDialog,
    dispatch,
    i18n,
    useIsMobile,
    useGetItem,
    useGetItems
  } = useGlobal();

  const isMobile = useIsMobile();
  const [isExpand, setExpand] = React.useState<boolean>(false);
  const itemAlbum = useGetItem(item?.album);
  const categories = useGetItems<{ id: number; name: string }>(
    item?.categories
  );

  const PhotoItemModalView = jsxBackend.get('photo.itemView.modalCard');
  const PhotoItemModalViewMobile = jsxBackend.get(
    'photo.block.photoViewMobile'
  );
  const VideoItemModalView = jsxBackend.get('video.itemView.modalCard');

  const { dialogProps } = useDialog();

  const onPrevClick = React.useCallback(() => {
    navigate(
      { pathname: prevUrl },
      { replace: true, state: { asModal: true } }
    );
  }, [navigate, prevUrl]);

  const onNextClick = React.useCallback(() => {
    navigate(
      { pathname: nextUrl },
      { replace: true, state: { asModal: true } }
    );
  }, [navigate, nextUrl]);

  const onAddPhotoTag = (data: unknown) => {
    dispatch({ type: 'photo/onAddPhotoTag', payload: { identity, data } });
  };

  const onRemovePhotoTag = (id: unknown) => {
    dispatch({ type: 'photo/onRemovePhotoTag', payload: { identity, id } });
  };

  const onMinimizePhoto = (minimize: boolean) => {
    setExpand(minimize);
  };

  const handleKeyDown = (e: any) => {
    if (e.keyCode === 39 && nextUrl) {
      onNextClick();
    } else if (e.keyCode === 37 && prevUrl) {
      onPrevClick();
    }
  };

  if (!item) return null;

  if (isMobile) {
    return (
      <Dialog
        {...dialogProps}
        fullScreen
        scroll="body"
        data-testid="popupViewPhoto"
      >
        <DialogTitle enableBack={isMobile} disableClose={isMobile}>
          {i18n.formatMessage({ id: 'photo' })}
        </DialogTitle>
        <Root>
          {React.createElement(PhotoItemModalViewMobile, {
            item,
            identity,
            onNextClick,
            onPrevClick,
            nextUrl,
            prevUrl
          })}
        </Root>
      </Dialog>
    );
  }

  return (
    <Dialog
      scroll={'body'}
      {...dialogProps}
      fullScreen
      data-testid="popupDetailPhoto"
      onBackdropClick={undefined}
      onKeyDown={handleKeyDown}
    >
      <Root dividers={false}>
        {mediaType === 'photo' ? (
          <DialogImage>
            <>
              {nextUrl ? (
                <ControlPhotoIcon onClick={onNextClick} displayPosition="right">
                  <LineIcon icon="ico-angle-right" />
                </ControlPhotoIcon>
              ) : null}
              {prevUrl ? (
                <ControlPhotoIcon onClick={onPrevClick} displayPosition="left">
                  <LineIcon icon="ico-angle-left" />
                </ControlPhotoIcon>
              ) : null}
              {React.createElement(PhotoItemModalView, {
                item,
                identity,
                taggedFriends: [],
                onAddPhotoTag,
                onRemovePhotoTag,
                onMinimizePhoto
              })}
            </>
          </DialogImage>
        ) : (
          <DialogVideo>
            <>
              {nextUrl ? (
                <ControlPhotoIcon onClick={onNextClick} displayPosition="right">
                  <LineIcon icon="ico-angle-right" />
                </ControlPhotoIcon>
              ) : null}
              {prevUrl ? (
                <ControlPhotoIcon onClick={onPrevClick} displayPosition="left">
                  <LineIcon icon="ico-angle-left" />
                </ControlPhotoIcon>
              ) : null}
              {React.createElement(VideoItemModalView, {
                item,
                onMinimizePhoto
              })}
            </>
          </DialogVideo>
        )}
        <DialogStatistic isExpand={isExpand}>
          <StyledWrapperStatistic>
            <StyledHeaderItemAlbum>
              {itemAlbum ? (
                <Box sx={{ pt: 2 }}>
                  <StyledAlbumNameWrapper>
                    <LineIcon icon=" ico-photos-o" />
                    <AlbumName>
                      {i18n.formatMessage(
                        { id: 'from_album_name' },
                        {
                          name: (
                            <Link to={itemAlbum?.link}>{itemAlbum?.name}</Link>
                          )
                        }
                      )}
                    </AlbumName>
                  </StyledAlbumNameWrapper>
                  <Box sx={{ pt: 2 }}>
                    <Divider />
                  </Box>
                </Box>
              ) : null}
              <CategoryList
                to={
                  mediaType === 'photo' ? '/photo/category' : '/video/category'
                }
                data={categories}
                sx={{
                  pt: 2,
                  mb: { sm: 1, xs: 0 },
                  textTransform: 'capitalize'
                }}
                displayLimit={2}
              />
            </StyledHeaderItemAlbum>

            <Box sx={{ flex: 1, minHeight: 0 }}>
              <ItemDetailInteractionInModal
                identity={identity}
                loading={loading}
              />
            </Box>
          </StyledWrapperStatistic>
        </DialogStatistic>
      </Root>
    </Dialog>
  );
}

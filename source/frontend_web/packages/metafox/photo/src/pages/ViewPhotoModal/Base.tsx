import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { Link, useGlobal } from '@metafox/framework';
import { MediaViewModalProps } from '@metafox/photo/types';
import { CategoryList, LineIcon } from '@metafox/ui';
import { Box, Divider, styled } from '@mui/material';
import * as React from 'react';
import ErrorBoundary from '@metafox/core/pages/ErrorPage/Page';

const name = 'PhotoViewDialog';

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
  height: '100vh',
  display: isExpand ? 'none' : 'block',
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
    height: '400px'
  }
}));

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

export default function PhotoViewDialog({
  item,
  identity,
  mediaType,
  loading,
  error,
  user
}: MediaViewModalProps) {
  const {
    jsxBackend,
    ItemDetailInteractionInModal,
    useDialog,
    dispatch,
    i18n,
    useIsMobile,
    useGetItems,
    useGetItem,
    useSession
  } = useGlobal();

  const isMobile = useIsMobile();
  const [isExpand, setExpand] = React.useState<boolean>(false);
  const categories = useGetItems<{ id: number; name: string }>(
    item?.categories
  );
  const itemAlbum = useGetItem(item?.album);
  const owner = useGetItem(item?.owner);
  const { user: authUser } = useSession();

  const PhotoItemModalView = jsxBackend.get('photo.itemView.modalCard');

  const PhotoItemModalViewMobile = jsxBackend.get(
    'photo.block.photoViewMobile'
  );
  const { dialogProps } = useDialog();

  const onAddPhotoTag = (data: unknown) => {
    dispatch({ type: 'photo/onAddPhotoTag', payload: { identity, data } });
  };

  const onRemovePhotoTag = (id: unknown) => {
    dispatch({ type: 'photo/onRemovePhotoTag', payload: { identity, id } });
  };

  const onMinimizePhoto = (minimize: boolean) => {
    setExpand(minimize);
  };

  const isAuthOwner = authUser?.id === user?.id;

  if (!item) return null;

  // Need improve: some case have data from listing but error when fetch detail. need waiting fetched to reder
  if (loading && user?.id !== owner?.id && !item?._loadedDetail && !isAuthOwner)
    return null;

  if (isMobile) {
    return (
      <Dialog
        {...dialogProps}
        fullScreen={!error}
        scroll="body"
        data-testid="popupViewPhoto"
      >
        <DialogTitle enableBack={isMobile} disableClose={isMobile}>
          {i18n.formatMessage({ id: 'photo' })}
        </DialogTitle>
        <ErrorBoundary error={error}>
          <Root>
            {React.createElement(PhotoItemModalViewMobile, {
              item,
              identity
            })}
          </Root>
        </ErrorBoundary>
      </Dialog>
    );
  }

  return (
    <Dialog
      scroll={'body'}
      {...dialogProps}
      fullScreen={!error}
      data-testid="popupDetailPhoto"
      onBackdropClick={undefined}
    >
      <ErrorBoundary error={error}>
        <Root dividers={false}>
          <DialogImage>
            <>
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
                              <Link to={itemAlbum?.link}>
                                {itemAlbum?.name}
                              </Link>
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
                  to="/photo/category"
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
      </ErrorBoundary>
    </Dialog>
  );
}

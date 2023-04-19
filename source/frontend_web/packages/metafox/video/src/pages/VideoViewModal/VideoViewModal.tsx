/**
 * @type: dialog
 * name: video.dialog.videoView
 */

import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { connectItem, Link, useGlobal } from '@metafox/framework';
import { CategoryList, LineIcon } from '@metafox/ui';
import { Box, Divider, styled } from '@mui/material';
import * as React from 'react';
import { VideoItemShapeDialogProps } from '../../types';
import ErrorBoundary from '@metafox/core/pages/ErrorPage/Page';

const name = 'videoView';

const DialogVideo = styled('div', { name: 'VideoView', slot: 'dialogVideo' })(
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

const DialogStatistic = styled('div', {
  name: 'VideoView',
  slot: 'dialogStatistic',
  shouldForwardProp: prop => prop !== 'isExpand'
})<{
  isExpand: boolean;
}>(({ theme, isExpand }) => ({
  height: '100%',
  width: isExpand ? 0 : '480px',
  flexGrow: 1,
  [theme.breakpoints.down('md')]: {
    width: '35%'
  },
  [theme.breakpoints.down('sm')]: {
    width: '100%'
  },
  [theme.breakpoints.down('xs')]: {
    width: '100%',
    height: '400px'
  }
}));

const Root = styled(DialogContent, {
  name: 'VideoView',
  slot: 'dialogStatistic'
})<{}>(({ theme }) => ({
  padding: '0 !important',
  height: '100%',
  display: 'flex',
  overflowX: 'hidden',
  [theme.breakpoints.down('sm')]: {
    flexFlow: 'column'
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

function VideoViewDialog({ item, identity, error }: VideoItemShapeDialogProps) {
  const {
    ItemDetailInteractionInModal,
    useDialog,
    useIsMobile,
    i18n,
    jsxBackend,
    useGetItem,
    useGetItems
  } = useGlobal();
  const { dialogProps } = useDialog();
  const isMobile = useIsMobile();
  const [isExpand, setExpand] = React.useState<boolean>(false);

  const itemAlbum = useGetItem(item?.album);

  const categories = useGetItems<{ id: number; name: string }>(
    item?.categories
  );

  const VideoItemModalView = jsxBackend.get('video.itemView.modalCard');

  if (!item) return null;

  const onMinimizePhoto = (minimize: boolean) => {
    setExpand(minimize);
  };

  return (
    <Dialog
      scroll={'body'}
      {...dialogProps}
      fullScreen={!error}
      data-testid="popupDetailVideo"
      onBackdropClick={undefined}
    >
      {isMobile || error ? (
        <DialogTitle enableBack={!error} disableClose={isMobile}>
          {i18n.formatMessage({ id: 'video' })}
        </DialogTitle>
      ) : null}
      <ErrorBoundary error={error}>
        <Root>
          <DialogVideo>
            <>
              {React.createElement(VideoItemModalView, {
                item,
                onMinimizePhoto
              })}
            </>
          </DialogVideo>
          <DialogStatistic isExpand={isExpand}>
            <StyledWrapperStatistic
              sx={{
                display: isExpand ? 'none' : 'flex',
                flexDirection: 'column'
              }}
            >
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
                  to={'/video/category'}
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
                <ItemDetailInteractionInModal identity={identity} />
              </Box>
            </StyledWrapperStatistic>
          </DialogStatistic>
        </Root>
      </ErrorBoundary>
    </Dialog>
  );
}

export default connectItem(VideoViewDialog);

/* eslint-disable max-len */
import ErrorPage from '@metafox/core/pages/ErrorPage/Page';
import {
  fetchDetail,
  Link,
  useGlobal,
  useResourceAction,
  useSession
} from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent, usePageParams } from '@metafox/layout';
import { APP_PHOTO, RESOURCE_ALBUM } from '@metafox/photo/constant';
import { AlbumDetailProps } from '@metafox/photo/types';
import {
  FeaturedFlag,
  ItemTitle,
  SponsorFlag,
  TruncateViewMore,
  ItemAction
} from '@metafox/ui';
import { Box, styled, Typography } from '@mui/material';
import * as React from 'react';
import useStyles from './styles';

export type Props = AlbumDetailProps;

const name = 'ProfileAlbumViewDetail';

const BoxStyled = styled(Box, { name })(({ theme }) => ({
  backgroundColor: theme.palette.background.paper,
  marginBottom: theme.spacing(2),
  borderRadius: theme.spacing(1)
}));

function ProfileAlbumViewDetail({
  item,
  user,
  identity,
  handleAction,
  state
}: AlbumDetailProps) {
  const classes = useStyles();
  const {
    jsxBackend,
    i18n,
    useFetchDetail,
    ItemDetailInteraction,
    dispatch,
    ItemActionMenu,
    useActionControl
  } = useGlobal();
  const PhotoAlbumView: any = jsxBackend.get('photo.block.pinView');
  const pageParams = usePageParams();
  const { album_id } = pageParams;

  const { user: userSession = {} } = useSession();

  // eslint-disable-next-line react-hooks/rules-of-hooks
  React.useEffect(() => {
    if (album_id) {
      dispatch(fetchDetail(`photo-album/${album_id}`, { id: album_id }));
    }
  }, [dispatch, pageParams, album_id]);

  const identity_album = `photo.entities.photo_album.${album_id}`;
  const [handleActionAlbum, stateAlbum] = useActionControl(identity_album, {});

  const [data, loading, error] = useFetchDetail({
    dataSource: {
      apiUrl: `photo-album/${album_id}`
    }
  });
  const resourceAction = useResourceAction(
    APP_PHOTO,
    RESOURCE_ALBUM,
    'getAlbumItems'
  );

  if (!album_id) return null;

  const { apiMethod } = resourceAction || {};

  if (!item) return null;

  const dataSource = {
    apiUrl: '/photo-album/items/:album_id',
    apiMethod,
    apiParams: 'sort=latest'
  };
  const contentType = 'photo_album';
  const pagingId = `photo-album/${album_id}`;

  const { is_featured, is_sponsor, name, text, extra } = Object.assign(
    {},
    data
  );

  return (
    <Block testid={`detailview ${data?.resource_name}`}>
      <BlockContent>
        <ErrorPage
          loading={loading}
          error={error}
          sx={{ backgroundColor: 'background.paper' }}
        >
          <div className={classes.root}>
            <BoxStyled>
              <div className={classes.albumContent}>
                <div className={classes.actionsDropdown}></div>
                <div className={classes.albumContainer}>
                  <Link
                    to={`/user/${item.id}/photo?stab=albums`}
                    color="primary"
                    children={
                      userSession?.id === item.id
                        ? i18n.formatMessage({ id: 'my_albums' })
                        : i18n.formatMessage(
                            { id: 'user_s_albums' },
                            { value: item.full_name }
                          )
                    }
                    className={classes.category}
                  />
                  <ItemTitle variant="h3" component={'div'} showFull>
                    <FeaturedFlag variant="itemView" value={is_featured} />
                    <SponsorFlag variant="itemView" value={is_sponsor} />
                    <Typography
                      component="h1"
                      variant="h3"
                      sx={{
                        display: { sm: 'inline', xs: 'block' },
                        mt: { sm: 0, xs: 1 },
                        verticalAlign: 'middle'
                      }}
                    >
                      {name}
                    </Typography>
                  </ItemTitle>
                  <ItemAction sx={{ position: 'absolute', top: 8, right: 8 }}>
                    <ItemActionMenu
                      identity={identity_album}
                      state={stateAlbum}
                      handleAction={handleActionAlbum}
                      icon={'ico-dottedmore-vertical-o'}
                    />
                  </ItemAction>
                  {text && (
                    <div className={classes.info}>
                      <TruncateViewMore
                        truncateProps={{
                          variant: 'body1',
                          lines: 5
                        }}
                      >
                        <HtmlViewer html={text} />
                      </TruncateViewMore>
                    </div>
                  )}
                </div>
                <ItemDetailInteraction
                  identity={identity_album}
                  handleAction={handleAction}
                  hideListComment
                />
              </div>
            </BoxStyled>
            <BoxStyled pt={2}>
              <PhotoAlbumView
                title=""
                numColumns={3}
                pagingId={pagingId}
                dataSource={dataSource}
                contentType={contentType}
                gridContainerProps={{ spacing: 1 }}
                emptyPage="photo.block.EmptyPhotoAlbum"
                emptyPageProps={{
                  isVisible: extra?.can_upload_media
                }}
              />
            </BoxStyled>
          </div>
        </ErrorPage>
      </BlockContent>
    </Block>
  );
}

export default ProfileAlbumViewDetail;

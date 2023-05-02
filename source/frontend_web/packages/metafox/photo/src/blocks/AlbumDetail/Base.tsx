/* eslint-disable max-len */
import ItemDetailInteraction from '@metafox/core/containers/ItemDetailInteraction';
import { Link, useGlobal, useResourceAction } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent } from '@metafox/layout';
import { APP_PHOTO, RESOURCE_ALBUM } from '@metafox/photo/constant';
import { AlbumDetailProps } from '@metafox/photo/types';
import { capitalizeWord } from '@metafox/photo/utils';
import {
  DotSeparator,
  FeaturedFlag,
  FormatDate,
  ItemTitle,
  LineIcon,
  PrivacyIcon,
  SponsorFlag,
  TruncateViewMore,
  UserAvatar
} from '@metafox/ui';
import { Box, Typography } from '@mui/material';
import React from 'react';
import useStyles from './styles';

export type Props = AlbumDetailProps;

function PhotoAlbumDetail({
  item,
  user,
  identity,
  handleAction,
  state
}: AlbumDetailProps) {
  const classes = useStyles();
  const { jsxBackend, i18n, ItemActionMenu, useGetItem, usePageParams } =
    useGlobal();
  const PhotoAlbumView: any = jsxBackend.get('photo.block.pinView');
  const { comment_id } = usePageParams();

  const resourceAction = useResourceAction(
    APP_PHOTO,
    RESOURCE_ALBUM,
    'getAlbumItems'
  );
  const ownerItem = useGetItem(item?.owner);
  const { apiUrl, apiMethod } = resourceAction || {};

  if (!item) return null;

  const dataSource = {
    apiUrl,
    apiMethod,
    apiParams: 'sort=latest'
  };
  const contentType = 'photo_album';
  const pagingId = `photo-album/${item.id}`;
  let toAllAlbums = '/photo/albums';
  let labelLinkAlbum = i18n.formatMessage({ id: 'all_albums' });

  const { is_featured, is_sponsor, name, text, extra } = item;

  if (ownerItem && ownerItem?.resource_name !== 'user') {
    toAllAlbums = `${ownerItem?.link}/photo?stab=albums`;
    labelLinkAlbum = i18n.formatMessage(
      { id: 'all_albums_from_name' },
      { name: capitalizeWord(ownerItem.resource_name) }
    );
  }

  return (
    <Block testid={`detailview ${item.resource_name}`}>
      <BlockContent>
        <div className={classes.root}>
          <div className={classes.albumContent}>
            <div className={classes.actionsDropdown}>
              <ItemActionMenu
                className={classes.dropdownButton}
                identity={identity}
                state={state}
                handleAction={handleAction}
              >
                <LineIcon
                  icon={'ico-dottedmore-vertical-o'}
                  className={classes.iconButton}
                />
              </ItemActionMenu>
            </div>
            <div className={classes.albumContainer}>
              <Link
                to={toAllAlbums}
                color="primary"
                children={labelLinkAlbum}
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
              <div className={classes.owner}>
                <div className={classes.ownerAvatar}>
                  <UserAvatar user={user} size={48} />
                </div>
                <div className={classes.ownerInfo}>
                  <Link
                    to={`/${user.user_name}`}
                    children={user.full_name}
                    hoverCard={`/user/${user.id}`}
                    className={classes.profileLink}
                  />
                  <DotSeparator sx={{ color: 'text.secondary', mt: 0.5 }}>
                    <FormatDate
                      data-testid="creationDate"
                      value={item.creation_date}
                      format="MMMM DD, yyyy"
                    />
                    <PrivacyIcon
                      value={item?.privacy}
                      item={item?.privacy_detail}
                    />
                  </DotSeparator>
                </div>
              </div>
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
              identity={identity}
              handleAction={handleAction}
              borderBottom
              hideListComment={!comment_id}
            />
          </div>
          <Box pb={2}>
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
          </Box>
        </div>
      </BlockContent>
    </Block>
  );
}

export default PhotoAlbumDetail;

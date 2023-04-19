/* eslint-disable max-len */
import { Link, useGlobal, useResourceAction } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent } from '@metafox/layout';
import { APP_PHOTO, RESOURCE_ALBUM } from '@metafox/photo/constant';
import { AlbumDetailProps } from '@metafox/photo/types';
import { capitalizeWord } from '@metafox/photo/utils';
import {
  DotSeparator,
  Flag,
  FormatDate,
  LineIcon,
  PrivacyIcon,
  UserAvatar
} from '@metafox/ui';
import clsx from 'clsx';
import * as React from 'react';
import useStyles from './styles';

export type Props = AlbumDetailProps;

function PhotoAlbumDetail({
  item,
  user,
  identity,
  handleAction,
  state,
  blockProps
}: Props) {
  const classes = useStyles();
  const { jsxBackend, ItemActionMenu, useGetItem, i18n } = useGlobal();
  const PhotoAlbumView = jsxBackend.get('photo.block.pinView');
  const resourceAction = useResourceAction(
    APP_PHOTO,
    RESOURCE_ALBUM,
    'getAlbumItems'
  );
  const ownerItem = useGetItem(item?.owner);

  if (!item) return null;

  const { apiUrl, apiMethod } = resourceAction || {};
  const dataSource = {
    apiUrl,
    apiMethod,
    apiParams: 'sort=latest'
  };
  const contentType = 'photo_album';
  const pagingId = `photo-album/${item.id}`;

  const { is_featured, is_sponsor, name, text, photos, id, extra } = item;
  const to = `/photo/album/${id}`;
  let toAllAlbums = '/photo/albums';
  let labelLinkAlbum = i18n.formatMessage({ id: 'all_albums' });

  if (ownerItem && ownerItem?.resource_name !== 'user') {
    toAllAlbums = `${ownerItem?.link}/photo?stab=albums`;
    labelLinkAlbum = i18n.formatMessage(
      { id: 'all_albums_from_name' },
      { name: capitalizeWord(ownerItem.resource_name) }
    );
  }

  return (
    <Block blockProps={blockProps} testid={`detailview ${item.resource_name}`}>
      <BlockContent>
        <div className={classes.root}>
          <div
            className={clsx(classes.albumContent, photos && classes.hasPhotos)}
          >
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
              <div className={classes.albumTitle}>
                <div className={classes.features}>
                  {is_featured ? (
                    <Flag
                      data-testid="featured"
                      type={'is_featured'}
                      color={'white'}
                      variant={'detailView'}
                    />
                  ) : null}
                  {is_sponsor ? (
                    <Flag
                      data-testid="sponsored"
                      type={'is_sponsor'}
                      color={'white'}
                      variant={'detailView'}
                    />
                  ) : null}
                </div>
                <Link
                  to={to}
                  className={clsx(
                    classes.title,
                    (is_featured || is_sponsor) && classes.hasFeatures
                  )}
                  children={name}
                  variant={'h4'}
                />
              </div>
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
              <div className={classes.info}>
                <HtmlViewer html={text} />
              </div>
            </div>
          </div>
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
        </div>
      </BlockContent>
    </Block>
  );
}

export default PhotoAlbumDetail;

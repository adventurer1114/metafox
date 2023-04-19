/**
 * @type: ui
 * name: feedPhotoGrid.embedItem.insideFeedItem
 */
import { RouteLink, useGlobal } from '@metafox/framework';
import { PhotoItemShape } from '@metafox/photo';
import { Flag, Image } from '@metafox/ui';
import VideoPlayer from '@metafox/ui/VideoPlayer';
import { getImageSrc } from '@metafox/utils';
import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';
import clsx from 'clsx';
import * as React from 'react';
type Photo = Record<string, any>;

type Props = {
  total_photo?: number;
  total_item?: number;
  photos?: Array<Photo>;
  items?: Array<Photo>;
  photo_set?: string;
  photo_album?: string;
  'data-testid': string;
  item: PhotoItemShape;
  isUpdateAvatar?: boolean;
};

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        display: 'block',
        marginLeft: theme.spacing(-2),
        marginRight: theme.spacing(-2)
      },
      listing: {
        display: 'flex',
        flexFlow: 'wrap',
        margin: theme.spacing(-0.25)
      },
      itemInner: {
        position: 'relative'
      },
      item: {
        display: 'block',
        padding: theme.spacing(0.25)
      },
      item1: {},
      item2: {},
      item3: {},
      item4: {},
      listing1: {
        '& $item': {
          width: '100%'
        }
      },
      listing2: {
        '& $item': {
          width: '50%'
        }
      },
      listing3: {
        '& $item': {
          width: '50%'
        },
        '& $item1': {
          width: '100%'
        }
      },
      listing4: {
        '& $item': {
          width: '50%'
        }
      },
      flag: {
        position: 'absolute',
        right: theme.spacing(2.5),
        bottom: theme.spacing(2.5)
      },
      remainBackdrop: {
        position: 'absolute',
        left: 0,
        right: 0,
        top: 0,
        bottom: 0,
        backgroundColor: 'rgba(0,0,0,0.3)',
        '&:hover': {
          backgroundColor: 'rgba(0,0,0,0.1)'
        }
      },
      remainText: {
        color: 'white',
        position: 'absolute',
        left: '50%',
        top: '50%',
        fontSize: '2rem',
        transform: 'translate(-50%,-50%)'
      },
      isUpdateAvatar: {
        width: '100%',
        maxHeight: '500px'
      }
    }),
  { name: 'FeedPhotoGrid' }
);

export default function FeedPhotoGrid({
  total_photo,
  total_item,
  photo_set,
  photo_album,
  photos,
  items,
  isUpdateAvatar,
  'data-testid': testid
}: Props) {
  const { assetUrl } = useGlobal();
  const classes = useStyles();

  let listPhotos = [];

  if (photos && photos.length > 0) listPhotos = photos;

  if (items && items.length > 0) listPhotos = items;

  const total = total_photo ?? total_item;

  const gridType = Math.min(listPhotos.length, 4) % 5;
  const remain = total - gridType;

  let path = '';

  if (photo_set) path = `/media/${photo_set}`;

  if (photo_album) path = `/media/album/${photo_album}`;

  const smallLayout = listPhotos.length < 4 && listPhotos.length !== 2;
  const countPhotos = listPhotos.length;

  return (
    <div className={clsx(classes.root)} data-testid={testid}>
      <div className={clsx(classes.listing, classes[`listing${gridType}`])}>
        {listPhotos.slice(0, gridType).map((photo, index) => {
          if (photo.resource_name === 'video') {
            return photo.is_processing ? (
              <div
                key={`i${photo?.id}`}
                className={clsx(classes.item, classes[`item${index + 1}`])}
              >
                <Image
                  src={getImageSrc(
                    photo.image,
                    index === 0 && smallLayout ? '1024' : '500',
                    assetUrl('video.video_in_processing_image')
                  )}
                  aspectRatio={'169'}
                />
              </div>
            ) : (
              <div
                key={`i${photo?.id}`}
                className={clsx(classes.item, classes[`item${index + 1}`])}
              >
                <VideoPlayer
                  src={photo.video_url || photo.destination}
                  thumb_url={photo.image}
                  autoplayIntersection={total === 1}
                  modalUrl={
                    total !== 1
                      ? `${path}/${photo.resource_name}/${photo.id}`
                      : ''
                  }
                />
              </div>
            );
          }

          return (
            <RouteLink
              role="link"
              to={`${path}/${photo.resource_name}/${photo.id}`}
              asModal
              className={clsx(classes.item, classes[`item${index + 1}`])}
              key={index.toString()}
              data-testid="embeditem"
            >
              <div className={classes.itemInner}>
                <Image
                  src={getImageSrc(
                    photo.image || photo.avatar,
                    index === 0 && smallLayout ? '1024' : '500'
                  )}
                  aspectRatio={
                    isUpdateAvatar || countPhotos < 2 ? 'auto' : '169'
                  }
                  imageFit={isUpdateAvatar ? 'contain' : 'cover'}
                  imgClass={clsx({
                    [classes.isUpdateAvatar]: isUpdateAvatar
                  })}
                />
                {!!photo.is_featured && (
                  <div className={classes.flag}>
                    <Flag
                      type={'is_featured'}
                      color={'white'}
                      data-testid="featured"
                    />
                  </div>
                )}
                {0 < remain && gridType === index + 1 && (
                  <div className={classes.remainBackdrop}>
                    <div className={classes.remainText}>{`+ ${remain}`}</div>
                  </div>
                )}
              </div>
            </RouteLink>
          );
        })}
      </div>
    </div>
  );
}

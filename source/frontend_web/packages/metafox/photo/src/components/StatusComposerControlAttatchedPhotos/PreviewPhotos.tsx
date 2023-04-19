/**
 * @type: ui
 * name: StatusComposerControlAttachedPhotos
 */

import {
  BasicFileItem,
  StatusComposerControlProps,
  useGlobal
} from '@metafox/framework';
import { Image, LineIcon } from '@metafox/ui';
import { getImageSrc, isVideoType } from '@metafox/utils';
import { Button, IconButton, Box } from '@mui/material';
import clsx from 'clsx';
import { get } from 'lodash';
import React from 'react';
import useAddPhotoToStatusComposerHandler from '../../hooks/useAddPhotoToStatusComposerHandler';
import useStyles from './styles';
import produce from 'immer';

const filterAcceptType = ({ acl }) => {
  // note simple check basic permission, dont check seeting can_create_feed
  const acceptTypes = [];

  get(acl, 'photo.photo.create') && acceptTypes.push('image/*');

  get(acl, 'video.video.create') && acceptTypes.push('video/*');

  return acceptTypes.join(', ');
};

const parseMessageAddMedia = ({ acl }) => {
  const canPhoto = get(acl, 'photo.photo.create');

  const canVideo = get(acl, 'video.video.create');

  if (canPhoto && canVideo) {
    return 'add_photos_video';
  }

  if (canVideo) {
    return 'add_videos';
  }

  return 'add_photos';
};

export default function PreviewPhotos({
  composerRef,
  isEdit
}: StatusComposerControlProps) {
  const classes = useStyles();
  const { i18n, dialogBackend, getAcl } = useGlobal();
  const inputRef = React.useRef<HTMLInputElement>();
  const [handleChange, addPhotos] = useAddPhotoToStatusComposerHandler(
    composerRef,
    inputRef
  );
  const items: BasicFileItem[] = get(
    composerRef.current.state,
    'attachments.photo.value'
  );

  const extraFeed: any = get(composerRef.current.state, 'extra');

  const acl = getAcl();

  const editPhoto = React.useCallback(
    (item: BasicFileItem, data) => {
      const newValue = produce(items, draft => {
        const itemEdit = draft.find(x => {
          return (
            (item?.uid && x?.uid === item.uid) ||
            (item?.id && x?.id === item?.id)
          );
        });

        if (!itemEdit) return;

        Object.assign(itemEdit, data);
      });

      composerRef.current.setAttachments('photo', 'photo', {
        as: 'StatusComposerControlAttachedPhotos',
        value: newValue
      });
    },
    [items, composerRef]
  );

  const removeItem = React.useCallback(
    (items: BasicFileItem[], item: BasicFileItem) => {
      const newItems = items.filter(
        x => x.uid !== item.uid || x.id !== item.id
      );

      if (newItems.length > 0) {
        composerRef.current.setAttachments('photo', 'photo', {
          as: 'StatusComposerControlAttachedPhotos',
          value: newItems
        });
      } else {
        composerRef.current.removeAttachments();
      }
    },
    [composerRef]
  );

  const editAll = React.useCallback(() => {
    dialogBackend.present({
      component: 'photo.dialog.EditPreviewPhotosDialog',
      props: {
        composerRef
      }
    });
  }, [composerRef, dialogBackend]);

  const editItem = React.useCallback(() => {
    dialogBackend
      .present({
        component: 'photo.dialog.EditPreviewPhotoDialog',
        props: {
          item: items[0],
          hideTextField: true
        }
      })
      .then(value => {
        if (!value) return;

        editPhoto(items[0], value);
      });
  }, [dialogBackend, items, editPhoto]);

  const accept = filterAcceptType({ acl });
  const messageAddMedia = parseMessageAddMedia({ acl });

  const total = items?.length || 0;

  if (!total) return null;

  const gridType = Math.min(total, 4) % 5;
  const remain = total - gridType;

  return (
    <div className={clsx(classes.root)} data-testid="previewAttachPhoto">
      <div
        className={clsx(classes.listContainer, classes[`preset${gridType}`])}
      >
        {total
          ? items.slice(0, gridType).map((item, index) => (
              <div
                className={clsx(classes.itemRoot, classes[`item${index}`])}
                key={index.toString()}
              >
                <Box sx={{ position: 'relative', width: '100%' }}>
                  {isVideoType(item.file?.type) ? (
                    <video
                      src={item.source}
                      draggable={false}
                      controls={false}
                      autoPlay={false}
                      muted
                      className={classes.videoItem}
                    />
                  ) : (
                    <Image
                      draggable={false}
                      src={
                        item.base64 || item.source || getImageSrc(item?.image)
                      }
                      aspectRatio={'169'}
                      shape={'radius'}
                    />
                  )}
                  {0 < remain && gridType === index + 1 ? (
                    <div className={classes.remainBackdrop}>
                      <div className={classes.remainText}>{`+ ${remain}`}</div>
                    </div>
                  ) : !isEdit ||
                    (extraFeed?.can_edit_feed_item && items?.length > 1) ? (
                    <IconButton
                      size="smallest"
                      onClick={() => removeItem(items, item)}
                      variant="blacked"
                      className={classes.removeBtn}
                      title={i18n.formatMessage({ id: 'remove' })}
                    >
                      <LineIcon icon="ico-close" />
                    </IconButton>
                  ) : null}
                </Box>
              </div>
            ))
          : null}
      </div>
      <div className={classes.actionBar}>
        {!isEdit || extraFeed?.can_edit_feed_item ? (
          <div className={classes.buttonGroup}>
            {1 === total ? (
              <Button
                variant="contained"
                size="smaller"
                color="default"
                onClick={editItem}
                startIcon={<LineIcon icon="ico-pencil" />}
              >
                {i18n.formatMessage({ id: 'edit' })}
              </Button>
            ) : (
              <Button
                variant="contained"
                size="smaller"
                color="default"
                onClick={editAll}
                startIcon={<LineIcon icon="ico-pencil" />}
              >
                {i18n.formatMessage({ id: 'edit_all' })}
              </Button>
            )}
            <Button
              variant="contained"
              size="smaller"
              color="default"
              onClick={addPhotos}
              startIcon={<LineIcon icon="ico-plus" />}
            >
              {i18n.formatMessage({ id: messageAddMedia })}
            </Button>
          </div>
        ) : null}
        <input
          type="file"
          className="srOnly"
          ref={inputRef}
          onChange={handleChange}
          multiple
          accept={accept}
        />
      </div>
    </div>
  );
}

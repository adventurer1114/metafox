import {
  BasicFileItem,
  StatusComposerControlProps,
  useGlobal
} from '@metafox/framework';
import { Dialog, DialogActions, DialogTitle } from '@metafox/dialog';
import { ScrollContainer } from '@metafox/layout';
import { Button, Box } from '@mui/material';
import produce from 'immer';
import { get } from 'lodash';
import React from 'react';
import AddMorePhotosButton from './AddMorePhotosButton';
import ItemPhoto from './ItemPhoto';

export type Props = StatusComposerControlProps & {
  classes: Record<string, string>;
  dialogTitle: string;
};

export default function EditPreviewPhotosDialog({
  composerRef,
  classes,
  dialogTitle = 'edit_media'
}: Props) {
  const { useDialog, i18n, dialogBackend } = useGlobal();
  const { dialogProps, closeDialog } = useDialog();
  const [items, setItems] = React.useState<BasicFileItem[]>(
    get(composerRef.current.state, 'attachments.photo.value')
  );

  React.useEffect(() => {
    composerRef.current.setAttachments('photo', 'photo', {
      as: 'StatusComposerControlAttachedPhotos',
      value: items
    });
  }, [composerRef, items]);

  const editPhoto = React.useCallback(
    (item: BasicFileItem, data) => {
      setItems(
        produce(items, draft => {
          const itemEdit = draft.find(x => {
            return (
              (item?.uid && x?.uid === item.uid) ||
              (item?.id && x?.id === item?.id)
            );
          });

          if (!itemEdit) return;

          Object.assign(itemEdit, data);
        })
      );
    },
    [items]
  );

  const editItem = React.useCallback(
    (item: BasicFileItem) => {
      dialogBackend
        .present({
          component: 'photo.dialog.EditPreviewPhotoDialog',
          props: {
            item,
            tagging: false
          }
        })
        .then(value => {
          if (!value) return;

          editPhoto(item, value);
        });
    },
    [dialogBackend, editPhoto]
  );

  const removeItem = React.useCallback((item: BasicFileItem) => {
    setItems(prev => prev.filter(x => x.uid !== item.uid || x.id !== item.id));
  }, []);

  const tagItem = React.useCallback(
    (item: BasicFileItem) => {
      dialogBackend
        .present({
          component: 'photo.dialog.EditPreviewPhotoDialog',
          props: {
            item,
            tagging: true
          }
        })
        .then(value => {
          if (!value) return;

          editPhoto(item, value);
        });
    },
    [dialogBackend]
  );

  const total = items.length;

  const movePhoto = React.useCallback(
    (dragIndex: number, hoverIndex: number) => {
      const dragPhoto = items[dragIndex];

      setItems(
        produce(items, draft => {
          draft.splice(dragIndex, 1);
          draft.splice(hoverIndex, 0, dragPhoto);
        })
      );
    },
    [items]
  );

  const editTextPhoto = React.useCallback(
    (item: BasicFileItem, text) => {
      setItems(
        produce(items, draft => {
          const itemEdit = draft.find(x => {
            return (
              (item?.uid && x?.uid === item.uid) ||
              (item?.id && x?.id === item?.id)
            );
          });

          if (!itemEdit) return;

          itemEdit.text = text;
        })
      );
    },
    [items]
  );

  return (
    <Dialog {...dialogProps} fullWidth>
      <DialogTitle className={classes.dialogTitle}>
        {i18n.formatMessage({ id: dialogTitle })}
      </DialogTitle>
      <ScrollContainer className={classes.dialogContent}>
        <div className={classes.itemListContainer}>
          {total ? (
            items.map((item, index) => (
              <ItemPhoto
                key={item.uid}
                index={index}
                id={item.uid}
                movePhoto={movePhoto}
                item={item}
                editItem={editItem}
                removeItem={removeItem}
                tagItem={tagItem}
                classes={classes}
                editTextPhoto={editTextPhoto}
              />
            ))
          ) : (
            <Box
              p={3}
              sx={{
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                width: '100%'
              }}
            >
              <AddMorePhotosButton variant="listing" setItems={setItems} />
            </Box>
          )}
        </div>
      </ScrollContainer>
      <DialogActions className={classes.dialogActions}>
        <Button variant="contained" color="primary" onClick={closeDialog}>
          {i18n.formatMessage({ id: 'done' })}
        </Button>
        <AddMorePhotosButton setItems={setItems} />
      </DialogActions>
    </Dialog>
  );
}

import { BasicFileItem, useGlobal, useSession } from '@metafox/framework';
import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { LineIcon } from '@metafox/ui';
import { Button, TextField } from '@mui/material';
import clsx from 'clsx';
import React from 'react';
import CropperImageSelection from '../../components/ImageCropperSelection';
import useCropImage from '../../hooks/useCropImage';
import StyledMenuItem from './StyledMenuItem';
import { getImageSrc, getCroppedImg } from '@metafox/utils';
import useTagOnImage from './useTagOnImage';
import TaggedBox from '@metafox/photo/components/TaggedBox';
import Suggestion from '@metafox/photo/components/Suggestion/Suggestion';
import PhotoTagPreview from '@metafox/photo/components/PhotoTagPreview';
import PhotoTag from '@metafox/photo/containers/PhotoTag';
import useStyles from './Tag.styles';

type PhotoFileItem = BasicFileItem & {
  tagged_friends?: Array<Record<string, any>>;
  base64?: string;
};

export type EditPreviewPhotoDialogProps = {
  classes: Record<string, string>;
  dialogTitle: string;
  item: PhotoFileItem;
  hideTextField?: boolean;
};

export type TransformState = {
  transform: 'crop' | 'ratate' | 'none';
  value?: any;
};

export type EditingActionState = 'tag' | 'crop' | 'rotate' | 'flip';

export type ImageLoadedState = 'loading' | 'error' | 'success';

export type FlipState = { x: number; y: number };

const defaultCropStyle = { top: 0, left: 0, width: '100%', height: '100%' };

const parseCropToArea = crop => {
  return {
    x: crop?.left,
    y: crop?.top,
    width: crop?.width,
    height: crop?.height
  };
};

export default function EditPreviewPhotoDialog({
  classes,
  dialogTitle = 'Edit Photos',
  item: data,
  hideTextField = false,
  tagging: initialTagging
}: EditPreviewPhotoDialogProps) {
  const { useDialog, i18n } = useGlobal();
  const { dialogProps, closeDialog, setDialogValue } = useDialog();
  const [item] = React.useState<PhotoFileItem>(data);
  const [imageLoaded, setImageLoaded] =
    React.useState<ImageLoadedState>('loading');
  const [rotate, setRotate] = React.useState<number>(0);
  const [flip, setFlip] = React.useState<{ x: number; y: number }>({
    x: 0,
    y: 0
  });
  const edittingActionDefault = initialTagging ? 'tag' : undefined;
  const [editingAction, setEditingAction] = React.useState<EditingActionState>(
    edittingActionDefault
  );
  const imageContainerRef = React.useRef<HTMLDivElement>();
  const imageWrapperRef = React.useRef<HTMLDivElement>();
  const imageRef = React.useRef<HTMLImageElement>();
  const cropSelectionRef = React.useRef<HTMLDivElement>();
  const srcImage = getImageSrc(item?.image);
  const [imageSource, setImageSource] = React.useState<string>(
    item.base64 || item.source || srcImage
  );
  const anchorTagRef = React.useRef<any>();
  const taggedFriendRef = React.useRef<any>(item?.tagged_friends || []);
  const [taggedFriend, setTaggedFriend] = React.useState(
    item?.tagged_friends || []
  );
  const classesTag = useStyles();
  const { user } = useSession();

  const onAddPhotoTag = data => {
    setTaggedFriend(prev => [...prev, data]);
  };

  const handleRemoveTagPreview = (
    e: React.MouseEvent<{}>,
    idRemove: string
  ) => {
    e.stopPropagation();
    setTaggedFriend(prev => prev.filter(x => x.content?.id !== idRemove));
  };

  const handleRemoveTag = (e: React.MouseEvent<{}>, idRemove: string) => {
    e.stopPropagation();
    setTaggedFriend(prev => prev.filter(x => x.id !== idRemove));
  };

  const {
    onClickImageBox,
    setTagging,
    tagging,
    offsetTag,
    openTagBox,
    chooseFriendToTag
  } = useTagOnImage({
    imageRef,
    onAddPhotoTag,
    ready: imageLoaded === 'success'
  });
  const defaultPhotoCaption = React.useMemo(
    () => item?.text ?? '',
    [item?.text]
  );
  const [photoCaption, setPhotoCaption] = React.useState(defaultPhotoCaption);
  const [crop, setCrop] = React.useState<string>();

  const onImageLoadedSuccess = React.useCallback(() => {
    setImageLoaded('success');
  }, []);
  const onImageLoadedError = React.useCallback(() => {
    setImageLoaded('error');
  }, []);

  const onChange = React.useCallback((a, b) => {}, []);

  const {
    getCropStyle,
    onFulfillSelection,
    cropSelectionStyle,
    onComponentMouseTouchDown,
    onCropMouseTouchDown
  } = useCropImage({
    imageContainerRef,
    imageWrapperRef,
    imageRef,
    cropSelectionRef,
    onChange
  });

  const startCroppingImage = React.useCallback(() => {
    if ('crop' !== editingAction) setEditingAction('crop');
  }, [editingAction]);

  const flipVertical = React.useCallback(() => {
    setFlip(prev => ({ ...prev, x: prev.x ? 0 : 1 }));

    if ('flip' !== editingAction) setEditingAction('flip');
  }, [editingAction]);

  const flipHorizontal = React.useCallback(() => {
    setFlip(prev => ({ ...prev, y: prev.y ? 0 : 1 }));

    if ('flip' !== editingAction) setEditingAction('flip');
  }, [editingAction]);

  const rotateImage = React.useCallback(() => {
    if ('rotate' !== editingAction) setEditingAction('rotate');

    setRotate(prev => (prev + 90) % 360);
  }, [editingAction]);

  const startTaggingImage = React.useCallback(() => {
    if ('tag' !== editingAction) {
      setEditingAction('tag');
    }
  }, [editingAction]);

  React.useEffect(() => {
    setTagging('tag' === editingAction);
  }, [editingAction, setTagging]);

  const discardChanges = React.useCallback(() => {
    if (editingAction === 'tag') {
      setTaggedFriend(taggedFriendRef.current);
    }

    setImageSource(imageSource || item.source || srcImage);
    setEditingAction(undefined);
    setFlip({ x: 0, y: 0 });
    setRotate(0);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [
    item.source,
    srcImage,
    imageSource,
    taggedFriendRef.current,
    editingAction
  ]);

  const handleCroppedImage = async () => {
    const isCropAction = editingAction === 'crop';
    const dataCrop = isCropAction ? getCropStyle() : defaultCropStyle;
    const dataArea = parseCropToArea(dataCrop);
    const image_crop = (await getCroppedImg(
      crop || imageSource,
      dataArea,
      'base64',
      rotate,
      !!flip?.x,
      !!flip?.y,
      '%'
    )) as string;
    setFlip({ x: 0, y: 0 });
    setRotate(0);

    if (image_crop) {
      setCrop(image_crop);
    }

    return true;
  };

  const saveChanges = React.useCallback(async () => {
    if (editingAction === 'tag') {
      taggedFriendRef.current = taggedFriend;
      setEditingAction(null);
    } else {
      const processCrop = await handleCroppedImage();

      if (processCrop) {
        setEditingAction(null);
      }
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [photoCaption, cropSelectionStyle, imageSource, rotate, flip]);

  const handleSubmit = React.useCallback(() => {
    setDialogValue({
      text: photoCaption,
      base64: crop,
      tagged_friends: taggedFriend.map(x => ({
        ...x,
        user_id: x.content?.id || x.user?.id
      }))
    });

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [photoCaption, crop, taggedFriend]);

  const wrapperClass = clsx(classes.mediaWrapper);

  const photoClass = clsx(
    classes.largeImage,
    editingAction && classes.largeImageCropping
  );

  const isMenuDisabled = React.useCallback(
    (check: string) => {
      return (
        'success' !== imageLoaded || (editingAction && check !== editingAction)
      );
    },
    [imageLoaded, editingAction]
  );

  const cssRotate = rotate ? `rotate(${rotate}deg)` : '';
  const cssScale =
    flip.x || flip.y ? `scale(${flip.x ? -1 : 1}, ${flip.y ? -1 : 1})` : '';

  const photoStyle: React.CSSProperties = {
    transform: `${cssRotate} ${cssScale}`
  };

  const handleChangeCaption = event => {
    const data = event.target.value;
    setPhotoCaption(data);
  };

  return (
    <Dialog {...dialogProps} disableBackdropClick scroll="body" maxWidth="lg">
      <DialogTitle className={classes.dialogTitle}>{dialogTitle}</DialogTitle>
      <DialogContent className={classes.dialogContent}>
        <div className={classes.leftSide}>
          {!hideTextField ? (
            <div className={classes.composerContainer}>
              <TextField
                variant="outlined"
                disabled={'success' !== imageLoaded}
                placeholder={i18n.formatMessage({ id: 'caption' })}
                fullWidth
                multiline
                rows={4}
                InputLabelProps={{
                  shrink: true
                }}
                value={photoCaption}
                className={classes.composer}
                onChange={handleChangeCaption}
              />
            </div>
          ) : null}
          <div className={classes.leftSideActions}>
            {user.extra?.can_tag_friend && (
              <StyledMenuItem
                disabled={isMenuDisabled('tag')}
                startIcon={<LineIcon icon="ico-price-tag" />}
                children={i18n.formatMessage({ id: 'tags' })}
                onClick={startTaggingImage}
              />
            )}
            <StyledMenuItem
              disabled={isMenuDisabled('crop')}
              startIcon={<LineIcon icon="ico-arrow-collapse" />}
              children={i18n.formatMessage({ id: 'crop' })}
              onClick={startCroppingImage}
            />
            <StyledMenuItem
              disabled={isMenuDisabled('rotate')}
              startIcon={<LineIcon icon="ico-rotate-right-alt" />}
              children={i18n.formatMessage({ id: 'rotate' })}
              onClick={rotateImage}
            />
            <StyledMenuItem
              disabled={isMenuDisabled('flip')}
              startIcon={<LineIcon icon="ico-flip-v" />}
              children={i18n.formatMessage({ id: 'flip_vertical' })}
              onClick={flipVertical}
            />
            <StyledMenuItem
              disabled={isMenuDisabled('flip')}
              startIcon={<LineIcon icon="ico-flip-h" />}
              children={i18n.formatMessage({ id: 'flip_horizontal' })}
              onClick={flipHorizontal}
            />
          </div>
          <div className={classes.leftSideBottom}>
            {editingAction ? (
              <Button
                disabled={'success' !== imageLoaded}
                variant="contained"
                color="primary"
                onClick={saveChanges}
              >
                {i18n.formatMessage({ id: 'save_changes' })}
              </Button>
            ) : null}
            {editingAction ? (
              <Button
                disabled={'success' !== imageLoaded}
                variant="outlined"
                color="primary"
                onClick={discardChanges}
              >
                {i18n.formatMessage({ id: 'discard' })}
              </Button>
            ) : null}
            {!editingAction ? (
              <Button
                disabled={!imageLoaded}
                variant="contained"
                color="primary"
                onClick={handleSubmit}
              >
                {i18n.formatMessage({ id: 'save' })}
              </Button>
            ) : null}
            {!editingAction ? (
              <Button
                disabled={!imageLoaded}
                variant="outlined"
                color="primary"
                onClick={closeDialog}
              >
                {i18n.formatMessage({ id: 'cancel' })}
              </Button>
            ) : null}
          </div>
        </div>
        <div className={classes.mainContent}>
          <div className={classes.backdrop}>
            <div className={classes.blurContainer}>
              <img
                draggable={false}
                src={item.source || srcImage}
                className={classes.blurImg}
                alt={item.uid}
              />
            </div>
          </div>
          <div className={classes.mask} />
          <div
            className={classes.mediaContainer}
            onMouseDown={onComponentMouseTouchDown}
            onTouchStart={onComponentMouseTouchDown}
            ref={imageContainerRef}
          >
            <div
              className={wrapperClass}
              ref={imageWrapperRef}
              onClick={onClickImageBox}
            >
              {'error' !== imageLoaded ? (
                <>
                  <img
                    draggable={false}
                    src={crop || imageSource}
                    ref={imageRef}
                    className={photoClass}
                    onLoad={onImageLoadedSuccess}
                    style={photoStyle}
                    onError={onImageLoadedError}
                    alt={item.uid}
                  />
                  <TaggedBox
                    open={tagging && openTagBox}
                    px={offsetTag.px}
                    py={offsetTag.py}
                    classes={classesTag}
                    ref={anchorTagRef}
                  />
                  {tagging && openTagBox ? (
                    <Suggestion
                      onItemClick={chooseFriendToTag}
                      classes={classesTag}
                      anchorRef={anchorTagRef}
                      identity={
                        item?.id ? `photo.entities.photo.${item.id}` : undefined
                      }
                      open
                      excludeIds={taggedFriend.map(
                        x => x?.content?.id || x?.user?.id
                      )}
                      isFullFriend
                    />
                  ) : null}
                  {taggedFriend && taggedFriend?.length
                    ? taggedFriend.map(x =>
                        (x?.resource_name === 'photo_tag' ? (
                          <PhotoTag
                            tagging={tagging}
                            identity={`${x.module_name}.entities.${x.resource_name}.${x.id}`}
                            onRemove={handleRemoveTag}
                            classes={classesTag}
                            isTypePreview
                            forceShow
                          />
                        ) : (
                          <PhotoTagPreview
                            {...x}
                            tagging={tagging}
                            onRemove={handleRemoveTagPreview}
                            forceShow
                          />
                        ))
                      )
                    : null}
                </>
              ) : (
                <div className={classes.imageError}>
                  {i18n.formatMessage({ id: 'could_not_load_photo' })}
                </div>
              )}
              {'crop' === editingAction && 'success' === imageLoaded ? (
                <CropperImageSelection
                  onMounted={onFulfillSelection}
                  style={cropSelectionStyle}
                  ref={cropSelectionRef}
                  onMouseDown={onCropMouseTouchDown}
                  onTouchStart={onCropMouseTouchDown}
                />
              ) : null}
            </div>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}

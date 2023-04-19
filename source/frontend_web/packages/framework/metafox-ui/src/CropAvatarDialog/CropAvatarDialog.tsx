/**
 * @type: dialog
 * name: CropAvatarDialog
 */
import { useGlobal } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import {
  getImageSrc,
  isNoImage,
  shortenFileName,
  parseFileSize
} from '@metafox/utils';
import {
  Button,
  CircularProgress,
  Dialog,
  DialogTitle,
  IconButton
} from '@mui/material';
import Slider from '@mui/material/Slider';
import { useTheme } from '@mui/material/styles';
import useMediaQuery from '@mui/material/useMediaQuery';
import { isFunction } from 'lodash';
import React, { ChangeEvent, useEffect, useReducer } from 'react';
import { getCroppedImg } from './canvasUtils';
import Cropper from './Cropper';
import { reducer, State } from './reducer';
import useStyles from './styles';
import { Area } from './types';

export const initState: State = {
  crop: { x: 0, y: 0 },
  zoom: 1,
  flipHorizontal: false,
  flipVertical: false,
  rotation: 0,
  isDirty: false,
  isLoading: false,
  croppedAreaPixels: null,
  isSuccess: false
};

const readFile = (file: File) => {
  return new Promise(resolve => {
    const reader = new FileReader();
    reader.addEventListener('load', () => resolve(reader.result), false);
    reader.readAsDataURL(file);
  });
};

const CropAvatarDialog = ({ onUpdate, item, avatar }) => {
  const classes = useStyles();
  const theme = useTheme();
  const inputFileRef = React.useRef(null);
  const [newAvatar, setNewAvatar] = React.useState<File>(null);
  const { i18n, dispatch, dialogBackend, useDialog, useLimitFileSize } =
    useGlobal();
  const { dialogProps, setUserConfirm, closeDialog } = useDialog();
  const { photo: photoMaxSize } = useLimitFileSize();
  const fullScreen = useMediaQuery(theme.breakpoints.down('sm'));
  let originalAvatarPhoto = getImageSrc(item?.avatar);

  if (avatar) {
    originalAvatarPhoto = getImageSrc(avatar);
  }

  initState.imageSrc = originalAvatarPhoto;
  const [state, fire] = useReducer(reducer, {
    ...initState
  });
  const [forceInputFileShow, setForceInputFileShow] = React.useState(
    !originalAvatarPhoto
  );

  const {
    crop,
    zoom,
    rotation,
    flipHorizontal,
    flipVertical,
    imageSrc,
    croppedAreaPixels,
    isLoading,
    isDirty,
    isSuccess
  } = state;

  useEffect(() => {
    setUserConfirm(() => {
      if (isDirty) {
        return { message: 'Are you sure you want to discard the changes?' };
      }
    });
  }, [setUserConfirm, isDirty]);

  useEffect(() => {
    if (!isSuccess) return;

    closeDialog();
  }, [isSuccess, closeDialog]);

  const handleCropComplete = (_: Area, croppedAreaPixels: Area) => {
    fire({ type: 'setCroppedAreaPixels', payload: croppedAreaPixels });
  };

  const handleRotation = () => {
    const rotateNumber = 360 <= state.rotation + 90 ? 0 : state.rotation + 90;

    fire({ type: 'setRotation', payload: rotateNumber });
  };

  const handleCroppedImage = async () => {
    fire({ type: 'pendingSave' });

    try {
      const avatar_crop = (await getCroppedImg(
        imageSrc,
        croppedAreaPixels,
        'base64',
        rotation,
        flipVertical,
        flipHorizontal
      )) as string;

      if (avatar_crop) {
        dispatch({
          type: 'updateProfileUserAvatar',
          payload: {
            avatar: newAvatar,
            avatar_crop,
            userId: item?.id,
            identity: item?._identity
          },
          meta: {
            onSuccess: () => {
              fire({ type: 'fullfilSave' });
            },
            onFailure: () => {
              dialogBackend.alert({
                title: 'Error',
                message: 'Ops! Something wrong'
              });
              fire({ type: 'rejectSave' });
            }
          }
        });
      }

      isFunction(onUpdate) && onUpdate(avatar);
    } catch (e) {
      dialogBackend.alert({
        title: 'Error',
        message: 'Ops! Picture format is not correct.'
      });
      fire({ type: 'rejectSave' });
    }
  };

  const handleFileChange = async (e: ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && 0 < e.target.files.length) {
      const file = e.target.files[0];

      const fileItemSize = file.size;

      if (fileItemSize > photoMaxSize && photoMaxSize !== 0) {
        dialogBackend.alert({
          message: i18n.formatMessage(
            { id: 'warning_upload_limit_one_file' },
            {
              fileName: shortenFileName(file.name, 30),
              fileSize: parseFileSize(file.size),
              maxSize: parseFileSize(photoMaxSize)
            }
          )
        });

        return;
      }

      setNewAvatar(file);
      const imageDataUrl = await readFile(file);

      fire({ type: 'updateFile', payload: imageDataUrl as string });
    }
  };

  const handleChangeFileButtonClick = () => {
    inputFileRef.current.click();
  };

  const initCheckFocusBack = () => {
    window.addEventListener('focus', checkFocusBack);
  };

  const checkFocusBack = () => {
    setTimeout(() => {
      setForceInputFileShow(false);
    }, 500);
    window.removeEventListener('focus', checkFocusBack);
  };

  useEffect(() => {
    if (forceInputFileShow) {
      handleChangeFileButtonClick();
    }
  }, [forceInputFileShow]);

  if (!imageSrc) {
    if (!forceInputFileShow) return null;

    return (
      <input
        type="file"
        onChange={handleFileChange}
        accept="image/*"
        style={{ display: 'none' }}
        ref={inputFileRef}
        onClick={initCheckFocusBack}
      />
    );
  }

  return (
    <Dialog
      {...dialogProps}
      className={classes.cropDialog}
      aria-labelledby="dialog-title"
      maxWidth={'sm'}
      fullWidth
      scroll={'body'}
      fullScreen={fullScreen}
    >
      <DialogTitle id="dialog-title">
        {i18n.formatMessage({ id: 'update_your_profile_picture' })}
        <IconButton
          size="small"
          className={classes.btnClose}
          disabled={isLoading}
          onClick={closeDialog}
        >
          <LineIcon icon={'ico-close'} />
        </IconButton>
      </DialogTitle>
      <div className={classes.cropContainer}>
        <div className={classes.cropContainer}>
          <div className={classes.cropContent}>
            <div className={classes.cropBackdrop}>
              <div className={classes.cropBlurContent}>
                <img
                  crossOrigin="anonymous"
                  src={imageSrc}
                  alt=""
                  className={classes.blurImage}
                />
              </div>
            </div>
            <Cropper
              image={imageSrc}
              crop={crop}
              zoom={zoom}
              aspect={1}
              flipHorizontal={flipHorizontal}
              flipVertical={flipVertical}
              onCropChange={location =>
                fire({ type: 'setCrop', payload: location })
              }
              onCropComplete={handleCropComplete}
              onZoomChange={zoom =>
                fire({
                  type: 'setZoom',
                  payload: { zoom }
                })
              }
              rotation={rotation}
              showGrid={false}
              cropShape={'round'}
            />
          </div>
          {!isNoImage(imageSrc) && (
            <>
              <div className={classes.controls}>
                <div className={classes.sliderContainer}>
                  <Button
                    variant="text"
                    className={classes.btnControl}
                    onClick={() =>
                      fire({ type: 'setZoom', payload: { mode: 'minus' } })
                    }
                  >
                    <LineIcon icon={'ico-minus'} />
                  </Button>
                  <Slider
                    className={classes.sliderZoom}
                    value={zoom}
                    min={1}
                    max={3}
                    step={0.1}
                    aria-labelledby="Zoom"
                    onChange={(e, zoom) =>
                      fire({
                        type: 'setZoom',
                        payload: { zoom: zoom as number }
                      })
                    }
                  />
                  <Button
                    variant="text"
                    className={classes.btnControl}
                    onClick={() =>
                      fire({ type: 'setZoom', payload: { mode: 'plus' } })
                    }
                  >
                    <LineIcon icon={'ico-plus'} />
                  </Button>
                </div>
              </div>
              <div className={classes.btnContainer}>
                <Button
                  startIcon={<LineIcon icon={'ico-rotate-right-alt'} />}
                  onClick={handleRotation}
                >
                  {i18n.formatMessage({ id: 'rotate' })}
                </Button>
                <Button
                  startIcon={<LineIcon icon={'ico-flip-v'} />}
                  onClick={() => fire({ type: 'setFlipVertical' })}
                >
                  {i18n.formatMessage({ id: 'flip_vertical' })}
                </Button>
                <Button
                  startIcon={<LineIcon icon={'ico-flip-h'} />}
                  onClick={() => fire({ type: 'setFlipHorizontal' })}
                >
                  {i18n.formatMessage({ id: 'flip_horizontal' })}
                </Button>
              </div>
            </>
          )}
          <div className={classes.btnActions}>
            <div className={classes.btnChangePhoto}>
              <input
                type="file"
                onChange={handleFileChange}
                accept="image/*"
                className={classes.inputFile}
                ref={inputFileRef}
              />
              <Button
                variant="outlined"
                disabled={isLoading}
                className={classes.btnAction}
                size="small"
                onClick={handleChangeFileButtonClick}
              >
                {i18n.formatMessage({ id: 'change_photo' })}
              </Button>
            </div>
            <Button
              variant="contained"
              className={classes.btnAction}
              disabled={!isDirty}
              onClick={handleCroppedImage}
              size="small"
            >
              {isLoading ? (
                <CircularProgress color="inherit" size="1rem" />
              ) : (
                i18n.formatMessage({ id: 'save' })
              )}
            </Button>
          </div>
        </div>
      </div>
    </Dialog>
  );
};

export default CropAvatarDialog;

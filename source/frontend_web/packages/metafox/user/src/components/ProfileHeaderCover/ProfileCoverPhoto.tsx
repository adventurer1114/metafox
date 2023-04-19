/**
 * @type: service
 * name: ProfileHeaderCover
 */
import { Link, useGetItem, useGlobal } from '@metafox/framework';
import { Container, LineIcon } from '@metafox/ui';
import { ProfileHeaderCoverProps } from '@metafox/user/types';
import { filterShowWhen, shortenFileName, parseFileSize } from '@metafox/utils';
import { Button, CircularProgress } from '@mui/material';
import { styled } from '@mui/material/styles';
import clsx from 'clsx';
import React, { useCallback, useEffect, useReducer, useRef } from 'react';
import { reducer } from './reducer';
import useStyles from './styles';
import loadable from '@loadable/component';

// cut off 60kb from bundle.
const Draggable = loadable(
  () => import(/* webpackChunkName: "reactDraggable" */ 'react-draggable')
);

const EditCoverButton = styled(Button, {
  name: 'EditCoverButton'
})(({ theme }) => ({
  textTransform: 'capitalize',
  position: 'absolute',
  top: theme.spacing(2),
  right: theme.spacing(2),
  zIndex: 1,
  '&:hover': {
    backgroundColor: theme.palette.divider
  }
}));

export default function ProfileCoverPhoto({
  image: defaultImage,
  imageId,
  identity,
  left = 0,
  top,
  alt
}: ProfileHeaderCoverProps) {
  const classes = useStyles();
  const {
    i18n,
    getAcl,
    getSetting,
    ItemActionMenu,
    dispatch,
    useIsMobile,
    dialogBackend,
    useLimitFileSize
  } = useGlobal();
  const [isLoading, setLoading] = React.useState<boolean>(false);
  const inputRef = useRef<HTMLInputElement>();
  const isMobile = useIsMobile();
  const { photo: photoMaxSize } = useLimitFileSize();
  const item = useGetItem(identity);
  const {
    can_view_cover_detail = true,
    can_add_cover,
    can_edit_cover
  } = Object.assign({}, item?.extra);

  const [state, fire] = useReducer(reducer, {
    defaultImage,
    left,
    top,
    imgHeight: 0,
    wrapHeight: 320,
    image: defaultImage,
    menuOpen: false,
    enable: can_add_cover || can_edit_cover,
    dragging: false,
    position: { x: left, y: top },
    file: undefined,
    imageDetail: undefined,
    bounds: {
      top: 320,
      left: 0,
      right: 0,
      bottom: 0
    }
  });

  const acl = getAcl();
  const setting = getSetting();

  const to = `/photo/${imageId}`;
  const hasCoverPhoto = Boolean(imageId);

  const onLoad = useCallback((evt: any) => {
    fire({ type: 'setImgHeight', payload: evt.target.height });
  }, []);

  const onControlledDrag = useCallback((_, { x, y }) => {
    fire({ type: 'dragging', payload: { x, y } });
  }, []);

  const handleResetValue = (
    event: React.MouseEvent<HTMLInputElement, MouseEvent>
  ) => {
    event.currentTarget.value = null;
  };

  const handleCancelClick = useCallback(() => {
    fire({ type: 'cancel' });
    setLoading(false);
  }, []);

  useEffect(() => {
    fire({
      type: 'defaultImage',
      payload: { defaultImage, position: { x: left, y: top } }
    });
  }, [defaultImage, left, top]);

  const handleSaveClick = useCallback(() => {
    setLoading(true);
    fire({ type: 'saving' });
    dispatch({
      type: state.imageDetail ? 'updateCoverFromPhoto' : 'updateProfileCover',
      payload: {
        identity,
        position: state.position,
        photoIdentity: state.imageDetail?._identity,
        file: state.file
      },
      meta: {
        onSuccess: () => {
          fire({
            type: 'success',
            payload: { image: state.image, top: state.position.y.toString() }
          });
          setLoading(false);
        },
        onFailure: () => fire({ type: 'failed' })
      }
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [state]);

  const handleFileInputChanged = useCallback(() => {
    if (!inputRef.current.files.length) return;

    const file = inputRef.current.files[0];
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

    fire({ type: 'setFile', payload: inputRef.current.files.item(0) });
  }, []);

  const handleUploadPhotoClick = useCallback(() => {
    inputRef.current.click();
    fire({ type: 'closeMenu' });
  }, []);

  const handleRemovePhoto = useCallback(() => {
    dispatch({
      type: 'removeCoverPhoto',
      payload: {
        identity
      },
      meta: {
        onSuccess: () => {
          fire({ type: 'resetPosition' });
        },
        onFailure: () => fire({ type: 'failed' })
      }
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const handleChoosePhoto = useCallback(() => {
    dispatch({
      type: 'chooseCoverPhotoDialog',
      payload: {
        identity
      },
      meta: {
        onSuccess: item => {
          fire({ type: 'setFromPhoto', payload: item });
        },
        onFailure: () => fire({ type: 'failed' })
      }
    });

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);
  const items = filterShowWhen(
    [
      {
        icon: 'ico-arrows-move',
        label: i18n.formatMessage({ id: 'reposition' }),
        value: 'reposition',
        testid: 'reposition',
        showWhen: [
          'and',
          ['truthy', 'hasCoverPhoto'],
          ['truthy', 'can_edit_cover']
        ]
      },
      {
        icon: 'ico-photo-up-o',
        label: i18n.formatMessage({ id: 'upload_photo' }),
        value: 'upload_photo',
        testid: 'upload_photo',
        showWhen: ['truthy', 'can_add_cover']
      },
      {
        icon: 'ico-photos-o',
        label: i18n.formatMessage({ id: 'choose_from_photos' }),
        value: 'choose_photo',
        testid: 'choose_photo',
        showWhen: ['truthy', 'can_add_cover']
      },
      {
        as: 'divider',
        testid: 'divider',
        showWhen: ['truthy', 'hasCoverPhoto']
      },
      {
        icon: 'ico-trash-o',
        label: i18n.formatMessage({ id: 'remove' }),
        value: 'remove_photo',
        testid: 'remove_photo',
        showWhen: [
          'and',
          ['truthy', 'hasCoverPhoto'],
          ['truthy', 'can_edit_cover']
        ]
      }
    ],
    { hasCoverPhoto, acl, setting, can_edit_cover, can_add_cover }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  );

  const itemsMobile = filterShowWhen(
    [
      {
        icon: 'ico-photo-up-o',
        label: i18n.formatMessage({ id: 'upload_photo' }),
        value: 'upload_photo',
        testid: 'upload_photo',
        showWhen: ['truthy', 'can_add_cover']
      },
      {
        icon: 'ico-photos-o',
        label: i18n.formatMessage({ id: 'choose_from_photos' }),
        value: 'choose_photo',
        testid: 'choose_photo',
        showWhen: ['truthy', 'can_add_cover']
      },
      {
        as: 'divider',
        testid: 'divider',
        showWhen: ['truthy', 'hasCoverPhoto']
      },
      {
        icon: 'ico-trash-o',
        label: i18n.formatMessage({ id: 'remove' }),
        value: 'remove_photo',
        testid: 'remove_photo',
        showWhen: [
          'and',
          ['truthy', 'hasCoverPhoto'],
          ['truthy', 'can_edit_cover']
        ]
      }
    ],
    { hasCoverPhoto, acl, setting }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  );

  const handleAction = (type: string, data: any, meta: any) => {
    switch (type) {
      case 'reposition':
        fire({ type: 'reposition' });
        break;
      case 'upload_photo':
        handleUploadPhotoClick();
        break;
      case 'choose_photo':
        handleChoosePhoto();
        break;
      case 'remove_photo':
        handleRemovePhoto();
        break;
    }
  };

  return (
    <div className={classes.root}>
      <input
        type="file"
        aria-hidden
        className="srOnly"
        ref={inputRef}
        accept="image/*"
        onChange={handleFileInputChanged}
        onClick={handleResetValue}
      />
      <Container maxWidth={'md'} className={classes.coverContainer}>
        {(can_add_cover || can_edit_cover) && !state.dragging ? (
          <ItemActionMenu
            label={i18n.formatMessage({ id: 'edit_cover_photo' })}
            id="editCoverPhoto"
            items={isMobile ? itemsMobile : items}
            disablePortal
            handleAction={handleAction}
            control={
              <EditCoverButton
                aria-label="Edit cover photo"
                disableRipple
                color="default"
                size="small"
              >
                <LineIcon
                  icon={'ico-camera'}
                  className={classes.iconEditCover}
                />
                <span className={classes.textEditCover}>
                  {i18n.formatMessage({ id: 'edit_cover_photo' })}
                </span>
              </EditCoverButton>
            }
          />
        ) : null}
        {state.dragging ? (
          <>
            {!isLoading && !isMobile && (
              <div className={classes.repositionMessage} draggable="false">
                {i18n.formatMessage({ id: 'drag_to_reposition_your_cover' })}
              </div>
            )}
            <div className={classes.controlGroup}>
              <Button
                variant="contained"
                color="default"
                className={clsx(classes.btnControl, classes.btnCancel)}
                onClick={handleCancelClick}
                disabled={isLoading}
                size="small"
              >
                {i18n.formatMessage({ id: 'cancel' })}
              </Button>
              <Button
                variant="contained"
                color="primary"
                className={classes.btnControl}
                onClick={handleSaveClick}
                size="small"
              >
                {isLoading ? (
                  <CircularProgress color="inherit" size="1.5rem" />
                ) : (
                  i18n.formatMessage({ id: 'save' })
                )}
              </Button>
            </div>
          </>
        ) : null}
        <div
          className={classes.bgCover}
          style={{ backgroundImage: `url(${state.image})` }}
        ></div>
      </Container>
      <div className={classes.imagePositionBox}>
        <div
          className={classes.bgBlur}
          style={{
            backgroundImage: `url(${state.image})`
          }}
        />
        <Container maxWidth={'md'} disableGutters>
          <Draggable
            disabled={!state.dragging || isLoading || isMobile}
            position={!isMobile ? state.position : ''}
            axis="y"
            bounds={state.bounds}
            onDrag={onControlledDrag}
          >
            <div
              className={clsx(
                classes.imageDrag,
                state.dragging && !isMobile && classes.isReposition
              )}
            >
              {hasCoverPhoto && can_view_cover_detail && !state.dragging ? (
                <Link
                  to={to}
                  asModal
                  className={!state.dragging && isMobile && classes.linkModal}
                >
                  <img
                    src={state.image}
                    alt={alt}
                    className={clsx(
                      classes.imageCover,
                      state.dragging && !isMobile && classes.isReposition
                    )}
                    onLoad={onLoad}
                  />
                </Link>
              ) : (
                <img
                  src={state.image}
                  alt={alt}
                  className={clsx(
                    classes.imageCover,
                    state.dragging && !isMobile && classes.isReposition
                  )}
                  onLoad={onLoad}
                />
              )}
              {state.dragging ? (
                <div className={classes.overBg}></div>
              ) : (
                <div
                  className={classes.bgCover}
                  style={{ backgroundImage: `url(${state.image})` }}
                ></div>
              )}
            </div>
          </Draggable>
        </Container>
      </div>
    </div>
  );
}

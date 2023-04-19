/**
 * @type: ui
 * name: photo.itemView.modalCard
 */
import { useGlobal, useLoggedIn } from '@metafox/framework';
import { PhotoItemShape } from '@metafox/photo/types';
import { ClickOutsideListener, LineIcon } from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { Box, Button, IconButton, styled, Tooltip } from '@mui/material';
import clsx from 'clsx';
import * as React from 'react';
import PhotoTag from '../../containers/PhotoTag';
import useStyles from './PhotoItemModalView.styles';
import Suggestion from '../Suggestion/Suggestion';
import TaggedBox from '../TaggedBox';

type PhotoItemModalViewProps = {
  item: PhotoItemShape;
  user;
  isModal: boolean;
  identity: string;
  imageHeightAuto?: boolean;
  hideActionMenu?: boolean;
  enablePhotoTags?: boolean;
  taggedFriends?: any[];
  onAddPhotoTag: (data: unknown) => void;
  onRemovePhotoTag: (id: unknown) => void;
  onMinimizePhoto: (minimize: boolean) => void;
};

type State = {
  tagging?: boolean;
  px: number;
  py: number;
};

const name = 'PhotoItemModalView';

const ImageBox = styled('div', {
  name,
  slot: 'ImageBox',
  shouldForwardProp: props => props !== 'tagging'
})<{ tagging?: boolean }>(({ theme, tagging }) => ({
  position: 'relative',
  maxWidth: '100%',
  maxHeight: '100vh',
  ...(tagging && {
    cursor: 'pointer'
  })
}));

const ActionBar = styled('div', { name, slot: 'actionBar' })(({ theme }) => ({
  position: 'absolute',
  right: 0,
  top: 0,
  width: '100%',
  padding: theme.spacing(1),
  display: 'flex',
  justifyContent: 'space-between',
  zIndex: 1,
  alignItems: 'center'
}));

export default function PhotoItemModalView({
  item,
  identity,
  imageHeightAuto = false,
  enablePhotoTags = true,
  taggedFriends = [],
  isModal = true,
  onAddPhotoTag,
  onRemovePhotoTag,
  onMinimizePhoto
}: PhotoItemModalViewProps) {
  const classes = useStyles();
  const { i18n, assetUrl, useDialog, useIsMobile } = useGlobal();
  const { closeDialog } = useDialog();
  const loggedIn = useLoggedIn();
  const isMobile = useIsMobile();
  const anchorRef = React.useRef<any>();
  const imageRef = React.useRef<HTMLImageElement>();
  const [loadImage, setLoadImage] = React.useState<boolean>(false);
  const [currentSize, setCurrentSize] = React.useState({
    width: 0,
    height: 0
  });
  const [offset, setOffset] = React.useState<State>({ px: 0, py: 0 });
  const [tagging, setTagging] = React.useState<boolean>(false);
  const [openTagBox, setOpenTagBox] = React.useState<boolean>(false);
  const [minimize, setMinimize] = React.useState<boolean>(true);

  // should splitting face-api.js should not bundled like now
  // import * as faceapi from 'face-api.js';
  // React.useEffect(() => {
  //   async function detect() {
  //     await faceapi.nets.ssdMobilenetv1.loadFromUri('/face-api/weights');
  //     await faceapi.detectAllFaces(imageRef.current);
  //   }

  //   if (tagging) {
  //     detect();
  //   }
  // }, [tagging]);

  if (!item) return null;

  const src = getImageSrc(item.image, 'origin', assetUrl('photo.no_image'));
  const srcSmall = getImageSrc(item.image, '240', assetUrl('photo.no_image'));

  const removeTagFriend = (e: React.MouseEvent<{}>, id: string) => {
    e.stopPropagation();
    onRemovePhotoTag(id);
  };

  const onLoad = e => {
    setCurrentSize({ width: e.target.width, height: e.target.height });
    setLoadImage(true);
  };

  const toggleTagging = () => {
    setTagging(prev => !prev);

    if (!tagging) {
      setOpenTagBox(false);
    }
  };

  const onClickImageBox = (e: React.MouseEvent<HTMLDivElement>) => {
    const rect = e.currentTarget.getBoundingClientRect();
    const px: number =
      (Math.max(Math.min(e.clientX - rect.left, currentSize.width - 50), 50) /
        currentSize.width) *
      100;
    const py: number =
      (Math.max(Math.min(e.clientY - rect.top, currentSize.height - 50), 50) /
        currentSize.height) *
      100;

    setOffset({ px, py });

    if (tagging) {
      setOpenTagBox(true);
    }
  };

  const chooseFriendToTag = (content: unknown) => {
    const { px, py } = offset;
    const newTaggedFriend = { content, px, py };
    onAddPhotoTag(newTaggedFriend);
    setOpenTagBox(false);
  };

  const handleClickAway = () => {
    setOpenTagBox(false);
    setTagging(false);
  };

  const handleFullSize = () => {
    const minimizeItem = minimize;
    setMinimize(!minimizeItem);
    onMinimizePhoto && onMinimizePhoto(minimizeItem);
  };

  const handleClose = () => {
    closeDialog();
    onMinimizePhoto && onMinimizePhoto(false);
  };

  return (
    <ClickOutsideListener onClickAway={handleClickAway}>
      <div>
        <ImageBox tagging={tagging} onClick={onClickImageBox}>
          <img
            onLoad={onLoad}
            ref={imageRef}
            className={clsx(
              classes.image,
              loadImage && classes.visibleImage,
              imageHeightAuto && classes.imageHeightAuto
            )}
            alt={item.title}
            src={src}
          />
          {!loadImage ? (
            <div className={classes.boxFake}>
              <img
                className={classes.imageFake}
                alt={item.title}
                src={srcSmall}
              />
            </div>
          ) : null}
          <TaggedBox
            open={tagging && openTagBox}
            px={offset.px}
            py={offset.py}
            classes={classes}
            ref={anchorRef}
          />
          {tagging && openTagBox ? (
            <Suggestion
              onItemClick={chooseFriendToTag}
              classes={classes}
              anchorRef={anchorRef}
              identity={identity}
              open
            />
          ) : null}
          {item.tagged_friends?.length
            ? item.tagged_friends.map(id => (
                <PhotoTag
                  extra={item.extra}
                  tagging={tagging}
                  identity={id}
                  key={id.toString()}
                  onRemove={removeTagFriend}
                  classes={classes}
                />
              ))
            : null}

          <div className={classes.clear}></div>
        </ImageBox>
        <ActionBar>
          <Box>
            {isModal && !isMobile && (
              <Tooltip title={i18n.formatMessage({ id: 'close' })}>
                <IconButton className={classes.tagFriend} onClick={handleClose}>
                  <LineIcon icon="ico-close" color="white" />
                </IconButton>
              </Tooltip>
            )}
          </Box>
          {loggedIn && enablePhotoTags && !tagging ? (
            <Box>
              {item.extra?.can_tag_friend && (
                <Tooltip title={i18n.formatMessage({ id: 'start_tagging' })}>
                  <IconButton
                    className={classes.tagFriend}
                    onClick={toggleTagging}
                  >
                    <LineIcon icon="ico-price-tag" color="white" />
                  </IconButton>
                </Tooltip>
              )}
              {isModal && (
                <Tooltip
                  title={i18n.formatMessage({
                    id: minimize ? 'switch_to_full_screen' : 'exit_full_screen'
                  })}
                >
                  <IconButton
                    className={classes.tagFriend}
                    onClick={handleFullSize}
                  >
                    <LineIcon
                      icon={
                        minimize ? 'ico-arrow-expand' : 'ico-arrow-collapse'
                      }
                      color="white"
                    />
                  </IconButton>
                </Tooltip>
              )}
            </Box>
          ) : null}
          {enablePhotoTags && tagging ? (
            <Tooltip title={i18n.formatMessage({ id: 'done_tagging' })}>
              <Button
                variant="contained"
                color="primary"
                size="small"
                onClick={toggleTagging}
              >
                {i18n.formatMessage({ id: 'done' })}
              </Button>
            </Tooltip>
          ) : null}
        </ActionBar>
      </div>
    </ClickOutsideListener>
  );
}

import { Link, useGlobal } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent } from '@metafox/layout';
import { PhotoDetailProps } from '@metafox/photo/types';
import {
  CategoryList,
  FromNow,
  LineIcon,
  PrivacyIcon,
  UserAvatar
} from '@metafox/ui';
import { Box, Divider, styled } from '@mui/material';
import clsx from 'clsx';
import xorBy from 'lodash/xorBy';
import * as React from 'react';
import useStyles from './styles';

const name = 'PhotoDetailMobile';
const HeaderItemAlbum = styled('div', { name, slot: 'HeaderItemAlbum' })(
  ({ theme }) => ({
    display: 'flex',
    flexDirection: 'column',
    padding: theme.spacing(0, 0, 2, 2)
  })
);
const AlbumNameWrapper = styled('div', { name, slot: 'AlbumNameWrapper' })(
  ({ theme }) => ({
    '& .ico.ico-photos-o': {
      fontSize: theme.mixins.pxToRem(18),
      marginRight: theme.spacing(1)
    },
    display: 'flex',
    alignItems: 'center'
  })
);
const AlbumName = styled('div', { name, slot: 'AlbumName' })(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(15)
}));

export type Props = PhotoDetailProps;

function PhotoDetail({
  item,
  actions,
  handleAction,
  state,
  user,
  blockProps,
  onNextClick,
  onPrevClick,
  nextUrl,
  prevUrl
}: Props) {
  const classes = useStyles();
  const {
    jsxBackend,
    ItemDetailInteraction,
    ItemActionMenu,
    useGetItem,
    useGetItems,
    i18n
  } = useGlobal();
  const [taggedFriends, setTaggedFriends] = React.useState([]);
  const [isExpand, setExpand] = React.useState<boolean>(false);
  const itemAlbum = useGetItem(item?.album);
  const categories = useGetItems<{ id: number; name: string }>(
    item?.categories
  );

  if (!item) return null;

  const PhotoItemView = jsxBackend.get('photo.itemView.modalCard');
  const identity = `photo.entities.photo.${item.id}`;
  const suggestFriends = xorBy([], taggedFriends, 'id');

  const onAddPhotoTag = newTaggedFriend => {
    setTaggedFriends(prev => [...prev, newTaggedFriend]);
  };

  const onRemovePhotoTag = friendId => {
    setTaggedFriends(prev => {
      const excludedFriends = xorBy(prev, [{ id: friendId }], 'id');

      return excludedFriends;
    });
  };

  const onMinimizePhoto = minimize => {
    setExpand(minimize);
  };

  return (
    <Block testid={`detailview ${item.resource_name}`}>
      <BlockContent>
        <div className={classes.root}>
          <HeaderItemAlbum>
            {itemAlbum ? (
              <>
                <AlbumNameWrapper>
                  <LineIcon icon=" ico-photos-o" />
                  <AlbumName>
                    {i18n.formatMessage(
                      { id: 'from_album_name' },
                      {
                        name: (
                          <Link to={itemAlbum?.link}>{itemAlbum?.name}</Link>
                        )
                      }
                    )}
                  </AlbumName>
                </AlbumNameWrapper>
                <Box sx={{ pt: 2 }}>
                  <Divider />
                </Box>
              </>
            ) : null}
            <CategoryList
              to={
                item.resource_name === 'photo'
                  ? '/photo/category'
                  : '/video/category'
              }
              data={categories}
              sx={{ pt: 2, mb: { sm: 1, xs: 0 }, textTransform: 'capitalize' }}
              displayLimit={2}
            />
          </HeaderItemAlbum>

          <div className={classes.header}>
            <div className={classes.headerAvatarHolder}>
              <UserAvatar user={user} size={48} />
            </div>
            <div className={classes.headerInfo}>
              <div>
                <Link
                  to={`/${user.user_name}`}
                  children={user.full_name}
                  hoverCard={`/user/${user.id}`}
                  className={classes.profileLink}
                />
              </div>
              <div className={classes.privacyBlock}>
                <span className={classes.separateSpans}>
                  <PrivacyIcon
                    value={item.privacy}
                    item={item?.privacy_detail}
                  />
                  <FromNow value={item.creation_date} />
                </span>
              </div>
            </div>
            <ItemActionMenu
              identity={identity}
              state={state}
              handleAction={handleAction}
            />
          </div>
          <div
            className={clsx(classes.imageContainer, {
              [classes.fullScreenView]: isExpand
            })}
          >
            <div>
              {nextUrl ? (
                <div
                  className={classes.nextPhoto}
                  onClick={() => onNextClick()}
                >
                  <LineIcon icon="ico-angle-right" />
                </div>
              ) : null}
              {prevUrl ? (
                <div
                  className={classes.prevPhoto}
                  onClick={() => onPrevClick()}
                >
                  <LineIcon icon="ico-angle-left" />
                </div>
              ) : null}
            </div>
            <PhotoItemView
              item={item}
              identity={identity}
              suggestions={suggestFriends}
              taggedFriends={taggedFriends}
              onAddPhotoTag={onAddPhotoTag}
              onRemovePhotoTag={onRemovePhotoTag}
              imageHeightAuto
              hideActionMenu
              enablePhotoTags
              onMinimizePhoto={onMinimizePhoto}
            />
          </div>
          {item.description ? (
            <div className={classes.info}>
              <HtmlViewer html={item.description} />
            </div>
          ) : null}
          <div className={classes.photoReaction}>
            <ItemDetailInteraction
              identity={identity}
              handleAction={handleAction}
            />
          </div>
        </div>
      </BlockContent>
    </Block>
  );
}

export default PhotoDetail;

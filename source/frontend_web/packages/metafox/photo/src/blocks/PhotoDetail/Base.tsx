import { useGlobal } from '@metafox/framework';
import { Block, BlockContent } from '@metafox/layout';
import { PhotoDetailProps } from '@metafox/photo/types';
import React from 'react';
import useStyles from './styles';

export type Props = PhotoDetailProps;

export default function PhotoDetail({
  item,
  identity,
  user,
  blockProps
}: Props) {
  const classes = useStyles();
  const { jsxBackend, dispatch, ItemDetailInteractionInModal } = useGlobal();
  const PhotoItemView = jsxBackend.get('photo.itemView.modalCard');

  const onAddPhotoTag = (data: unknown) => {
    dispatch({ type: 'photo/onAddPhotoTag', payload: { identity, data } });
  };

  const onRemovePhotoTag = (id: unknown) => {
    dispatch({ type: 'photo/onRemovePhotoTag', payload: { identity, id } });
  };

  if (!item) return null;

  return (
    <Block blockProps={blockProps} testid={`detailview ${item.resource_name}`}>
      <BlockContent>
        <div className={classes.root}>
          <div className={classes.imageContainer}>
            <PhotoItemView
              isModal={false}
              item={item}
              identity={identity}
              onAddPhotoTag={onAddPhotoTag}
              onRemovePhotoTag={onRemovePhotoTag}
            />
          </div>
          <div className={classes.statistic}>
            <ItemDetailInteractionInModal identity={identity} />
          </div>
        </div>
      </BlockContent>
    </Block>
  );
}

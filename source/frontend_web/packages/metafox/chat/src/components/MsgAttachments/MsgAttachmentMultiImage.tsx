import { useGlobal } from '@metafox/framework';
import { getImageSrc } from '@metafox/utils';
import { styled } from '@mui/material';
import React from 'react';

const name = 'MsgAttachmentImage';

const UIMsgAttachmentImgWrapper = styled('div', {
  name,
  slot: 'UIMsgAttachmentImgWrapper'
})(({ theme }) => ({
  position: 'relative',
  width: '100%'
}));

const MediaWrapper = styled('figure', { name })(({ theme }) => ({
  margin: 0,
  display: 'block'
}));
const ImgRatioWrapper = styled('div', {
  name,
  shouldForwardProp: props =>
    props !== 'isOwner' &&
    props !== 'isPageAllMessages' &&
    props !== 'typeGridLayout'
})<{
  isOwner?: boolean;
  isPageAllMessages?: boolean;
  typeGridLayout?: string;
}>(({ theme, isOwner, isPageAllMessages, typeGridLayout }) => ({
  width: '94px',
  height: '94px',
  maxWidth: '100%',
  cursor: 'pointer',
  marginBottom: '1px',
  // 1. style fly chat
  ...(typeGridLayout === 'type-2' &&
    !isOwner && { width: '72px', height: '72px' }),

  // type 1: % 3 or is 4
  ...(typeGridLayout === 'type-1' && { width: '52px', height: '52px' }),
  ...(typeGridLayout === 'type-1' &&
    isOwner && { width: '62px', height: '62px' }),

  // 2. style page all
  ...(isPageAllMessages &&
    typeGridLayout === 'type-2' && { width: '190px', height: '190px' }),
  ...(isPageAllMessages &&
    typeGridLayout === 'type-1' && { width: '126px', height: '126px' })
}));
const ImageStyled = styled('img', {
  name,
  shouldForwardProp: props => props !== 'typeGridLayout'
})<{ typeGridLayout?: string }>(({ theme, typeGridLayout }) => ({
  position: 'absolute',
  left: 0,
  top: 0,
  width: '100%',
  objectFit: 'cover',
  borderRadius: theme.spacing(1),
  ...(typeGridLayout && {
    width: '98%',
    height: '98%'
  })
}));
interface Props {
  image: any;
  download_url?: string;
  images?: any;
  file_name?: any;
  isOwner?: boolean;
  typeGridLayout?: string;
  keyIndex?: string;
}
export default function MsgAttachmentMultiImage({
  image,
  download_url,
  images,
  typeGridLayout,
  isOwner,
  file_name,
  keyIndex
}: Props) {
  const { dispatch, usePageParams, assetUrl } = useGlobal();
  const pageParams = usePageParams();
  const isPageAllMessages = pageParams?.rid || false;

  const listImages = React.useMemo(() => {
    return images && images.length
      ? images.map((item: any, index: number) => {
          const itemSrc = getImageSrc(
            item.image,
            'origin',
            assetUrl('photo.no_image')
          );

          return {
            id: index,
            src: itemSrc,
            download_url: item.download_url,
            file_name: item.file_name
          };
        })
      : [];
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [images]);

  const imgSrc = getImageSrc(image, '500', assetUrl('photo.no_image'));
  const imgOriginSrc = getImageSrc(image, 'origin', assetUrl('photo.no_image'));

  const presentImageView = () => {
    dispatch({
      type: 'chat/room/presentImageView',
      payload: {
        image: {
          id: parseInt(keyIndex),
          src: imgOriginSrc,
          download_url,
          file_name
        },
        images: listImages
      }
    });
  };

  return (
    <UIMsgAttachmentImgWrapper>
      <MediaWrapper>
        <ImgRatioWrapper
          isOwner={!!isOwner}
          isPageAllMessages={isPageAllMessages}
          typeGridLayout={typeGridLayout}
        >
          <ImageStyled
            typeGridLayout={typeGridLayout}
            src={imgSrc}
            onClick={presentImageView}
            alt={'dwq'}
          />
        </ImgRatioWrapper>
      </MediaWrapper>
    </UIMsgAttachmentImgWrapper>
  );
}

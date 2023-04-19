import { useGlobal } from '@metafox/framework';
import { getImageSrc } from '@metafox/utils';
import { styled } from '@mui/material';
import React from 'react';

const name = 'MsgAttachmentImage';

const UIMsgAttachmentImgWrapper = styled('div', { name })(({ theme }) => ({
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
    props !== 'isOwner' && props !== 'isPageAllMessages'
})<{ isOwner?: boolean; isPageAllMessages?: boolean }>(
  ({ theme, isOwner, isPageAllMessages }) => ({
    width: '144px',
    maxWidth: '100%',
    cursor: 'pointer',
    ...(isOwner && { width: '186px' }),
    ...(isPageAllMessages && { width: '300px' })
  })
);
const ImageStyled = styled('img', { name })(({ theme }) => ({
  position: 'absolute',
  left: 0,
  top: 0,
  width: '100%',
  borderRadius: theme.spacing(1)
}));
interface Props {
  keyIndex?: string;
  isOwner?: boolean;
  image: any;
  download_url?: string;
  file_name?: string;
}

const loadImage = (setImageDimensions, imageUrl) => {
  const img = new Image();
  img.src = imageUrl;

  img.onload = () => {
    setImageDimensions({
      height: img.height,
      width: img.width
    });
  };
  img.onerror = err => {
    setImageDimensions(err);
  };
};

export default function MsgAttachmentImage({
  isOwner,
  image,
  download_url,
  file_name
}: Props) {
  const { dispatch, usePageParams, assetUrl } = useGlobal();
  const pageParams = usePageParams();
  const isPageAllMessages = pageParams?.rid || false;

  const [imageDimensions, setImageDimensions] = React.useState(null);

  const imgSrc = getImageSrc(image, '240', assetUrl('photo.no_image'));
  const imgOriginSrc = getImageSrc(image, 'origin', assetUrl('photo.no_image'));

  React.useEffect(() => {
    if (imgSrc) {
      loadImage(setImageDimensions, imgSrc);
    }
  }, [imgSrc]);

  const ratioImage = imageDimensions
    ? imageDimensions.height / imageDimensions.width
    : false;

  const presentImageView = () => {
    dispatch({
      type: 'chat/room/presentImageView',
      payload: {
        image: {
          id: 'img0',
          src: imgOriginSrc,
          download_url,
          is_image: true,
          file_name
        }
      }
    });
  };

  return (
    <UIMsgAttachmentImgWrapper className={'uiMsgAttachmentImgWrapper'}>
      <MediaWrapper>
        <ImgRatioWrapper
          isOwner={!!isOwner}
          isPageAllMessages={isPageAllMessages}
          style={{ paddingBottom: `${ratioImage ? ratioImage * 100 : 100}%` }}
        >
          <ImageStyled
            src={imgSrc}
            onClick={presentImageView}
            alt={file_name}
          />
        </ImgRatioWrapper>
      </MediaWrapper>
    </UIMsgAttachmentImgWrapper>
  );
}

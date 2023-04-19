import { useGlobal } from '@metafox/framework';
import { styled } from '@mui/material';
import React from 'react';
import MsgAttachmentMedia from './MsgAttachmentMedia';

const name = 'MsgAttachmentMultiMedia';

const UIMsgImageAttachmentWrapper = styled('div', {
  name,
  slot: 'uiMsgImageAttachmentWrapper',
  shouldForwardProp: props => props !== 'isOwner'
})<{ typeGridLayout?: string; isOwner?: boolean }>(({ theme, isOwner }) => ({
  display: 'flex',
  alignItems: 'center',
  flexWrap: 'wrap',
  justifyContent: isOwner ? 'flex-end' : 'flex-start',
  marginTop: theme.spacing(0.25)
}));

const UIMsgImageAttachmentItem = styled('div', {
  name,
  slot: 'uiMsgImageAttachmentItem',
  shouldForwardProp: props =>
    props !== 'typeGridLayout' &&
    props !== 'isPageAllMessages' &&
    props !== 'isLastItem'
})<{
  typeGridLayout?: string;
  isPageAllMessages?: boolean;
  isLastItem?: boolean;
}>(({ theme, typeGridLayout, isPageAllMessages, isLastItem }) => ({
  ...(typeGridLayout === 'type-1' && {
    flexBasis: '33.333333%'
  }),
  ...(typeGridLayout === 'type-2' && {
    flexBasis: '50%'
  })
}));
interface IProps {
  mediaItems: any[];
  isOwner: boolean;
  isOther?: boolean;
}
export default function MsgAttachmentMultiMedia({
  mediaItems,
  isOwner
}: IProps) {
  const { usePageParams } = useGlobal();
  const pageParams = usePageParams();

  const isPageAllMessages = pageParams?.rid || false;

  const countImage = mediaItems.length;
  const typeGridLayout =
    countImage % 3 === 0
      ? 'type-1'
      : countImage % 3 === 1
      ? 'type-1'
      : 'type-2';

  return (
    <UIMsgImageAttachmentWrapper isOwner={isOwner}>
      {mediaItems.map((item: any, i: number) => (
        <UIMsgImageAttachmentItem
          typeGridLayout={typeGridLayout}
          isPageAllMessages={isPageAllMessages}
          isLastItem={i === mediaItems.length - 1}
          key={`k${i}`}
        >
          <MsgAttachmentMedia
            {...item}
            keyIndex={i}
            layout="multi-image"
            totalImages={mediaItems}
            typeGridLayout={typeGridLayout}
            isOwner={isOwner}
          />
        </UIMsgImageAttachmentItem>
      ))}
    </UIMsgImageAttachmentWrapper>
  );
}

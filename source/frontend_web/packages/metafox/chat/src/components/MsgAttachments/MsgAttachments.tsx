import { MsgItemShape } from '@metafox/chat/types';
import { useGlobal } from '@metafox/framework';
import React from 'react';
import MsgAttachment from './MsgAttachment';
import MsgAttachmentMultiMedia from './MsgAttachmentMultiMedia';

export interface Props {
  message: MsgItemShape;
  isOwner?: boolean;
}

export const useCheckImageFile = attachments => {
  const { useGetItem } = useGlobal();

  const dataConvert = attachments.map(item => {
    if (typeof item === 'string') {
      // eslint-disable-next-line react-hooks/rules-of-hooks
      const attachment: any = useGetItem(item);

      return attachment;
    } else {
      return item;
    }
  });

  const multiImageFile = [];
  const otherFile = [];

  dataConvert.map(item => {
    if (item.is_image) {
      multiImageFile.push(item);
    } else {
      otherFile.push(item);
    }
  });

  return { multiImageFile, otherFile, attachments: dataConvert };
};

export default function MsgAttachments({
  message,
  isOwner
}: Props): JSX.Element {
  const { multiImageFile, otherFile, attachments } = useCheckImageFile(
    message?.attachments
  );

  if (
    !message.attachments?.length ||
    (!multiImageFile.length && !otherFile.length)
  ) {
    return null;
  }

  // if it has multiple image (countimage > 1) => all attachment is image
  if (multiImageFile.length > 1) {
    return (
      <>
        <MsgAttachmentMultiMedia
          mediaItems={multiImageFile}
          isOwner={isOwner}
        />
        {otherFile.length
          ? otherFile.map((item, i) => (
              <MsgAttachment item={item} key={`k${i}`} isOwner={isOwner} />
            ))
          : null}
      </>
    );
  }

  return attachments.map((item, i) => (
    <MsgAttachment item={item} key={`k${i}`} isOwner={isOwner} />
  )) as any;
}

/**
 * @type: ui
 * name: chat.messageContent.text
 */

import formatTextMsg from '@metafox/chat/services/formatTextMsg';
import { MsgContentProps } from '@metafox/chat/types';
import { styled, Tooltip } from '@mui/material';
import React from 'react';
import MsgAttachments from '../MsgAttachments';
// import MsgEmbeds from '../MsgEmbeds';
import MsgQuote from '../MsgQuote';

const name = 'MessageText';

const UIChatMsgItemMsg = styled('div', {
  name,
  slot: 'uiChatMsgItemMsg',
  shouldForwardProp: prop => prop !== 'isOwner' && prop !== 'isQuote'
})<{ isOwner?: boolean; isQuote?: boolean }>(({ theme, isOwner, isQuote }) => ({
  borderRadius: theme.spacing(1),
  fontSize: theme.mixins.pxToRem(15),
  padding: theme.spacing(1.25),
  display: 'flex',
  alignItems: 'center',
  backgroundColor: theme.palette.grey['100'],
  ...(theme.palette.mode === 'dark' && {
    backgroundColor: theme.palette.grey['600']
  }),
  overflowWrap: 'break-word',
  '& a': {
    color: isOwner ? '#fff' : theme.palette.text.primary,
    textDecoration: 'underline',
    cursor: 'pointer',
    overflowWrap: 'break-word'
  },
  ...(isOwner && {
    backgroundColor: theme.palette.primary.main,
    color: '#fff !important'
  })
}));

const UIChatMsgItemBodyInnerWrapper = styled('div', {
  name,
  slot: 'uiChatMsgItemBodyInnerWrapper',
  shouldForwardProp: prop => prop !== 'isOwner' && prop !== 'filter'
})<{ isOwner?: boolean; filter?: boolean }>(({ theme, isOwner, filter }) => ({
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'flex-start',
  ...(isOwner && !filter && { alignItems: 'flex-end' })
}));

export default function MessageText({
  message,
  isOwner,
  createdDate,
  tooltipPosition
}: MsgContentProps) {
  const formattedMsg = message.message
    ? formatTextMsg(message.message, { mentions: message.mentions })
    : null;

  const dataQuote = message?.extra;

  return (
    <Tooltip
      title={createdDate}
      placement={tooltipPosition}
      PopperProps={{
        disablePortal: true
      }}
    >
      <UIChatMsgItemBodyInnerWrapper
        isOwner={isOwner}
        filter={message?.filtered}
      >
        {dataQuote ? (
          <MsgQuote dataQuote={dataQuote} isOwner={isOwner} />
        ) : null}
        {formattedMsg ? (
          <UIChatMsgItemMsg
            isQuote={Boolean(dataQuote)}
            isOwner={isOwner}
            className={'uiChatMsgItemMsg'}
            dangerouslySetInnerHTML={{ __html: formattedMsg }}
          />
        ) : null}
        <MsgAttachments message={message} isOwner={isOwner} />
        {/* <MsgEmbeds message={message} /> */}
      </UIChatMsgItemBodyInnerWrapper>
    </Tooltip>
  );
}

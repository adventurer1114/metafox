import { styled } from '@mui/material';
import { isEmpty } from 'lodash';
import React from 'react';
import MsgQuote from '../MsgQuote';

interface MessageSetProps {
  item: any;
}

const name = 'MsgSet';

const UIChatMsg = styled('div', {
  name,
  slot: 'uiChatMsgSet'
})(({ theme }) => ({
  display: 'flex',
  flexDirection: 'row-reverse',
  padding: theme.spacing(0, 2),
  opacity: 0.6,
  pointerEvents: 'none'
}));

const UIChatMsgSetBody = styled('div', {
  name,
  slot: 'uiChatMsgSetBody',
  shouldForwardProp: prop => prop !== 'isOwner'
})<{ isOwner?: boolean }>(({ theme, isOwner }) => ({
  flex: 1,
  minWidth: 0,
  ...(isOwner && { flexDirection: 'row-reverse' })
}));

const UIChatMsgItemMsg = styled('div', {
  name,
  slot: 'uiChatMsgItemMsg',
  shouldForwardProp: prop => prop !== 'isOwner' && prop !== 'isQuote'
})<{ isOwner?: boolean; isQuote?: boolean }>(({ theme, isOwner, isQuote }) => ({
  minHeight: theme.spacing(5),
  marginBottom: theme.spacing(0.5),
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
  slot: 'uiChatMsgItemBodyInnerWrapper'
})(({ theme }) => ({
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'flex-end'
}));

export default function MsgPreFetching({ item }: MessageSetProps) {
  if (!item.text && !item.dataQuote) return null;

  return (
    <UIChatMsg>
      <UIChatMsgSetBody>
        <UIChatMsgItemBodyInnerWrapper>
          {!isEmpty(item.dataQuote) ? (
            <MsgQuote dataQuote={item.dataQuote} isOwner />
          ) : null}
          {item.text ? (
            <UIChatMsgItemMsg
              isQuote={false}
              isOwner
              className={'uiChatMsgItemMsg'}
              dangerouslySetInnerHTML={{ __html: item.text }}
            />
          ) : null}
        </UIChatMsgItemBodyInnerWrapper>
      </UIChatMsgSetBody>
    </UIChatMsg>
  );
}

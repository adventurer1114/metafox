/* eslint-disable  */
import { useItemActionMessage } from '@metafox/chat/hooks';
import { ChatMsgPassProps } from '@metafox/chat/types';
import {
  convertDateTime,
  countAttachmentImages,
  isNotSeenMsg
} from '@metafox/chat/utils';
import MsgReactions from '../MsgReactions';
import { useActionControl, useGlobal } from '@metafox/framework';
import { UserItemShape } from '@metafox/user';
import { filterShowWhen } from '@metafox/utils';
import { styled } from '@mui/material';
import clsx from 'clsx';
import React from 'react';
import MsgToolbar from '../MsgToolbar';

interface MsgItemProps extends ChatMsgPassProps {
  msgId: string;
  isSearch?: boolean;
  showToolbar?: boolean;
  authUser: UserItemShape;
  toggleSearch?: () => void;
}
const name = 'MsgItem';

const UIChatMsgItem = styled('div', {
  name,
  slot: 'uiChatMsgItem',
  shouldForwardProp: prop => prop !== 'isOwner'
})<{ isOwner?: boolean }>(({ theme, isOwner }) => ({
  // display: 'flex',
  // '& :not(:last-child)': {
  //   marginBottom: theme.spacing(1)
  // },
  ...(isOwner && {
    flexDirection: 'row-reverse'
  }),
  minHeight: '40px',
  marginBottom: theme.spacing(0.5)
}));

const UIChatMsgItemBody = styled('div', {
  name,
  slot: 'uiChatMsgItemBody',
  shouldForwardProp: prop =>
    prop !== 'isOwner' && prop !== 'isAlert' && prop !== 'isShowReact'
})<{ isOwner?: boolean; isAlert?: boolean; isShowReact?: boolean }>(
  ({ theme, isOwner, isAlert, isShowReact }) => ({
    display: 'flex',
    // maxWidth: 'calc(100% - 50px)',
    '&:hover .uiChatItemBtn': {
      visibility: 'visible'
    },
    ...(isOwner && {
      flexDirection: 'row-reverse'
    }),
    ...(isAlert && {
      textAlign: 'center',

      ...(isShowReact && {
        justifyContent: 'center'
      })
    })
  })
);
const UIChatMsgItemBodyOuter = styled('div', {
  name,
  slot: 'uiChatMsgItemBodyOuter',
  shouldForwardProp: prop =>
    prop !== 'hasToolbar' &&
    prop !== 'multiImage' &&
    prop !== 'isOwner' &&
    prop !== 'isPageAllMessages' &&
    prop !== 'msgType' &&
    prop !== 'msgContentType'
})<{
  hasToolbar?: boolean;
  multiImage?: boolean;
  msgContentType?: string;
  isOwner?: boolean;
  isPageAllMessages?: boolean;
  msgType?: string;
}>(
  ({
    theme,
    hasToolbar,
    multiImage,
    isOwner,
    isPageAllMessages,
    msgType,
    msgContentType
  }) => ({
    maxWidth: '100%',
    ...(hasToolbar && { maxWidth: 'calc(100% - 42px)' }),
    ...(hasToolbar && isPageAllMessages && { maxWidth: 'calc(65% - 42px)' }),
    ...(msgContentType === 'messageDeleted' &&
      !isOwner && { maxWidth: 'calc(100% - 26px)' }),
    ...(multiImage && { maxWidth: '160px' }),
    ...(multiImage && isOwner && { maxWidth: '190px' }),
    ...(multiImage && isPageAllMessages && { maxWidth: '400px' }),
    ...(isPageAllMessages &&
      (msgType === 'message_pinned' || msgType === 'message_unpinned') && {
        maxWidth: '400px'
      })
  })
);

export default function MsgItem({
  msgId,
  authUser,
  showToolbar = true,
  disableReact,
  handleAction
}: MsgItemProps) {
  const { jsxBackend, usePageParams, useGetItem } = useGlobal();
  const identity = `chat.entities.message.${msgId}`;
  const message = useGetItem(identity);

  const pageParams = usePageParams();
  const isPageAllMessages = pageParams?.rid || false;

  const [handleActionLocal] = useActionControl<{}, unknown>(identity, {});

  const itemAction = useItemActionMessage();

  const { id, type, attachments, reactions, created_at, user } = message || {};

  const userMsg = useGetItem(user);

  const isOwner = authUser.id === userMsg?.id;
  const createdDate = convertDateTime(created_at);
  const countImage = countAttachmentImages(attachments);
  const isAlert = isNotSeenMsg(type);

  const isSearch = false;
  const allowEdit = isOwner;
  const canDelete = isOwner;

  const itemActionMessages = React.useMemo(
    () =>
      filterShowWhen(itemAction, {
        isSearch,
        allowEdit,
        item: message,
        canDelete
      }),
    [itemAction, isSearch, allowEdit, message, canDelete]
  );

  if (!message) return null;

  const handleActionLocalFunc = (
    type: string,
    payload?: unknown,
    meta?: unknown
  ) => {
    handleActionLocal(type, payload, meta);
    handleAction(type, message, meta);
  };

  const isShowReact = type !== 'messageDeleted';

  return (
    <UIChatMsgItem
      isOwner={isOwner}
      data-id={id}
      data-t={type}
      data-testid={id}
    >
      <UIChatMsgItemBody
        isOwner={isOwner}
        isAlert={isAlert}
        isShowReact={isShowReact}
      >
        <UIChatMsgItemBodyOuter
          hasToolbar={showToolbar}
          multiImage={countImage > 1}
          isOwner={isOwner}
          isPageAllMessages={isPageAllMessages}
          msgType={type}
          className={clsx('uiChatMsgItemBodyOuter')}
        >
          {jsxBackend.render({
            component: `chat.messageContent.${type}`,
            props: {
              message,
              isOwner,
              user,
              createdDate,
              msgType: type,
              tooltipPosition: 'top'
            }
          })}
        </UIChatMsgItemBodyOuter>
        {isShowReact && !isAlert && (
          <MsgToolbar
            identity={identity}
            handleAction={handleActionLocalFunc}
            disabled={!showToolbar}
            disableReact={disableReact}
            items={itemActionMessages}
            isOwner={isOwner}
          />
        )}
      </UIChatMsgItemBody>
      {isShowReact && (
        <MsgReactions
          identity={identity}
          disabled={!reactions}
          reactions={reactions}
          isOwner={isOwner}
          handleAction={handleActionLocalFunc}
        />
      )}
    </UIChatMsgItem>
  );
}

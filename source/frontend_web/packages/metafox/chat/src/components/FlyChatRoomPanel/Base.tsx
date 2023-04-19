import {
  Panel,
  PanelContent,
  PanelFooter,
  PanelHeader,
  PanelTitle,
  PanelToolbar,
  SearchFilter
} from '@metafox/chat/components/DockPanel';
import { useChatRoom, useItemActionDockChat } from '@metafox/chat/hooks';
import { formatGeneralMsg } from '@metafox/chat/services/formatTextMsg';
import { MsgItemShape, ReactMode } from '@metafox/chat/types';
import { useGlobal } from '@metafox/framework';
import { ScrollContainer } from '@metafox/layout';
import { LineIcon, TruncateText } from '@metafox/ui';
import { filterShowWhen } from '@metafox/utils';
import { styled, Box, CircularProgress } from '@mui/material';
import { isEmpty } from 'lodash';
import React, { useState } from 'react';
import Messages from '../Messages';
import MessageFilter from '../Messages/MessageFitler';
import FilesPreview from '../ChatComposer/FilePreview';

const name = 'FlyChatRoomPanel';
const UIChatMsgStart = styled('div', { name, slot: 'UIChatMsgStart' })(
  ({ theme }) => ({
    textAlign: 'center',
    padding: theme.spacing(2),
    fontStyle: 'italic',
    color: theme.palette.text.primary,
    fontSize: theme.spacing(1.75)
  })
);
const ReplyEditWrapper = styled('div', { name, slot: 'ReplyEditWrapper' })(
  ({ theme }) => ({
    padding: theme.spacing(0.625, 1.25),
    height: '50px',
    width: '100%',
    borderTop: theme.mixins.border('secondary'),
    display: 'flex',
    justifyContent: 'space-between',
    backgroundColor:
      theme.palette.mode === 'dark'
        ? theme.palette.grey['600']
        : theme.palette.grey['100']
  })
);
const SelectedMsg = styled(TruncateText, { name, slot: 'SelectedMsg' })(
  ({ theme }) => ({
    color: theme.palette.text.primary
  })
);
const LineIconClose = styled(LineIcon, { name, slot: 'LineIconClose' })(
  ({ theme }) => ({
    cursor: 'pointer',
    alignSelf: 'center'
  })
);
const SelectedMsgAttachment = styled('div', {
  name,
  slot: 'SelectedMsgAttachment'
})(({ theme }) => ({
  fontSize: theme.spacing(1.5),
  margin: theme.spacing(0.5, 0),
  '& .ico': {
    marginRight: theme.spacing(0.5)
  }
}));
const ContentWrapper = styled('div', {
  name,
  slot: 'ContentWrapper'
})(({ theme }) => ({
  overflow: 'hidden'
}));

const LoadingStyled = styled('div', { name, slot: 'LoadingStyled' })(
  ({ theme }) => ({
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: theme.spacing(3)
  })
);

interface State {}

interface Props {
  rid: string;
  collapsed?: boolean;
  active?: boolean;
}

interface RefMessageHandle {
  scrollToBottom: () => void;
}

export default function FlyChatRoomPanel({ rid, collapsed }: Props) {
  const { i18n, useActionControl, dispatch, jsxBackend, useGetItem } =
    useGlobal();

  const previewRef = React.useRef();
  const filesUploadRef = React.useRef();
  const refMessage = React.useRef<RefMessageHandle>();
  const scrollRef = React.useRef<HTMLDivElement>();

  const chatRoom = useChatRoom(rid);

  const [handleAction] = useActionControl<State, unknown>(rid, {});

  const [reactMode, setReactMode] = useState<ReactMode>('no_react');
  const [selectedMsg, setSelectedMsg] = useState<MsgItemShape>();
  const [data, setData] = useState(undefined);
  const [loading, setLoading] = useState(false);

  const userIdentity = data?.other_members[0] || undefined;

  const user = useGetItem(userIdentity);

  const ChatSimpleComposer = jsxBackend.get('ChatSimpleComposer');

  const handleMarkAsRead = React.useCallback(() => {
    if (rid) {
      dispatch({
        type: 'chat/room/markAsRead',
        payload: { identity: rid }
      });
    }
  }, [rid, dispatch]);

  React.useEffect(() => {
    if (rid) {
      dispatch({
        type: 'chat/room/markAsRead',
        payload: { identity: rid }
      });
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [rid]);

  React.useEffect(() => {
    if (!rid) return;

    setLoading(true);
    dispatch({
      type: 'chat/room/active',
      payload: rid,
      meta: {
        onSuccess: value => {
          setData(value);
          setLoading(false);
        },
        onFailure: () => {
          setLoading(false);
        }
      }
    });

    return () => {
      dispatch({ type: 'chat/room/inactive', payload: { rid } });
      dispatch({
        type: 'chat/room/closeSearching',
        payload: { identity: rid }
      });
    };

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const messageFilter = chatRoom?.messageFilter;
  const disableReact = false;

  const isSearching = chatRoom?.searching;

  const itemAction = useItemActionDockChat();

  const items = React.useMemo(
    () => filterShowWhen(itemAction, {}),
    [itemAction]
  );

  const msgReactNode = React.useMemo(() => {
    return selectedMsg?.message ? formatGeneralMsg(selectedMsg?.message) : '';
  }, [selectedMsg]);

  const handleCustomAction = (types: string, payload?: any) => {
    if (!types) return;

    // convert types into Array
    const typeArray = types.split(/.,| /);

    typeArray.forEach(type => {
      switch (type) {
        case 'chat/replyMessage':
          setReactMode('reply');
          setSelectedMsg(payload);
          break;
        case 'chat/editMessage':
          setReactMode('edit');
          setSelectedMsg(payload);
          break;
        default:
          break;
      }
    });
  };

  const handleCloseReactNode = () => {
    setReactMode('no_react');
    setSelectedMsg(undefined);
  };

  const handleComposeSuccess = () => {
    handleCloseReactNode();

    if (refMessage?.current) {
      refMessage.current.scrollToBottom();
    }
  };

  return (
    <Panel>
      <PanelHeader searching={isSearching}>
        <PanelTitle loading={loading} room={{ ...data, t: 'd' }} user={user}>
          {data?.name}
        </PanelTitle>
        <PanelToolbar
          items={items}
          handleAction={handleAction}
          variant="roomPanel"
        />
      </PanelHeader>
      <SearchFilter
        hide={!isSearching}
        roomId={rid}
        placeholder={i18n.formatMessage({ id: 'search_messages_dots' })}
        size={30}
        searching={isSearching}
      />
      <Box sx={{ flex: 1, minHeight: 0 }}>
        <ScrollContainer
          autoHide={false}
          autoHeight
          autoHeightMax={'100%'}
          ref={scrollRef}
        >
          <PanelContent
            searching={isSearching}
            collapsed={collapsed}
            ref={scrollRef}
          >
            {isSearching || loading ? null : (
              <UIChatMsgStart>
                {i18n.formatMessage({ id: 'start_of_conversation' })}
              </UIChatMsgStart>
            )}

            {loading && (
              <LoadingStyled data-testid="loadingIndicator">
                <CircularProgress size={24} />
              </LoadingStyled>
            )}
            {isSearching && messageFilter ? (
              <MessageFilter
                items={messageFilter}
                room={data}
                disableReact
                handleAction={handleAction}
              />
            ) : (
              <Messages
                rid={rid}
                groups={chatRoom?.groups}
                groupIds={chatRoom?.groupIds}
                newest={chatRoom?.newest}
                room={data}
                containerRef={scrollRef}
                disableReact={disableReact}
                handleAction={handleCustomAction}
                ref={refMessage}
                loading={loading}
                roomProgress={chatRoom?.roomProgress}
              />
            )}
          </PanelContent>
        </ScrollContainer>
      </Box>

      {reactMode !== 'no_react' && (
        <ReplyEditWrapper>
          <ContentWrapper>
            <div>
              {reactMode === 'reply'
                ? i18n.formatMessage(
                    { id: 'reply_to_user_at_timestamp' },
                    {
                      user: selectedMsg?.user?.full_name,
                      time: new Date(selectedMsg.created_at).toLocaleTimeString(
                        [],
                        { hour: '2-digit', minute: '2-digit' }
                      )
                    }
                  )
                : i18n.formatMessage({ id: 'editing' })}
            </div>
            {!isEmpty(selectedMsg?.attachments) ? (
              <SelectedMsgAttachment>
                <LineIcon icon="ico-paperclip-alt" />
                <span>{i18n.formatMessage({ id: 'file_attachment' })} </span>
              </SelectedMsgAttachment>
            ) : (
              <SelectedMsg
                lines={1}
                dangerouslySetInnerHTML={{
                  __html: msgReactNode
                }}
              />
            )}
          </ContentWrapper>
          <LineIconClose icon="ico-close" onClick={handleCloseReactNode} />
        </ReplyEditWrapper>
      )}
      <FilesPreview ref={previewRef} filesUploadRef={filesUploadRef} />
      <PanelFooter searching={isSearching}>
        <ChatSimpleComposer
          rid={rid}
          msgId={selectedMsg?.id}
          reactMode={reactMode}
          text={reactMode === 'edit' ? msgReactNode : ''}
          onSuccess={handleComposeSuccess}
          previewRef={previewRef}
          ref={filesUploadRef}
          onMarkAsRead={handleMarkAsRead}
        />
      </PanelFooter>
    </Panel>
  );
}

import { PanelFooter } from '@metafox/chat/components/DockPanel';
import { MsgItemShape, ReactMode } from '@metafox/chat/types';
import { BlockViewProps, useGetItem, useGlobal } from '@metafox/framework';
import { ScrollContainer } from '@metafox/layout';
import { LineIcon, TruncateText } from '@metafox/ui';
import { styled, useTheme, Box, CircularProgress } from '@mui/material';
import React, { useState } from 'react';
import HeaderRoom from './Header';
import NoConversation from './NoConversation';
import { isEmpty } from 'lodash';
import Messages from '../Messages';
import MessageFilter from '../Messages/MessageFitler';
import { useChatRoom } from '@metafox/chat/hooks';
import { formatGeneralMsg } from '@metafox/chat/services/formatTextMsg';
import FilesPreview from '../ChatComposer/FilePreview';

const name = 'ChatRoomPanel';

const Root = styled('div', {
  name,
  slot: 'root',
  shouldForwardProp: props => props !== 'loading'
})<{ loading?: boolean }>(({ theme, loading }) => ({
  borderLeft: theme.mixins.border('secondary'),
  backgroundColor: theme.palette.background.paper,
  display: 'flex',
  flexDirection: 'row',
  width: '100%',
  height: '100%',
  '& .MuiAvatar-root': {
    fontSize: theme.mixins.pxToRem(14)
  }
}));

const MainContent = styled('div', { name, slot: 'mainContent' })(
  ({ theme }) => ({
    flex: 1,
    display: 'flex',
    width: '100%',
    height: '100%'
  })
);

const Body = styled('div')(({ theme }) => ({
  display: 'flex',
  flexDirection: 'row',
  width: '100%',
  flex: 1,
  minHeight: 0
}));

const Main = styled('div', {
  name,
  slot: 'Main',
  shouldForwardProp: props => props !== 'isMobile'
})<{ isMobile?: boolean }>(({ theme, isMobile }) => ({
  flex: 1,
  display: 'flex',
  flexDirection: 'column',
  overflow: 'hidden',
  height: '100%'
}));

const UIChatMsgStart = styled('div')(({ theme }) => ({
  textAlign: 'center',
  padding: theme.spacing(2),
  fontStyle: 'italic',
  color:
    theme.palette.mode === 'dark'
      ? theme.palette.text.primary
      : theme.palette.grey['700'],
  fontSize: theme.spacing(1.75)
}));

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
interface RefMessageHandle {
  scrollToBottom: () => void;
}

export interface Props extends BlockViewProps {}

export default function Block(props: Props) {
  const {
    i18n,
    jsxBackend,
    usePageParams,
    useActionControl,
    dispatch,
    useIsMobile
  } = useGlobal();

  const pageParams = usePageParams();
  const { rid } = pageParams;

  const isMobile = useIsMobile();

  const scrollRef = React.useRef<HTMLDivElement>();
  const previewRef = React.useRef();
  const filesUploadRef = React.useRef();
  const refMessage = React.useRef<RefMessageHandle>();

  const chatRoom = useChatRoom(rid);

  const [handleAction] = useActionControl<State, unknown>(rid, {});

  const [reactMode, setReactMode] = useState<ReactMode>('no_react');
  const [selectedMsg, setSelectedMsg] = useState<MsgItemShape>();
  const [data, setData] = useState(undefined);
  const [loading, setLoading] = useState(false);

  const userSelectedMsg = useGetItem(selectedMsg?.user);
  const [loadingMsgs, setLoadingMsgs] = React.useState(false);

  const theme = useTheme();

  const isSearching = chatRoom?.searching;

  const ChatSimpleComposer = jsxBackend.get('ChatSimpleComposer');

  const oldScrollOffset = React.useRef();

  const handleScroll = evt => {
    if (evt.target.scrollTop < 100 && !loadingMsgs) {
      const { lastMsgId, endLoadmoreMessage } = chatRoom;

      if (!lastMsgId || endLoadmoreMessage) return;

      oldScrollOffset.current =
        evt.target.scrollHeight - evt.target.clientHeight;
      setLoadingMsgs(true);
      dispatch({
        type: 'chat/room/loadHistory',
        payload: { rid, lastMsgId },
        meta: {
          onSuccess: () => {
            setLoadingMsgs(false);
          }
        }
      });
    }
  };

  React.useEffect(() => {
    // check when some msg pushed
    if (!chatRoom?.lastMsgId || !oldScrollOffset.current) return;

    const curScrollPos = 0;
    const newScroll =
      scrollRef.current.scrollHeight - scrollRef.current.clientHeight;

    scrollRef.current.scrollTop =
      curScrollPos + (newScroll - oldScrollOffset.current);
    oldScrollOffset.current = 0;
  }, [chatRoom?.lastMsgId]);

  const handleCloseReactNode = () => {
    setReactMode('no_react');
    setSelectedMsg(undefined);
  };

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
      setLoading(true);
      dispatch({
        type: 'chat/room/markAsRead',
        payload: { identity: rid }
      });
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
    }

    return () => {
      dispatch({ type: 'chat/room/inactive', payload: { rid } });
      dispatch({
        type: 'chat/room/closeSearching',
        payload: { identity: rid }
      });

      handleCloseReactNode();
      setData(undefined);
    };

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [rid]);

  const loadingData = loading || (!data && rid);
  const disableReact = false;
  const messageFilter = chatRoom?.messageFilter;
  const styleScroll: React.CSSProperties = {
    backgroundColor: theme.palette.background.paper
  };

  const msgReactNode = React.useMemo(() => {
    return selectedMsg?.message ? formatGeneralMsg(selectedMsg?.message) : '';
  }, [selectedMsg]);

  if (!rid && !isMobile && !data) return <NoConversation />;

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

  const handleComposeSuccess = () => {
    handleCloseReactNode();

    if (refMessage?.current) {
      refMessage.current.scrollToBottom();
    }
  };

  return (
    <Root loading={loadingData}>
      <MainContent>
        <Body>
          <Main isMobile={isMobile}>
            <HeaderRoom room={data} searching={isSearching} />
            <Box sx={{ flex: 1, minHeight: 0 }}>
              <ScrollContainer
                autoHeight
                autoHeightMax={'100%'}
                ref={scrollRef}
                style={styleScroll}
                onScroll={handleScroll}
              >
                {isSearching ||
                loading ||
                !chatRoom?.endLoadmoreMessage ? null : (
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
                  <Box sx={{ mt: 2 }}>
                    <MessageFilter
                      items={messageFilter}
                      room={data}
                      disableReact
                      handleAction={handleAction}
                    />
                  </Box>
                ) : (
                  <Messages
                    rid={rid}
                    groups={chatRoom?.groups}
                    groupIds={chatRoom?.groupIds}
                    newest={chatRoom?.newest}
                    preFetchingMsg={chatRoom?.preFetchingMsg}
                    room={data}
                    containerRef={scrollRef}
                    disableReact={disableReact}
                    handleAction={handleCustomAction}
                    ref={refMessage}
                    loading={loading}
                    roomProgress={chatRoom?.roomProgress}
                    isAllPage
                  />
                )}
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
                            user: userSelectedMsg?.full_name,
                            time: new Date(
                              selectedMsg.created_at
                            ).toLocaleTimeString([], {
                              hour: '2-digit',
                              minute: '2-digit'
                            })
                          }
                        )
                      : i18n.formatMessage({ id: 'editing' })}
                  </div>
                  {!isEmpty(selectedMsg?.attachments) ? (
                    <SelectedMsgAttachment>
                      <LineIcon icon="ico-paperclip-alt" />
                      <span>
                        {i18n.formatMessage({ id: 'file_attachment' })}
                      </span>
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
                <LineIconClose
                  icon="ico-close"
                  onClick={() => setReactMode('no_react')}
                />
              </ReplyEditWrapper>
            )}
            <FilesPreview
              ref={previewRef}
              filesUploadRef={filesUploadRef}
              isAllPage
            />
            <PanelFooter searching={isSearching}>
              <ChatSimpleComposer
                rid={rid}
                msgId={selectedMsg?.id}
                reactMode={reactMode}
                text={reactMode === 'edit' ? msgReactNode : ''}
                onSuccess={handleComposeSuccess}
                previewRef={previewRef}
                ref={filesUploadRef}
                isAllPage
                onMarkAsRead={handleMarkAsRead}
              />
            </PanelFooter>
          </Main>
        </Body>
      </MainContent>
    </Root>
  );
}

import { ChatMsgPassProps, MsgGroupShape } from '@metafox/chat/types';
import { convertDateTime } from '@metafox/chat/utils';
import { useScrollRef } from '@metafox/framework';
import { styled } from '@mui/material';
import React from 'react';
import { MessagesContext } from '../ChatComposer/context';
import MsgGroup from './MsgGroup';
import ChatFileProgress from '../ChatFileProgess';
import { isEmpty } from 'lodash';

const name = 'Messages';

const DateTimeGroup = styled('div', { name, slot: 'DateTimeGroup' })(
  ({ theme }) => ({
    textAlign: 'center',
    color: theme.palette.grey['600']
  })
);
interface MessagesProps extends ChatMsgPassProps {
  rid?: string;
  groups: Record<string, MsgGroupShape>;
  groupIds: string[];
  containerRef: React.RefObject<HTMLDivElement>;
  newest?: number;
  isAllPage?: boolean;
  roomProgress?: any;
  loading?;
  boolean;
}

const THRESHOLD_SCROLL = 110;

function Messages(
  {
    rid,
    groups,
    groupIds,
    newest,
    disableReact,
    room,
    containerRef,
    handleAction,
    isAllPage,
    roomProgress,
    loading
  }: MessagesProps,
  ref
) {
  const scrollRef = useScrollRef();
  const [initMessage, setInitMessage] = React.useState(false);

  const scrollToBottom = () => {
    const yOffset = 0;
    const y = scrollRef.current.scrollHeight + yOffset;

    scrollRef.current.scrollTo({ top: y });
  };

  React.useEffect(() => {
    if (!initMessage) return;

    // make sure scroll bottom when dom change
    setTimeout(() => {
      scrollToBottom();
    }, 100);

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [rid, initMessage, loading]);

  React.useEffect(() => {
    setInitMessage(!!groupIds?.length);

    return () => setInitMessage(false);
  }, [groupIds?.length]);

  React.useLayoutEffect(() => {
    if (scrollRef?.current && newest) {
      const ele = scrollRef.current;

      if (
        ele.scrollHeight - ele.scrollTop - THRESHOLD_SCROLL <=
        ele.clientHeight
      ) {
        scrollToBottom();
      }
    }

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [newest]);

  React.useImperativeHandle(ref, () => {
    return {
      scrollToBottom: () => {
        scrollToBottom();
      }
    };
  });

  if (!room || !groups) {
    return null;
  }

  return (
    <MessagesContext.Provider value={containerRef}>
      {groupIds.map((groupId, i) => (
        <div key={`k.0${i}`}>
          {groups[groupId] && convertDateTime(groups[groupId]?.ts) ? (
            <DateTimeGroup>
              {convertDateTime(groups[groupId]?.ts)}
            </DateTimeGroup>
          ) : null}

          <MsgGroup
            msgGroup={groups[groupId]}
            disableReact={disableReact}
            handleAction={handleAction}
          />
        </div>
      ))}
      {!isEmpty(roomProgress) ? (
        <ChatFileProgress
          data={roomProgress}
          eventInit={scrollToBottom}
          isAllPage={isAllPage}
        />
      ) : null}
    </MessagesContext.Provider>
  );
}

export default React.forwardRef(Messages);

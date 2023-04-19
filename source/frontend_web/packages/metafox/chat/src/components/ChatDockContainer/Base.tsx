import FlyBuddyMinimize from '@metafox/chat/components/FlyBuddyMinimize';
import FlyChatRoomPanel from '@metafox/chat/components/FlyChatRoomPanel';
import { NEW_CHAT_ROOM } from '@metafox/chat/constants';
import { useOpenChatRooms } from '@metafox/chat/hooks';
import { useGlobal, useIsMobile } from '@metafox/framework';
import { styled } from '@mui/material';
import React from 'react';
import { useLocation } from 'react-router-dom';
import FlyNewRoomPanel from '../FlyNewRoomPanel';
import UnreadMessageBadgeAware from '../UnreadMessageBadgeAware';

const Root = styled('div')(({ theme }) => ({
  '& .MuiAvatar-root': {
    fontSize: theme.mixins.pxToRem(12)
  }
}));
const DockContainer = styled('div')(({ theme }) => ({
  position: 'fixed',
  zIndex: 998,
  right: 0,
  bottom: 0,
  transform: 'translateZ(0)',
  display: 'flex',
  flexDirection: 'row-reverse'
}));

const PanelSide = styled('div')(({ theme }) => ({
  position: 'absolute',
  bottom: 24,
  right: 24,
  zIndex: 1000
}));

const PanelContainer = styled('div')(({ theme }) => ({
  position: 'absolute',
  bottom: 0,
  right: 90,
  display: 'flex',
  flexDirection: 'row-reverse',
  '&:after': {
    clear: 'both',
    content: "'.'",
    display: 'block',
    fontSize: '0',
    height: '0',
    lineHeight: 0,
    visibility: 'hidden'
  }
}));

export default function ChatDockContainer() {
  const { useLoggedIn, dispatch, getSetting } = useGlobal();
  const location = useLocation() as any;
  const setting: any = getSetting();

  const openRooms = useOpenChatRooms();

  const loggedIn = useLoggedIn();

  const isMobile = useIsMobile();

  const firstUpdate = React.useRef(true);

  const isNoShow = React.useMemo(() => {
    function funcCheckShow() {
      return ['admincp', 'messages', 'page-not-found'].some(item => {
        return location.pathname.includes(item);
      });
    }

    let result = false;

    result = funcCheckShow();

    // homepage
    if (isMobile) result = true;

    return result;
  }, [isMobile, location.pathname]);

  const openRoomsPanel = React.useMemo(
    () => openRooms.values.filter(item => !item.collapsed),
    [openRooms]
  );

  React.useEffect(() => {
    dispatch({
      type: 'chat/getRoomsLocal'
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  React.useEffect(() => {
    if (firstUpdate.current) {
      firstUpdate.current = false;

      return;
    }

    dispatch({
      type: 'chat/saveChatRoomsLocal',
      payload: openRooms?.values
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [openRooms]);

  if (
    !loggedIn ||
    !setting?.chat ||
    !setting?.broadcast?.connections?.pusher?.key
  )
    return null;

  return (
    <div>
      {isNoShow ? null : (
        <Root>
          <DockContainer>
            <PanelSide>
              <FlyBuddyMinimize />
            </PanelSide>
            <PanelContainer>
              {openRooms.newChatRoom ? (
                <FlyNewRoomPanel
                  rid={NEW_CHAT_ROOM}
                  active={openRooms.active === NEW_CHAT_ROOM}
                />
              ) : null}
              {openRoomsPanel.map(room => {
                return (
                  <FlyChatRoomPanel
                    active={openRooms.active === room.rid}
                    collapsed={room.collapsed}
                    rid={room.rid}
                    key={room.rid}
                  />
                );
              })}
            </PanelContainer>
          </DockContainer>
        </Root>
      )}

      <UnreadMessageBadgeAware />
    </div>
  );
}

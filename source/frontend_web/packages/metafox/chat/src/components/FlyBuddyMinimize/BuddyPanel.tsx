import { NEW_CHAT_ROOM } from '@metafox/chat/constants';
import { useOpenChatRooms } from '@metafox/chat/hooks';
import { useGlobal } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import { styled } from '@mui/material';
import React from 'react';
import BuddyItem from './BuddyItem';
import MoreItems from './MoreItems';
import MoreOption from './MoreOptions';

const name = 'BuddyPanel';

const NewMessage = styled('div')(({ theme }) => ({
  position: 'relative',
  width: '48px',
  height: '48px',
  backgroundColor: '#fff',
  borderRadius: '50%',
  marginTop: theme.spacing(1),
  cursor: 'pointer',
  border:
    theme.palette.mode === 'light' ? theme.mixins.border('secondary') : 'none',
  boxShadow: theme.shadows[4]
}));

const AddMessageIcon = styled(LineIcon)(({ theme }) => ({
  fontSize: theme.spacing(3),
  color: theme.palette.primary.main,
  position: 'absolute',
  top: '55%',
  left: '50%',
  transform: 'translate(-50%, -50%)'
}));

const Block = styled('div')(({ theme }) => ({
  display: 'flex',
  flexDirection: 'column-reverse',
  alignItems: 'center',
  '&:hover': {
    '.moreOptionStyled': {
      visibility: 'visible'
    }
  }
}));

const MoreOptionStyled = styled('div', { name, slot: 'MoreOptionStyled' })(
  ({ theme }) => ({
    visibility: 'hidden'
  })
);

export default function BuddyPanel() {
  const { dispatch } = useGlobal();
  const limitDisplay = 5;

  const openRooms = useOpenChatRooms();

  const bubbyList = openRooms.values.filter(item => item.collapsed);

  const isAllCollapsed = openRooms.values.length === bubbyList.length;

  const togglePanel = React.useCallback(() => {
    dispatch({
      type: 'chat/openRoomPanel',
      payload: NEW_CHAT_ROOM
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const dataBubbyList = React.useMemo(
    () => bubbyList.slice(0, limitDisplay).reverse(),
    [bubbyList]
  );

  return (
    <Block>
      <NewMessage onClick={togglePanel}>
        <AddMessageIcon icon="ico-comment-plus-o" />
      </NewMessage>

      {bubbyList.length > limitDisplay && (
        <MoreItems buddyList={bubbyList} limitDisplay={limitDisplay} />
      )}
      {dataBubbyList.map((item, idx) => (
        <BuddyItem item={item} key={idx} />
      ))}
      {bubbyList && bubbyList.length > 0 && (
        <MoreOptionStyled className="moreOptionStyled">
          <MoreOption isAllCollapsed={isAllCollapsed} />
        </MoreOptionStyled>
      )}
    </Block>
  );
}

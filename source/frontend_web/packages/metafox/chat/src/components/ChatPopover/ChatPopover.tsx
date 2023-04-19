/**
 * @type: ui
 * name: messages.ui.MessagesPopper
 */

import { NEW_CHAT_ROOM } from '@metafox/chat/constants';
import { RefOf, useGlobal } from '@metafox/framework';
import { ScrollContainer } from '@metafox/layout';
import { LineIcon } from '@metafox/ui';
import {
  Box,
  Button,
  Paper,
  Popper,
  PopperProps,
  styled,
  Typography
} from '@mui/material';
import React from 'react';

const ActionItem = styled('div')(({ theme }) => ({ cursor: 'pointer' }));
const WrapperButtonIcon = styled(Button)(({ theme }) => ({
  color:
    theme.palette.mode === 'dark'
      ? theme.palette.text.primary
      : theme.palette.grey['700'],
  fontSize: theme.spacing(2.25),
  minWidth: theme.spacing(0)
}));

const ButtonIcon = styled(Button)(({ theme }) => ({
  fontSize: theme.spacing(2.25),
  minWidth: theme.spacing(6.25),
  color: theme.palette.text.primary,
  '& .ico.ico-check-circle-alt': {
    marginRight: theme.spacing(0.75),
    marginTop: theme.spacing(0.5),
    fontSize: theme.spacing(1.75)
  }
}));

// const TotalUnread = styled(Typography)(({ theme }) => ({
//   marginLeft: theme.spacing(0.5)
// }));
const TitleHeader = styled('div')(({ theme }) => ({
  display: 'flex',
  alignItems: 'flex-end'
}));
const Header = styled(Box, {
  shouldForwardProp: props => props !== 'noContent'
})<{ noContent?: boolean }>(({ theme, noContent }) => ({
  padding: theme.spacing(1.5, 2),
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'space-between',

  ...(!noContent && {
    borderBottom: '1px solid',
    borderColor: theme.palette.border?.secondary
  })
}));
const Footer = styled(Box)(({ theme }) => ({
  padding: theme.spacing(1, 2),
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'space-between',
  color: theme.palette.text.primary
}));

const WrapperSearch = styled('div')(({ theme }) => ({
  padding: theme.spacing(1, 1, 1, 2),
  display: 'flex',
  alignItems: 'center'
}));

export default function MessagesPopper({
  anchorRef,
  open,
  closePopover,
  ...rest
}: PopperProps & { anchorRef: RefOf<HTMLDivElement>; closePopover: () => {} }) {
  const { i18n, dispatch, ListView, jsxBackend } = useGlobal();
  const [openSearch, setOpenSearch] = React.useState(false);
  const [query, setQuery] = React.useState<string>('');

  const SearchBox = jsxBackend.get('ui.searchBox');

  const pagingId = 'pagination.messagesPopper.listRooms';
  const [dataSource] = React.useState({
    apiUrl: '/chat-room',
    apiParams: 'limit=50'
  });

  React.useEffect(() => {
    return () => {
      if (open)
        dispatch({
          type: 'chatplus/buddyPanel/clearSearching'
        });
    };
  }, []);

  // let totalUnread = dataSubscription
  //   .filter((item: SubscriptionItemShape) => item.open && item.alert)
  //   .reduce((total: any, item: any) => {
  //     return item?.unread ? ++total : total;
  //   }, 0);
  // totalUnread = totalUnread > 99 ? '99+' : parseInt(totalUnread, 10);

  const handleClickNewChat = React.useCallback(() => {
    dispatch({
      type: 'chat/openRoomPanel',
      payload: NEW_CHAT_ROOM
    });

    closePopover && closePopover();

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const handleClickMarkAllRead = React.useCallback(() => {
    dispatch({
      type: 'chat/room/markAllRead'
    });

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const handleClickViewAll = React.useCallback(() => {
    dispatch({
      type: 'chat/navigateMessagesPage'
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const onQueryChange = value => {
    setQuery(value);
  };

  return (
    <Popper
      id="notification"
      data-testid="notifications"
      anchorEl={anchorRef.current}
      open={open}
      {...rest}
    >
      <Paper
        sx={{
          width: 360,
          overflow: 'hidden',
          userSelect: 'none'
        }}
      >
        <Header>
          <TitleHeader>
            <Typography variant="h4">
              {i18n.formatMessage({ id: 'messages' })}
            </Typography>
            {/* <TotalUnread variant="body1">
               ({totalUnread} {i18n.formatMessage({ id: 'unread' })})
             </TotalUnread> */}
          </TitleHeader>
          <ActionItem>
            <WrapperButtonIcon onClick={handleClickNewChat}>
              <LineIcon icon="ico-compose" />
            </WrapperButtonIcon>
          </ActionItem>
        </Header>
        <WrapperSearch>
          {openSearch ? (
            <WrapperButtonIcon onClick={() => setOpenSearch(false)}>
              <LineIcon icon="ico-arrow-left" />
            </WrapperButtonIcon>
          ) : null}
          <SearchBox
            placeholder={i18n.formatMessage({ id: 'search_people' })}
            value={query}
            onQueryChange={onQueryChange}
            onFocus={() => setOpenSearch(true)}
            sx={{ width: '100%' }}
          />
        </WrapperSearch>
        <ScrollContainer
          autoHide
          autoHeight
          autoHeightMax={320}
          autoHeightMin={40}
        >
          <ListView
            acceptQuerySearch
            query={query}
            dataSource={dataSource}
            pagingId={pagingId}
            canLoadMore
            clearDataOnUnMount
            gridContainerProps={{ rowSpacing: 0.5 }}
            gridLayout="ChatRoom - Main Card - Popper"
            itemLayout="ChatRoom - Main Card - Popper"
            itemView="blocked.itemView.chatroomCard"
            emptyPage="core.block.no_content"
            emptyPageProps={{
              title: 'no_messages',
              contentStyle: {
                sx: {
                  marginTop: '16px'
                }
              }
            }}
          />
        </ScrollContainer>
        <Footer>
          <ActionItem>
            <ButtonIcon onClick={handleClickMarkAllRead}>
              <LineIcon icon="ico-check-circle-alt" />
              <Typography variant="body1">
                {i18n.formatMessage({ id: 'mark_all_as_read' })}
              </Typography>
            </ButtonIcon>
          </ActionItem>
          <ActionItem>
            <Typography
              variant="body1"
              onClick={handleClickViewAll}
              // sx={{ textDecoration: 'underline' }}
            >
              {i18n.formatMessage({ id: 'view_all_messages' })}
            </Typography>
          </ActionItem>
        </Footer>
      </Paper>
    </Popper>
  );
}

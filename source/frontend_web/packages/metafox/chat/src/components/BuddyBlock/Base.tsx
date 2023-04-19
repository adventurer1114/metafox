import { BlockViewProps, useGlobal } from '@metafox/framework';
import { ScrollContainer } from '@metafox/layout';
import { Box, styled } from '@mui/material';
import React, { useState } from 'react';

export interface Props extends BlockViewProps {}

const name = 'BuddyBlock';

const Root = styled(Box, { name, slot: 'root' })(({ theme }) => ({
  backgroundColor: theme.palette.background.paper,
  width: '100%',
  height: '100%',
  display: 'flex',
  flexDirection: 'column'
}));

const WrapperHeader = styled('div', { name, slot: 'WrapperHeader' })(
  ({ theme }) => ({})
);
const HeaderStyled = styled('div')(({ theme }) => ({
  alignItems: 'center',
  boxSizing: 'border-box',
  display: 'flex',
  height: theme.spacing(9),
  padding: theme.spacing(1, 1, 1, 2),
  justifyContent: 'space-between'
}));

const HeaderTitle = styled('div')(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(24),
  lineHeight: theme.mixins.pxToRem(36),
  fontWeight: theme.typography.fontWeightMedium
}));

const BlockSearchStyled = styled('div', { name, slot: 'BlockSearch' })(
  ({ theme }) => ({
    padding: theme.spacing(1, 1, 1, 2)
  })
);

const HeaderActionStyled = styled('div')(({ theme }) => ({}));

const Content = styled('div')(({ theme }) => ({
  backgroundColor: theme.palette.background.paper,
  padding: theme.spacing(1, 0, 1, 1),
  flex: 1,
  minHeight: 0
}));

export default function Base({ title }: Props) {
  const { ListView, jsxBackend, i18n } = useGlobal();
  const scrollRef = React.useRef();

  const dataSource = {
    apiUrl: '/chat-room'
  };
  const [query, setQuery] = useState('');

  const pagingId = 'pagination.listRooms';
  const SearchBox = jsxBackend.get('ui.searchBox');

  const onQueryChange = value => {
    setQuery(value);
  };

  return (
    <Root>
      <WrapperHeader>
        <HeaderStyled>
          <HeaderTitle>
            {i18n.formatMessage({ id: title || 'message' })}
          </HeaderTitle>
          <HeaderActionStyled>
            {jsxBackend.render({
              component: 'chat.buttonAddChatRoom'
            })}
          </HeaderActionStyled>
        </HeaderStyled>
        <BlockSearchStyled>
          {jsxBackend.render({
            component: SearchBox,
            props: { placeholder: 'search_people', onQueryChange, value: query }
          })}
        </BlockSearchStyled>
      </WrapperHeader>
      <Content>
        <ScrollContainer
          autoHide
          autoHeight
          autoHeightMax={'100%'}
          ref={scrollRef}
        >
          <ListView
            acceptQuerySearch
            query={query}
            dataSource={dataSource}
            pagingId={pagingId}
            canLoadMore
            canLoadSmooth
            gridContainerProps={{ rowSpacing: 0.5 }}
            gridLayout="ChatRoom - Main Card"
            blockLayout="Event Lists"
            itemLayout="ChatRoom - Main Card"
            itemView="blocked.itemView.chatroomCard"
            emptyPage="core.block.no_content"
            emptyPageProps={{
              title: 'no_messages',
              variant: 'center'
            }}
          />
        </ScrollContainer>
      </Content>
    </Root>
  );
}

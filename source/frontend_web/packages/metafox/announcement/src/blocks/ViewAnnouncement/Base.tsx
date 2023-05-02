import { Link, useGlobal } from '@metafox/framework';
// utils
import HtmlViewer from '@metafox/html-viewer';
// layout
import { Block, BlockContent } from '@metafox/layout';
// types
import { AnnouncementItemProps as ItemProps } from '@metafox/announcement/types';
// components
import { FormatDate, ItemTitle, ItemUserShape, UserAvatar } from '@metafox/ui';
import { Box, Button, styled, Typography } from '@mui/material';
import React from 'react';
import LoadingSkeleton from './LoadingSkeleton';

const AvatarWrapper = styled('div', { name: 'AvatarWrapper' })(({ theme }) => ({
  marginRight: theme.spacing(1.5)
}));

const UserRead = styled('span', { name: 'userRead' })(({ theme }) => ({
  marginLeft: theme.spacing(0.5),
  color: theme.palette.primary.main,
  cursor: 'pointer',
  '&:hover': {
    textDecoration: 'underline'
  }
}));

export default function DetailView({
  user,
  item,
  identity,
  state,
  handleAction
}: ItemProps) {
  const { ItemDetailInteraction, i18n, dispatch } = useGlobal();

  if (!user || !item) return null;

  const openListViewer = () => {
    dispatch({ type: 'announcement/openListViewer', payload: { identity } });
  };

  const onMarkAsRead = () => {
    if (item.is_read) return;

    dispatch({
      type: 'announcement/markAsRead',
      payload: { id: item.id, isDetail: true },
      meta: { onSuccess: () => {} }
    });
  };

  return (
    <Block testid={`detailview ${item.resource_name}`}>
      <BlockContent>
        <Box p={2} bgcolor="background.paper">
          <ItemTitle variant="h3" component={'div'} pr={2} showFull>
            <Typography
              component="h1"
              variant="h3"
              sx={{
                pr: 2.5,
                display: { sm: 'inline', xs: 'block' },
                mt: { sm: 0, xs: 1 },
                verticalAlign: 'middle'
              }}
            >
              {item?.title}
            </Typography>
          </ItemTitle>
          <Box mt={2} display="flex">
            <AvatarWrapper>
              <UserAvatar user={user as ItemUserShape} size={48} />
            </AvatarWrapper>
            <div>
              <Link
                to={user.link}
                children={user?.full_name}
                hoverCard={`/user/${user.id}`}
                color="text.primary"
                sx={{ fontSize: 15, fontWeight: 'bold', display: 'block' }}
              />
              <FormatDate
                data-testid="publishedDate"
                value={item?.creation_date}
                format="MMMM DD, yyyy"
              />
            </div>
          </Box>
          <Box component="div" mt={3} fontSize="15px">
            <HtmlViewer html={item?.text || ''} />
            <Box mt={2} sx={{ display: 'flex', alignItems: 'center' }}>
              <Button
                variant="outlined"
                size="medium"
                color="primary"
                disabled={item?.is_read}
                onClick={onMarkAsRead}
              >
                {i18n.formatMessage({
                  id: item?.is_read ? 'i_have_read_this' : 'mark_as_read'
                })}
              </Button>
              <Box ml={1}>
                {i18n.formatMessage({ id: 'read_by' })}
                <UserRead onClick={openListViewer}>
                  {i18n.formatMessage(
                    { id: 'number_user' },
                    { value: item?.statistic?.total_view }
                  )}
                </UserRead>
              </Box>
            </Box>
          </Box>
          <ItemDetailInteraction
            identity={identity}
            state={state}
            handleAction={handleAction}
          />
        </Box>
      </BlockContent>
    </Block>
  );
}

DetailView.LoadingSkeleton = LoadingSkeleton;
DetailView.displayName = 'Announcement_DetailView';

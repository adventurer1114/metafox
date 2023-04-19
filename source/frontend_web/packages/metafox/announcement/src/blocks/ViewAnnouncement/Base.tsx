import { Link, useGlobal } from '@metafox/framework';
// utils
import HtmlViewer from '@metafox/html-viewer';
// layout
import { Block, BlockContent } from '@metafox/layout';
// types
import { AnnouncementItemProps as ItemProps } from '@metafox/announcement/types';
// components
import { FormatDate, ItemTitle, ItemUserShape, UserAvatar } from '@metafox/ui';
import { Box, styled, Typography } from '@mui/material';
import React from 'react';
import LoadingSkeleton from './LoadingSkeleton';

const AvatarWrapper = styled('div', { name: 'AvatarWrapper' })(({ theme }) => ({
  marginRight: theme.spacing(1.5)
}));

export default function DetailView({
  user,
  item,
  identity,
  state,
  handleAction
}: ItemProps) {
  const { ItemDetailInteraction } = useGlobal();

  if (!user || !item) return null;

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

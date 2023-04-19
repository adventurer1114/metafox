import { Link, useGlobal } from '@metafox/framework';
import React from 'react';
import HtmlViewer from '@metafox/html-viewer';
import {
  AttachmentItem,
  DotSeparator,
  FormatDate,
  ItemUserShape,
  UserAvatar,
  ItemView,
  ItemAction,
  PendingFlag
} from '@metafox/ui';
import { styled, Box } from '@mui/material';
import LoadingSkeleton from './LoadingSkeleton';

const name = 'PostItemDetailCard';

const PostContent = styled('div', { name, slot: 'postContent' })(
  ({ theme }) => ({
    fontSize: theme.mixins.pxToRem(15),
    lineHeight: 1.33,
    marginTop: theme.spacing(3),
    '& p + p': {
      marginBottom: theme.spacing(2.5)
    }
  })
);
const AvatarWrapper = styled('div', { name, slot: 'AvatarWrapper' })(
  ({ theme }) => ({
    marginRight: theme.spacing(1.5)
  })
);
const AttachmentTitle = styled('div', { name, slot: 'attachmentTitle' })(
  ({ theme }) => ({
    fontSize: theme.mixins.pxToRem(18),
    marginTop: theme.spacing(4),
    color: theme.palette.text.secondary,
    fontWeight: theme.typography.fontWeightBold
  })
);
const Attachment = styled('div', { name, slot: 'attachment' })(({ theme }) => ({
  width: '100%',
  display: 'flex',
  flexWrap: 'wrap',
  marginTop: theme.spacing(2),
  justifyContent: 'space-between'
}));
const AttachmentItemWrapper = styled('div', {
  name,
  slot: 'attachmentItemWrapper'
})(({ theme }) => ({
  marginTop: theme.spacing(2),
  flexGrow: 0,
  flexShrink: 0,
  flexBasis: 'calc(50% - 8px)',
  minWidth: 300
}));

export default function PostItemDetailCard({
  item,
  identity,
  itemProps,
  user,
  state,
  handleAction,
  wrapAs,
  wrapProps
}) {
  const { ItemActionMenu, useGetItem, useGetItems, i18n } = useGlobal();
  const attachments = useGetItems(item?.attachments);
  const { content: description, thread: threadIdentity } = item;
  const thread = useGetItem(threadIdentity);
  const to = `/forum/thread/${thread.id}`;

  return (
    <ItemView testid={item.resource_name} wrapAs={wrapAs} wrapProps={wrapProps}>
      <Box sx={{ width: '100%' }}>
        {itemProps.showActionMenu ? (
          <ItemAction sx={{ position: 'absolute', top: 8, right: 8 }}>
            <ItemActionMenu
              identity={identity}
              icon={'ico-dottedmore-vertical-o'}
              state={state}
              handleAction={handleAction}
            />
          </ItemAction>
        ) : null}
        <Box mt={2} display="flex">
          <AvatarWrapper>
            <UserAvatar user={user as ItemUserShape} size={48} />
          </AvatarWrapper>
          <Box>
            <PendingFlag variant="itemView" value={!item.is_approved} />
            <Link
              variant="body1"
              color={'text.primary'}
              to={user.link}
              children={user?.full_name}
              hoverCard={`/user/${user.id}`}
              sx={{ fontWeight: 'bold', display: 'block' }}
            />
            <DotSeparator sx={{ color: 'text.secondary', mt: 1 }}>
              <FormatDate
                data-testid="publishedDate"
                value={item?.creation_date}
                format="MMMM DD, yyyy"
              />
            </DotSeparator>
          </Box>
        </Box>
        <PostContent>
          <HtmlViewer html={description || ''} />
        </PostContent>
        {attachments?.length > 0 && (
          <>
            <AttachmentTitle>
              {i18n.formatMessage({ id: 'attachments' })}
            </AttachmentTitle>
            <Attachment>
              {attachments.map(item => (
                <AttachmentItemWrapper key={item.id.toString()}>
                  <AttachmentItem
                    fileName={item.file_name}
                    downloadUrl={item.download_url}
                    isImage={item.is_image}
                    fileSizeText={item.file_size_text}
                    size="large"
                    image={item?.image}
                  />
                </AttachmentItemWrapper>
              ))}
            </Attachment>
          </>
        )}
        <Box sx={{ color: 'text.secondary' }}>
          <span>{i18n.formatMessage({ id: 'parent_thread' })}: </span>
          <Link color={'primary'} to={to}>
            {thread.title}
          </Link>
        </Box>
      </Box>
    </ItemView>
  );
}
PostItemDetailCard.LoadingSkeleton = LoadingSkeleton;
PostItemDetailCard.displayName = 'ForumPostItem(PostItemDetailCard)';

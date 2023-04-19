import { useGlobal, Link } from '@metafox/framework';
import React from 'react';
import HtmlViewer from '@metafox/html-viewer';
import { TruncateText } from '@metafox/ui';
import { styled, Box } from '@mui/material';

const name = 'FeedPostMain';

const PostContent = styled(TruncateText, { name, slot: 'postContent' })(
  ({ theme }) => ({
    marginTop: theme.spacing(1.5),
    color: theme.palette.text.secondary,
    '& p + p': {
      marginBottom: theme.spacing(2.5)
    }
  })
);

const Wrapper = styled('div', {
  name,
  slot: 'wrapperItem'
})(({ theme }) => ({
  width: '100%',
  padding: '16px 24px',
  borderRadius: '4px',
  border: `1px solid ${theme.palette.divider}`
}));

export default function FeedPostMain({ item }) {
  const { useGetItem, i18n } = useGlobal();
  const {
    short_content: description,
    user: userIdentity,
    thread: threadIdentity
  } = item;
  const user = useGetItem(userIdentity);
  const thread = useGetItem(threadIdentity);
  const toThread = thread?.id ? `/forum/thread/${thread?.id}` : '';

  return (
    <Wrapper>
      <Box>
        <TruncateText color={'text.hint'} variant="body2" lines={1}>
          <Link
            variant="body2"
            color={'text.primary'}
            to={user.link}
            children={user?.full_name}
            hoverCard={`/user/${user.id}`}
            sx={{ fontWeight: 'bold', display: 'inline' }}
          />{' '}
          {i18n.formatMessage({ id: 'posted_a_reply_on' })}{' '}
          {toThread ? (
            <Link sx={{ display: 'inline' }} to={toThread} color="primary">
              {thread?.title}
            </Link>
          ) : null}
        </TruncateText>
      </Box>
      <PostContent>
        <TruncateText variant="body1" lines={3}>
          <HtmlViewer html={description || ''} />
        </TruncateText>
      </PostContent>
    </Wrapper>
  );
}
FeedPostMain.displayName = 'ForumFeedPostMain';

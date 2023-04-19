/**
 * @type: ui
 * name: forum_post.ui.quoteItem
 */

import { useGlobal, Link } from '@metafox/framework';
import React from 'react';
import HtmlViewer from '@metafox/html-viewer';
import { AttachmentItem, TruncateText } from '@metafox/ui';
import { styled, Box, useTheme } from '@mui/material';
import { isString } from 'lodash';

const name = 'QuotePostMain';

const PostContent = styled('div', { name, slot: 'postContent' })(
  ({ theme }) => ({
    fontSize: theme.mixins.pxToRem(13),
    lineHeight: 1.33,
    marginTop: theme.spacing(1.5),
    color:
      theme.palette.mode === 'dark'
        ? theme.palette.text.primary
        : theme.palette.text.secondary,
    '& p + p': {
      marginBottom: theme.spacing(2.5)
    }
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

export default function QuotePostMain({ item }) {
  const { useGetItem, useGetItems, i18n } = useGlobal();
  const attachments = useGetItems(item?.attachments);
  const { content: description, user: userIdentity } = item;
  let user = useGetItem(userIdentity);

  if (!user && !isString(userIdentity)) {
    user = userIdentity;
  }

  const theme = useTheme();

  return (
    <Box
      sx={{
        width: '100%',
        padding: '16px 24px',
        borderRadius: '4px',
        border: 'solid 1px rgba(85, 85, 85, 0.2)',
        backgroundColor:
          theme.palette.mode === 'dark'
            ? theme.palette.grey[700]
            : theme.palette.grey[100]
      }}
    >
      <Box>
        {user ? (
          <TruncateText
            color={
              theme.palette.mode === 'dark' ? 'text.secondary' : 'text.hint'
            }
            variant="body2"
            lines={1}
          >
            {i18n.formatMessage({ id: 'originally_posted_by' })}{' '}
            <Link
              variant="body2"
              color={'text.primary'}
              to={user?.link}
              children={user?.full_name}
              hoverCard={`/user/${user?.id}`}
              sx={{ fontWeight: 'bold', display: 'inline' }}
            />
          </TruncateText>
        ) : null}
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
            {attachments.map((item: any) => (
              <AttachmentItemWrapper key={item?.id.toString()}>
                <AttachmentItem
                  fileName={item.file_name}
                  downloadUrl={item.download_url}
                  isImage={item.is_image}
                  fileSizeText={item.file_size_text}
                  size="mini"
                  image={item?.image}
                />
              </AttachmentItemWrapper>
            ))}
          </Attachment>
        </>
      )}
    </Box>
  );
}
QuotePostMain.displayName = 'ForumQuotePostMain';

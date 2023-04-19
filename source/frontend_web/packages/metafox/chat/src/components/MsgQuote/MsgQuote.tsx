import formatTextMsg from '@metafox/chat/services/formatTextMsg';
import { useGlobal } from '@metafox/framework';
import { UserItemShape } from '@metafox/user';
import { Box, styled, Typography } from '@mui/material';
import React from 'react';
import { MsgItemShape } from '@metafox/chat/types';
import { LineIcon, TruncateText } from '@metafox/ui';
import { useCheckImageFile } from '../MsgAttachments/MsgAttachments';
import MsgAttachment from '../MsgAttachments/MsgAttachment';

const name = 'MsgAttachment';

const UIMsgAttachment = styled('div', {
  name,
  slot: 'UIMsgAttachment',
  shouldForwardProp: prop => prop !== 'isOwner'
})<{
  isOwner?: boolean;
}>(({ theme, isOwner }) => ({
  marginBottom: theme.spacing(-2),
  marginLeft: theme.spacing(1),
  marginRight: 0,
  marginTop: theme.spacing(0.25),
  paddingBottom: theme.spacing(3),
  maxWidth: 'calc(100% - 8px)',
  display: 'flex',
  flexDirection: 'row',
  overflow: 'hidden',
  border: theme.mixins.border('secondary'),
  borderRadius: theme.spacing(1),
  minHeight: '70px',
  '& a:not(.MuiAvatar-root)': {
    color: theme.palette.text.primary,
    textDecoration: 'underline',
    cursor: 'pointer'
  },
  ...(isOwner && {
    color: theme.palette.text.secondary,
    marginRight: theme.spacing(1),
    marginLeft: 0
  })
}));

const UIMsgAttachmentAuthor = styled('div', {
  name,
  slot: 'Author'
})(({ theme }) => ({
  display: 'flex',
  justifyContent: 'flex-start',
  alignItems: 'center',
  '& .MuiAvatar-root': {
    fontSize: theme.mixins.pxToRem(7)
  },
  strong: {
    padding: theme.spacing(0, 0.5)
  }
}));
const UIMsgAttachmentFlex = styled('div', {
  name,
  slot: 'MsgAttachmentFlex'
})(({ theme }) => ({
  textAlign: 'start'
}));
const UIMsgAttachmentText = styled(TruncateText, {
  name,
  slot: 'Text'
})(({ theme }) => ({
  color: theme.palette.text.secondary,
  margin: theme.spacing(0.5, 0)
}));
const UIMsgAttachmentTextDelete = styled('div', {
  name,
  slot: 'TextDelete'
})(({ theme }) => ({
  margin: theme.spacing(0.5),
  marginLeft: 0,
  fontStyle: 'italic'
}));
const UIMsgAttachmentInfoWrapper = styled('div', {
  name,
  slot: 'AttachmentInfo'
})<{ msgType?: string; isOther?: boolean }>(({ theme }) => ({
  overflow: 'hidden',
  margin: theme.spacing(1, 1, 0, 0)
}));

const DividerStyled = styled('div')(({ theme }) => ({
  width: theme.spacing(0.5),
  height: '100%',
  backgroundColor: theme.palette.grey['100']
}));

const DividerWrapperStyled = styled('div')(({ theme }) => ({
  width: theme.spacing(3),
  minWidth: theme.spacing(3),
  marginTop: theme.spacing(1.5),
  display: 'flex',
  justifyContent: 'center',
  alignItems: 'center'
}));

const AuthorNameStyled = styled(Typography)(({ theme }) => ({
  color:
    theme.palette.mode === 'light'
      ? theme.palette.grey['A200']
      : theme.palette.text.primary
}));

const UIMsgAttachmentLink = styled(TruncateText, {
  name,
  slot: 'uiMsgAttachmentLink'
})(({ theme }) => ({
  '& .ico': {
    fontSize: theme.mixins.pxToRem(12),
    marginRight: theme.spacing(0.5)
  }
}));

interface MsgQuoteProps extends MsgItemShape {
  mentions?: any;
  isOwner?: boolean;
  user: UserItemShape;
  message: string;
  type: string;
  attachments?: any;
}

interface Props {
  dataQuote: MsgQuoteProps;
  isOwner?: boolean;
}

export default function MsgQuote({ dataQuote, isOwner }: Props) {
  const { i18n } = useGlobal();

  const { type, user, message } = dataQuote;

  const { multiImageFile, otherFile, attachments } = useCheckImageFile(
    dataQuote?.attachments
  );

  return (
    <UIMsgAttachment isOwner={isOwner}>
      <DividerWrapperStyled>
        <DividerStyled />
      </DividerWrapperStyled>
      <UIMsgAttachmentInfoWrapper>
        {user ? (
          <UIMsgAttachmentAuthor>
            <AuthorNameStyled component="h2" variant="h5">
              {user.full_name || user.user_name}
            </AuthorNameStyled>
          </UIMsgAttachmentAuthor>
        ) : null}
        <UIMsgAttachmentFlex>
          {type === 'messageDeleted' ? (
            <UIMsgAttachmentTextDelete>
              {i18n.formatMessage({ id: 'message_was_deleted' })}
            </UIMsgAttachmentTextDelete>
          ) : null}
          {message && type !== 'messageDeleted' ? (
            <UIMsgAttachmentText
              lines={1}
              dangerouslySetInnerHTML={{
                __html: formatTextMsg(message)
              }}
            />
          ) : null}

          {!message &&
          (dataQuote.attachments?.length ||
            (!multiImageFile.length && !otherFile.length)) ? (
            <Box sx={{ mt: 1 }}>
              {multiImageFile.length > 1 ? (
                <UIMsgAttachmentText lines={1}>
                  {i18n.formatMessage(
                    { id: 'total_photo' },
                    { value: multiImageFile.length }
                  )}
                </UIMsgAttachmentText>
              ) : (
                <>
                  {attachments.map((item, i) => (
                    <React.Fragment key={`k${i}`}>
                      {item.is_image && item.image ? (
                        <MsgAttachment item={item} isOwner={isOwner} />
                      ) : (
                        <UIMsgAttachmentLink lines={1}>
                          <LineIcon icon="ico-paperclip-alt" />
                          {item.file_name}
                        </UIMsgAttachmentLink>
                      )}
                    </React.Fragment>
                  ))}
                </>
              )}
            </Box>
          ) : null}
        </UIMsgAttachmentFlex>
      </UIMsgAttachmentInfoWrapper>
    </UIMsgAttachment>
  );
}

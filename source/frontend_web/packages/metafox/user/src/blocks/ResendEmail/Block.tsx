/**
 * @type: block
 * name: user.block.resendEmail
 */

import { BlockViewProps, createBlock, useGlobal } from '@metafox/framework';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import FormResendEmail from '@metafox/user/components/FormResendEmail';
import { Link, styled, Typography } from '@mui/material';
import React from 'react';

const ResendLink = styled(Link)(({ theme }) => ({
  marginTop: theme.spacing(2),
  color: theme.palette.primary.main
}));

function ResendEmail({ title }) {
  const { i18n } = useGlobal();

  const [openForm, setOpenForm] = React.useState(false);

  const handleOpenForm = () => {
    setOpenForm(true);
  };

  return (
    <Block>
      <BlockHeader title={title} />
      <BlockContent>
        <Typography mb={1}>
          {i18n.formatMessage({ id: 'need_verify_your_email_continue' })}
        </Typography>
        <Typography>
          {i18n.formatMessage({ id: 'resend_email_description' })}
        </Typography>
        <ResendLink component="button" variant="body2" onClick={handleOpenForm}>
          {i18n.formatMessage({ id: 'did_not_receive_resend_email' })}
        </ResendLink>
        {openForm ? <FormResendEmail /> : null}
      </BlockContent>
    </Block>
  );
}

export default createBlock<BlockViewProps>({
  extendBlock: ResendEmail,
  defaults: {
    title: 'Verify your email',
    blockLayout: 'Resend Email - Contained'
  }
});

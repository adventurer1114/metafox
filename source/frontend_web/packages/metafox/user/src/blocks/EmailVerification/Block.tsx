/**
 * @type: block
 * name: user.block.EmailVerification
 */

import {
  BlockViewProps,
  createBlock,
  GlobalState,
  useGlobal
} from '@metafox/framework';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import FormResendEmail from '@metafox/user/components/FormResendEmail';
import { Box, CircularProgress, Link, styled, Typography } from '@mui/material';
import React from 'react';
import { useSelector } from 'react-redux';

const ResendLink = styled(Link)(({ theme }) => ({
  marginTop: theme.spacing(2),
  color: theme.palette.primary.main
}));

const Loading = styled(Box)(({ theme }) => ({
  display: 'flex',
  justifyContent: 'center'
}));

const Content = ({ loading, error, success }: any) => {
  const { i18n, navigate } = useGlobal();

  const [openForm, setOpenForm] = React.useState(false);

  const handleOpenForm = () => {
    setOpenForm(true);
  };

  if (loading) {
    return (
      <Loading>
        <CircularProgress variant="indeterminate" size={38} />
      </Loading>
    );
  }

  if (success) {
    return (
      <Typography>
        {i18n.formatMessage({
          id: 'your_email_has_been_verified_successfully'
        })}
      </Typography>
    );
  }

  if (!error) {
    navigate('/', { replace: true });

    return null;
  }

  const { title, resend, redirect } = error;

  if (redirect) {
    navigate('/', { replace: true });

    return null;
  }

  if (title && !resend) {
    return <Typography>{title}</Typography>;
  }

  if (resend) {
    return (
      <>
        <Typography component="h1" variant="h3">
          {title}
        </Typography>
        <ResendLink component="button" variant="body2" onClick={handleOpenForm}>
          {i18n.formatMessage({ id: 'did_not_receive_resend_email' })}
        </ResendLink>
        {openForm ? <FormResendEmail /> : null}
      </>
    );
  }

  return null;
};

function EmailVerification({ title }) {
  const { usePageParams, dispatch, navigate } = useGlobal();
  const pageParams = usePageParams();

  const {
    loading,
    error: err,
    success
  } = useSelector((state: GlobalState) => state.user.verifyEmail);

  const error = err && JSON.parse(err);

  const { hash } = pageParams;

  React.useEffect(() => {
    dispatch({ type: 'user/verify/email', payload: { hash } });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [hash]);

  if (success) {
    window.setTimeout(() => navigate('/login', { replace: true }), 5000);
  }

  return (
    <Block>
      <BlockHeader title={title} />
      <BlockContent>
        <Content loading={loading} error={error} success={success} />
      </BlockContent>
    </Block>
  );
}

export default createBlock<BlockViewProps>({
  extendBlock: EmailVerification,
  defaults: {
    title: 'Verification',
    blockLayout: 'Blocker'
  }
});

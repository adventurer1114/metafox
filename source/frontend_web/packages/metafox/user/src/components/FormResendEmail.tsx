import { FormBuilder } from '@metafox/form';
import { useResourceForm } from '@metafox/framework';
import { Box } from '@mui/material';
import React from 'react';

const FormResendEmail = () => {
  const formSchema = useResourceForm('user', 'user_verify', 'resend');

  return (
    <Box mt={2}>
      <FormBuilder navigationConfirmWhenDirty={false} formSchema={formSchema} />
    </Box>
  );
};
export default FormResendEmail;

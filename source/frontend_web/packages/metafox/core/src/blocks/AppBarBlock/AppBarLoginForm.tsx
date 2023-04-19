import { useResourceForm } from '@metafox/framework';
import { FormBuilder } from '@metafox/form';
import React from 'react';

export default function AppBarLoginForm() {
  const formSchema = useResourceForm('user', 'user', 'small_login');

  return (
    <FormBuilder formSchema={formSchema} navigationConfirmWhenDirty={false} />
  );
}

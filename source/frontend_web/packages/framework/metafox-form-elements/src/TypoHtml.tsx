/**
 * @type: formElement
 * name: form.element.Typo
 * chunkName: formBasic
 */

import { Typography } from '@mui/material';
import React from 'react';
import { FormFieldProps } from '@metafox/form';

const TypoHtml = ({ config }: FormFieldProps) => {
  const { plainText, component, elements, tagName, ...restConfig } = config;

  return (
    <Typography sx={{ mt: 2 }} component={tagName as any} {...restConfig}>
      <div dangerouslySetInnerHTML={{ __html: plainText }} />
    </Typography>
  );
};

export default TypoHtml;

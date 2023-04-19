/**
 * @type: formElement
 * name: form.element.Captcha
 * chunkName: formBasic
 */

import { FormFieldProps } from '@metafox/form';
import { useScript } from '@metafox/framework';
import { FormControl } from '@mui/material';
import { useField } from 'formik';
import { camelCase } from 'lodash';
import React from 'react';
import ErrorMessage from '../ErrorMessage';

const CaptchaField = ({
  config,
  disabled: forceDisabled,
  name,
  formik
}: FormFieldProps) => {
  const { margin, fullWidth, disabled, siteKey } = config;
  const [, meta] = useField(name ?? 'captcha');

  useScript(
    disabled
      ? null
      : `https://www.google.com/recaptcha/api.js?render=${siteKey}`
  );

  const haveError = Boolean(meta.error && (meta.touched || formik.submitCount));

  if (!siteKey || disabled || forceDisabled) {
    return null;
  }

  return (
    <FormControl
      margin={margin}
      fullWidth={fullWidth}
      data-testid={camelCase(`field ${name}`)}
      sx={{ margin: 0 }}
    >
      {haveError ? <ErrorMessage error={meta.error} /> : null}
    </FormControl>
  );
};

export default CaptchaField;

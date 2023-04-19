/**
 * @type: formElement
 * name: form.element.ViewMore
 * chunkName: formBasic
 */

import { FormFieldProps } from '@metafox/form';
import { useGlobal } from '@metafox/framework';
import { Link, Box } from '@mui/material';
import { useField } from 'formik';
import React, { useEffect } from 'react';

export default function ViewMore({ config, name }: FormFieldProps) {
  const [field, , { setValue }] = useField(name ?? 'ViewMoreField');

  const { i18n } = useGlobal();

  useEffect(() => {
    setValue(!!field.value);
  }, []);

  const {
    moreText = 'view_more',
    lessText = 'view_less',
    sxFieldWrapper
  } = config;

  return (
    <Box sx={sxFieldWrapper}>
      <Link
        color="primary"
        component="button"
        onClick={() => setValue(!field.value)}
        style={{ cursor: 'pointer' }}
        variant="body2"
      >
        {i18n.formatMessage({ id: field.value ? lessText : moreText })}
      </Link>
    </Box>
  );
}

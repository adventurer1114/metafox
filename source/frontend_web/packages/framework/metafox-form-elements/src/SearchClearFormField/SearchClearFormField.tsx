/**
 * @type: formElement
 * name: form.element.ClearSearchForm
 * chunkName: formElement
 */

import { FormFieldProps } from '@metafox/form/types';
import { Box, Link, FormControl } from '@mui/material';
import { pick, omit, isEqual, camelCase } from 'lodash';
import React from 'react';
import { useFormSchema } from '@metafox/form';

export default function ClearSearchFormField({
  config: {
    excludeFields,
    label = 'Reset',
    align = 'right',
    size,
    margin,
    fullWidth,
    sxFieldWrapper
  },
  name,
  formik
}: FormFieldProps) {
  const { value: valueDefault } = useFormSchema();
  const defaultRelatedValues = omit({ ...valueDefault }, excludeFields);
  const relatedValues = omit({ ...formik.values }, excludeFields);
  const excludeValues = pick({ ...formik.values }, excludeFields);
  const disableReset = isEqual(defaultRelatedValues, relatedValues);
  const handleReset = React.useCallback(() => {
    if (disableReset) return;

    formik.resetForm({ values: { ...excludeValues, ...defaultRelatedValues } });

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [disableReset, defaultRelatedValues, excludeValues]);

  return (
    <FormControl
      size={size}
      margin={margin}
      fullWidth={fullWidth}
      data-testid={camelCase(`field ${name}`)}
      sx={sxFieldWrapper}
    >
      <Box sx={{ textAlign: align }} component="span">
        <Link
          color="primary"
          component="button"
          variant="body2"
          onClick={handleReset}
          disabled={disableReset}
        >
          {label}
        </Link>
      </Box>
    </FormControl>
  );
}

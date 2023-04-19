/**
 * @type: formElement
 * name: form.element.ClearSearch
 * chunkName: formElement
 */

import { FormFieldProps } from '@metafox/form/types';
import { Box, Link } from '@mui/material';
import { pick, isEqual, omit, omitBy } from 'lodash';
import React from 'react';
import qs from 'query-string';
import { useNavigate } from 'react-router-dom';
import { useFormSchema } from '@metafox/form';

export default function ClearSearchField({
  config: { excludeFields, label = 'Reset', align = 'right', ...restConfig },
  name,
  formik
}: FormFieldProps) {
  // this component only use sidebar search app. if want clear form please check ClearSearchForm element
  const navigate = useNavigate();
  const { value: valueDefault } = useFormSchema();
  const defaultRelatedValues = omit({ ...valueDefault }, excludeFields);
  const relatedValues = omit({ ...formik.values }, excludeFields);
  const keepParams = pick({ ...formik.values }, excludeFields);
  const disableReset = isEqual(
    defaultRelatedValues,
    omitBy(relatedValues, v => !v)
  );

  const handleReset = React.useCallback(() => {
    if (disableReset) return;

    navigate(
      {
        search: qs.stringify({
          ...defaultRelatedValues,
          ...keepParams
        })
      },
      { replace: true }
    );
    formik.resetForm();

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [defaultRelatedValues, disableReset, keepParams]);

  return (
    <Box sx={{ textAlign: align }} {...restConfig}>
      <Link
        color="primary"
        component="span"
        variant="body2"
        onClick={handleReset}
        disabled={disableReset}
      >
        {label}
      </Link>
    </Box>
  );
}

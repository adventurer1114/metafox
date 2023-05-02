/**
 * @type: formElement
 * name: form.element.DynamicTypo
 * chunkName: formBasic
 */

import { Typography } from '@mui/material';
import React from 'react';
import { FormFieldProps } from '@metafox/form';
import { camelCase } from 'lodash';
import { useField } from 'formik';

const TypoHtml = ({ config }: FormFieldProps & { config: { data: any[] } }) => {
  const {
    data = [],
    relatedField,
    testid,
    tagName = 'span',
    color = 'text.hint',
    variant = 'body2',
    ...restConfig
  } = config;

  const [field] = useField(relatedField ?? 'placement_id');

  const plainText = React.useMemo(() => {
    return data.find(item => item.value === field.value)?.description;
  }, [field.value, data]);

  if (!plainText) return null;

  return (
    <Typography
      data-testid={camelCase(`field ${testid}`)}
      variant={variant as any}
      color={color}
      component={tagName as any}
      {...restConfig}
    >
      <div dangerouslySetInnerHTML={{ __html: plainText }} />
    </Typography>
  );
};

export default TypoHtml;

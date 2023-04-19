/**
 * @type: formElement
 * name: form.element.Description
 * chunkName: formBasic
 */
import { FormControl, FormHelperText, Typography } from '@mui/material';
import { camelCase } from 'lodash';
import React from 'react';
import { FormFieldProps } from '@metafox/form';

const DescriptionField = ({ config, name, formik }: FormFieldProps) => {
  const {
    label,
    margin,
    disabled,
    fullWidth,
    hiddenLabel,
    required,
    variant,
    size,
    style,
    description,
    color
  } = config;

  return (
    <FormControl
      sx={{ display: 'block' }}
      margin={margin ?? 'normal'}
      disabled={disabled}
      fullWidth={fullWidth}
      hiddenLabel={hiddenLabel}
      required={required}
      style={style}
      size={size}
      variant={variant as any}
      data-testid={camelCase(`field ${name}`)}
    >
      <Typography component="legend" color={color}>
        {label}
      </Typography>
      <FormHelperText children={description} />
    </FormControl>
  );
};

export default DescriptionField;

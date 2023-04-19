/**
 * @type: formElement
 * name: form.element.Checkbox
 * chunkName: formBasic
 */

/* eslint-disable eqeqeq */

import { FormFieldProps } from '@metafox/form';
import { FormControl, FormHelperText, styled } from '@mui/material';
import MuiCheckbox from '@mui/material/Checkbox';
import FormControlLabel from '@mui/material/FormControlLabel';
import { useField } from 'formik';
import { camelCase } from 'lodash';
import React from 'react';
import ErrorMessage from './ErrorMessage';
import Label from './Label';

const StyledCheckBox = styled(MuiCheckbox, {
  name: 'MuiCheckbox',
  shouldForwardProp: (prop: string) => prop !== 'haveError'
})<{ haveError: boolean }>(({ theme, haveError }) => ({
  ...(haveError && {
    color: theme.palette.error.main
  })
}));

const CheckboxField = (props: FormFieldProps) => {
  const { config, disabled: forceDisabled, name, formik } = props;
  const [field, meta, helpers] = useField(name ?? 'CheckboxField');

  const {
    label,
    disabled,
    checkedValue = 1,
    uncheckedValue = 0,
    margin,
    size,
    fullWidth,
    color,
    description
  } = config;
  const haveError = Boolean(meta.error && (meta.touched || formik.submitCount));

  return (
    <FormControl
      margin={margin}
      fullWidth={fullWidth}
      data-testid={camelCase(`field ${name}`)}
    >
      <FormControlLabel
        disabled={disabled || forceDisabled || formik.isSubmitting}
        label={<Label text={label} />}
        checked={field.value == checkedValue}
        onChange={(_, checked) =>
          helpers.setValue(checked ? checkedValue : uncheckedValue)
        }
        control={
          <StyledCheckBox
            sx={{ color }}
            haveError={haveError}
            size={size}
            data-testid={camelCase(`input ${name}`)}
            inputProps={{ 'aria-label': label }}
          />
        }
      />
      {description ? <FormHelperText>{description}</FormHelperText> : null}
      {haveError ? <ErrorMessage error={meta.error} /> : null}
    </FormControl>
  );
};

export default CheckboxField;

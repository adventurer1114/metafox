/**
 * @type: formElement
 * name: form.element.Textarea
 * chunkName: formExtras
 */
import MuiTextField from '@mui/material/TextField';
import { useField } from 'formik';
import { camelCase } from 'lodash';
import { createElement } from 'react';
import { FormFieldProps } from '@metafox/form';

const TextAreaField = ({
  config,
  disabled: forceDisabled,
  name,
  formik
}: FormFieldProps) => {
  const [field, meta] = useField(name ?? 'TextField');
  const {
    label,
    disabled,
    labelProps,
    placeholder,
    variant,
    margin = 'normal',
    fullWidth,
    type = 'text',
    rows = 5,
    description,
    autoFocus,
    required,
    maxLength
  } = config;

  // fix: A component is changing an uncontrolled input
  if (!field.value) {
    field.value = config.defaultValue ?? '';
  }

  const haveError = Boolean(meta.error && (meta.touched || formik.submitCount));

  return createElement(MuiTextField, {
    ...field,
    required,
    multiline: true,
    disabled: disabled || forceDisabled || formik.isSubmitting,
    variant,
    label,
    'data-testid': camelCase(`field ${name}`),
    autoFocus,
    inputProps: { 'data-testid': camelCase(`input ${name}`), maxLength },
    rows,
    InputLabelProps: labelProps,
    placeholder,
    margin,
    error: haveError ? meta.error : false,
    fullWidth,
    type,
    helperText: haveError ? meta.error : description
  });
};

export default TextAreaField;

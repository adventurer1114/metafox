/**
 * @type: formElement
 * name: form.element.Text
 * chunkName: formBasic
 */
import { FormFieldProps } from '@metafox/form';
import {
  FormControl,
  InputAdornment,
  styled,
  TextField,
  TextFieldProps,
  Typography
} from '@mui/material';
import { useField } from 'formik';
import { camelCase, isString } from 'lodash';
import Description from './Description';
import ErrorTooltip from './ErrorTooltip';
import Warning from './Warning';
import React from 'react';
import { toFindReplaceSlugify } from './utils';

const Title = styled(Typography, {
  name: 'Title',
  shouldForwardProp: prop => prop !== 'styleGroup'
})<{ styleGroup?: string }>(({ theme, styleGroup }) => ({
  ...(styleGroup === 'question' && {
    color: theme.palette.text.secondary,
    fontWeight: theme.typography.fontWeightBold
  }),
  ...(styleGroup === 'normal' && {})
}));

const TextFormField = ({
  config,
  disabled: forceDisabled,
  required: forceRequired,
  name,
  formik
}: FormFieldProps<TextFieldProps>) => {
  const [field, meta, { setValue }] = useField(name ?? 'TextField');

  const {
    label,
    disabled,
    autoComplete,
    placeholder,
    noFeedback,
    variant,
    margin,
    fullWidth,
    type = 'text',
    rows,
    size,
    required,
    multiline,
    description,
    autoFocus,
    readOnly,
    maxLength,
    hasFormLabel = false,
    showErrorTooltip = false,
    sx,
    sxFieldWrapper,
    hasFormOrder = false,
    order,
    styleGroup = 'normal',
    preventScrolling = false,
    startAdornment,
    endAdornment,
    minNumber,
    maxNumber,
    warning,
    hoverState,
    returnKeyType,
    alwayShowDescription = true,
    contextualDescription,
    defaultValue,
    component, // fix React warning.
    testid,
    showWhen,
    requiredWhen,
    enabledWhen,
    findReplace,
    ...rest
  } = config;

  let haveError: boolean = !!(
    meta.error &&
    (meta.touched || formik.submitCount)
  );

  if (autoComplete && autoFocus) {
    haveError = haveError && field.value !== undefined;
  }

  React.useEffect(() => {
    if (field.value === undefined && formik.submitCount) {
      setValue('');
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [formik.submitCount]);

  const handleBlur = e => {
    isString(field.value) && setValue(field.value.trim());
    field.onBlur(e);
  };

  const showError = !showErrorTooltip && haveError && meta.error;

  let helperText = null;

  if (description) {
    helperText = <Description text={description} />;
  }

  if (showError) {
    helperText = meta.error;
  }

  if (description && alwayShowDescription && showError) {
    helperText = (
      <>
        <Description text={description} />
        <Description text={meta.error} error />
      </>
    );
  }

  if (noFeedback) {
    helperText = null;
  }

  const orderLabel = hasFormOrder && order ? `${order}. ` : null;

  const rangeNumber =
    type === 'number' ? { min: minNumber, max: maxNumber } : {};

  const suffixDescription = findReplace
    ? toFindReplaceSlugify(field.value, findReplace?.find, findReplace?.replace)
    : field.value;

  React.useEffect(() => {
    if (findReplace) {
      setValue(suffixDescription);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [findReplace, suffixDescription]);

  return (
    <FormControl
      margin={margin}
      variant={variant}
      fullWidth={fullWidth}
      data-testid={camelCase(`field ${name}`)}
      sx={sxFieldWrapper}
      id={name}
    >
      {hasFormLabel && (
        <Title sx={{ mb: 2 }} variant={variant} styleGroup={styleGroup}>
          {orderLabel}
          {label}
        </Title>
      )}
      {contextualDescription && (
        <Description text={`${contextualDescription}${suffixDescription}`} />
      )}
      <ErrorTooltip name={field.name} showErrorTooltip={showErrorTooltip}>
        <TextField
          {...rest}
          value={autoComplete && autoFocus ? undefined : field.value ?? ''}
          name={field.name}
          onChange={field.onChange}
          onWheel={e => preventScrolling && e.target?.blur()}
          error={haveError}
          multiline={!!(rows && multiline)}
          // flagDisabled={disabled || forceDisabled || formik.isSubmitting}
          disabled={disabled || forceDisabled || formik.isSubmitting}
          required={required || forceRequired}
          size={size}
          onBlur={handleBlur}
          InputProps={{
            startAdornment: startAdornment ? (
              <InputAdornment position="start">{startAdornment}</InputAdornment>
            ) : null,
            endAdornment: endAdornment ? (
              <InputAdornment position="end">{endAdornment}</InputAdornment>
            ) : null
          }}
          inputProps={{
            ...rangeNumber,
            readOnly,
            autoFocus,
            autoComplete,
            maxLength,
            'data-testid': camelCase(`input ${name}`)
          }}
          label={!hasFormLabel ? label : undefined}
          rows={rows}
          placeholder={placeholder ?? label}
          type={type}
          defaultValue={field.value ?? defaultValue}
          helperText={helperText}
          variant={variant}
          sx={sx}
        />
      </ErrorTooltip>
      <Warning warning={warning} />
    </FormControl>
  );
};

export default TextFormField;

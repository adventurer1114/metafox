/**
 * @type: formElement
 * name: form.element.Date
 * chunkName: datePicker
 */

import { FormFieldProps } from '@metafox/form';
import { FormControl, TextField } from '@mui/material';
import { DatePicker, LocalizationProvider } from '@mui/x-date-pickers';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';
import { endOfDay, startOfDay } from 'date-fns';
import { useField } from 'formik';
import { camelCase } from 'lodash';
import moment from 'moment';
import React from 'react';
import ErrorMessage from '../ErrorMessage';
import useStyles from './styles';

function DatePickerField({
  config,
  name,
  disabled: forceDisabled,
  formik
}: FormFieldProps) {
  const {
    label,
    component,
    variant,
    disabled,
    pickerVariant,
    autoComplete = 'off',
    placeholder,
    startOfDay: _start,
    endOfDay: _end,
    minDate,
    maxDate,
    sxFieldWrapper,
    size,
    ...restConfig
  } = config;

  const classes = useStyles();

  const [field, meta, { setValue, setTouched }] = useField(name ?? 'date');
  const haveError = Boolean(meta.error && (meta.touched || formik.submitCount));

  const handleDateChange = (date: Date, value: string) => {
    setTouched(true);

    const isValidDate = moment(date).isValid();

    if (isValidDate) {
      if (date && _start) {
        date = startOfDay(date);
      } else if (date && _end) {
        date = endOfDay(date);
      }

      setValue(date ? date.toISOString() : undefined);
    } else {
      setValue(null);
    }
  };

  return (
    <FormControl
      margin="dense"
      className={classes.root}
      data-testid={camelCase(`button ${name}`)}
      sx={sxFieldWrapper}
      size={size}
    >
      <LocalizationProvider dateAdapter={AdapterDateFns}>
        <DatePicker
          value={field.value ?? null}
          onChange={handleDateChange}
          label={label}
          minDate={minDate ? new Date(minDate) : null}
          maxDate={maxDate ? new Date(maxDate) : null}
          disabled={disabled || forceDisabled || formik.isSubmitting}
          renderInput={params => (
            <TextField
              {...params}
              autoComplete={autoComplete}
              data-testid={camelCase(`input ${name}`)}
              size={size}
            />
          )}
          {...restConfig}
        />
      </LocalizationProvider>
      {haveError ? <ErrorMessage error={meta.error} /> : null}
    </FormControl>
  );
}

export default DatePickerField;

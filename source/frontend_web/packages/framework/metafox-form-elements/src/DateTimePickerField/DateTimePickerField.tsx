/**
 * @type: formElement
 * name: form.element.Datetime
 * chunkName: datePicker
 */
import { Box, styled, TextField } from '@mui/material';
import { useField } from 'formik';
import { camelCase } from 'lodash';
import moment from 'moment';
import React from 'react';
import { FormFieldProps } from '@metafox/form';
import ErrorMessage from '../ErrorMessage';
import TimeSuggestionPicker from './TimeSuggestionPicker';
import {
  LocalizationProvider,
  DatePicker,
  TimePicker
} from '@mui/x-date-pickers';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';

const Root = styled('div', { name: 'DateTimePickerField' })(({ theme }) => ({
  padding: theme.spacing(2, 0, 1)
}));

const isSameDay = (start: Date, end: Date) => {
  if (!moment(start).isValid() || !moment(end).isValid()) return false;

  const startMoment = moment([
    start.getFullYear(),
    start.getMonth(),
    start.getDate()
  ]);
  const endMoment = moment([end.getFullYear(), end.getMonth(), end.getDate()]);

  return !startMoment.diff(endMoment);
};

function DateTimePickerField({
  config,
  name,
  formik,
  disabled: forceDisabled
}: FormFieldProps) {
  const {
    label,
    variant,
    margin,
    pickerVariant,
    disabled,
    component,
    value,
    required,
    inputFormat,
    formatValue,
    minDateTime,
    maxDateTime,
    timeSuggestion,
    labelDatePicker,
    labelTimePicker,
    nullable,
    ...restConfig
  } = config;
  const [field, meta, { setValue, setTouched }] = useField(name ?? 'datetime');
  const [selectedDate, setDate] = React.useState(
    nullable && !field.value ? null : new Date(field.value)
  );
  const [selectedTime, setTime] = React.useState(
    nullable && !field.value ? null : new Date(field.value)
  );

  const handleDateChange = (date: Date) => {
    const isValidDate = moment(date).isValid();

    setDate(isValidDate ? new Date(date.toDateString()) : date);
  };

  const handleTimeChange = (time: Date) => {
    setTime(time);
  };

  React.useEffect(() => {
    setTouched(true);
    const isValid =
      moment(selectedDate).isValid() && moment(selectedTime).isValid();

    if (!isValid) {
      setValue(null);

      return;
    }

    const hour = moment(selectedTime).get('hour') * 60 * 60 * 1000;
    const minute = moment(selectedTime).get('minute') * 60 * 1000;
    const selectedDateStart = moment(selectedDate).startOf('day');
    const newDateTime = new Date(selectedDateStart.valueOf() + hour + minute);

    const initialMoment = moment(formik.initialValues[name]);
    const initSecond = initialMoment.get('seconds');
    const initMilliseconds = initialMoment.get('milliseconds');

    initialMoment.subtract(initSecond, 'seconds');
    initialMoment.subtract(initMilliseconds, 'milliseconds');

    if (initialMoment.toISOString() === newDateTime.toISOString()) return;

    setValue(new Date(newDateTime));
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [selectedDate, selectedTime]);

  const haveError = Boolean(meta.error && (meta.touched || formik.submitCount));

  const hasMinTime = isSameDay(
    selectedDate,
    new Date(new Date(minDateTime).toLocaleDateString())
  );
  const hasMaxTime = isSameDay(
    selectedDate,
    new Date(new Date(maxDateTime).toLocaleDateString())
  );

  return (
    <Root data-testid={camelCase(`field ${name}`)}>
      <LocalizationProvider dateAdapter={AdapterDateFns}>
        <Box sx={{ display: 'flex' }}>
          <Box>
            <DatePicker
              value={selectedDate}
              onChange={handleDateChange}
              label={labelDatePicker || label}
              minDate={minDateTime ? new Date(minDateTime) : null}
              maxDate={maxDateTime ? new Date(maxDateTime) : null}
              disabled={disabled || forceDisabled || formik.isSubmitting}
              renderInput={params => (
                <TextField
                  {...params}
                  required={required}
                  autoComplete="off"
                  data-testid={camelCase(`input ${name}`)}
                />
              )}
              {...restConfig}
            />
          </Box>
          <Box sx={{ paddingLeft: 2 }}>
            {timeSuggestion ? (
              <TimeSuggestionPicker
                {...config}
                hasMinTime={hasMinTime}
                hasMaxTime={hasMaxTime}
                value={selectedTime}
                handleChange={handleTimeChange}
                disabled={disabled || forceDisabled || formik.isSubmitting}
              />
            ) : (
              <TimePicker
                {...restConfig}
                ampmInClock
                minTime={hasMinTime ? new Date(minDateTime) : null}
                maxTime={hasMaxTime ? new Date(maxDateTime) : null}
                mask="__:__"
                minutesStep={1}
                disabled={disabled || forceDisabled || formik.isSubmitting}
                label={labelTimePicker || label}
                value={selectedTime}
                onChange={handleTimeChange}
                renderInput={params => (
                  <TextField
                    {...params}
                    required={required}
                    autoComplete="off"
                  />
                )}
              />
            )}
          </Box>
        </Box>
      </LocalizationProvider>
      {haveError ? <ErrorMessage error={meta.error} /> : null}
    </Root>
  );
}

export default DateTimePickerField;

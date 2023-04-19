import { TextField, Autocomplete, Box, TextFieldProps } from '@mui/material';
import moment from 'moment';
import React from 'react';
import AccessTimeIcon from '@mui/icons-material/AccessTime';
import { FormElementShape } from '@metafox/form';

const rangeTime = 15;

const generateTimePickerList = (timeStartDefault = 0, maxTime) => {
  const max = maxTime ? moment.duration(maxTime).asMinutes() : 24 * 60;
  const x = rangeTime; // minutes interval
  const times = []; // time array
  let tt = timeStartDefault; // start time

  // loop to increment the time and push results in array
  for (let i = 0; tt < max; i++) {
    const hh = Math.floor(tt / 60); // getting hours of day in 0-24 format
    const mm = tt % 60; // getting minutes of the hour in 0-55 format

    times[i] = {
      label: `${`0${hh}`.slice(-2)}:${`0${mm}`.slice(-2)}`,
      value: `${`0${hh}`.slice(-2)}:${`0${mm}`.slice(-2)}`
    }; // pushing data in array in [00:00 - 12:00 AM/PM format]
    tt = tt + x;
  }

  return times;
};

const validateManualTime = (value: string) => {
  return moment(value, ['HH:mm', 'H:mm'], true).isValid();
};

const convertManualTime = (value: string) => {
  return moment(value, ['HH:mm', 'H:mm']).format('HH:mm');
};

const getMinutesStart = (time: Date) => {
  if (!time) return 0;

  const hour = moment(time).get('hour') * 60;
  const minute = moment(time).get('minute');
  const minuteQuarter = (hour + minute) / rangeTime;
  const ceilQuarter =
    minuteQuarter % 1 === 0 ? minuteQuarter + 1 : Math.ceil(minuteQuarter);

  return ceilQuarter * rangeTime;
};

const getHHmmStart = (time: Date) => {
  const start = moment(time);
  const remainder = rangeTime - (start.minute() % rangeTime);

  return moment(start).add(remainder, 'minutes').format('HH:mm');
};
export interface Props extends FormElementShape {}

function TimeSuggestionField(props: Props) {
  const {
    label,
    minDateTime,
    maxDateTime,
    hasMinTime,
    hasMaxTime,
    handleChange,
    value,
    disabled,
    labelTimePicker
  } = props;

  const [valueTimePicker, setValueTimePicker] = React.useState(
    moment(value).format('HH:mm')
  );
  const minMinuteTimeStart = hasMinTime ? getMinutesStart(minDateTime) : 0;

  const optionsTimePicker = generateTimePickerList(
    minMinuteTimeStart,
    hasMaxTime ? maxDateTime : null
  );

  React.useEffect(() => {
    if (value && moment(value).isValid()) return;

    const valueDefault = minDateTime
      ? getHHmmStart(minDateTime)
      : moment().format('HH:mm');
    setValueTimePicker(valueDefault);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  React.useEffect(() => {
    if (!valueTimePicker) return;

    const convertValue = moment(valueTimePicker, 'HH:mm').toDate();
    handleChange(convertValue);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [valueTimePicker]);

  const handleTimePickerChange = (event, newValue) => {
    setValueTimePicker(newValue.value);
  };

  const handleBlurPickerTime = e => {
    const { value } = e.target;

    if (value !== valueTimePicker) {
      setValueTimePicker(
        validateManualTime(value) ? convertManualTime(value) : valueTimePicker
      );
    }
  };

  const renderInputTimePicker = React.useCallback(
    (params: JSX.IntrinsicAttributes & TextFieldProps) => {
      const min = moment(minDateTime).format('HH:mm');
      const max = moment(maxDateTime).format('HH:mm');
      const pickerTime = valueTimePicker
        ? moment(valueTimePicker, 'HH:mm')
        : null;

      const minTime = hasMinTime ? moment(min, 'HH:mm') : null;
      const maxTime = hasMaxTime ? moment(max, 'HH:mm') : null;
      const validMinTime =
        pickerTime && minTime ? pickerTime.isSameOrAfter(minTime) : true;
      const validMaxTime =
        pickerTime && maxTime ? pickerTime.isSameOrBefore(maxTime) : true;
      const valid = validMinTime && validMaxTime;

      return (
        <TextField
          {...params}
          error={!valid}
          onBlur={handleBlurPickerTime}
          label={labelTimePicker || label}
        />
      );
    },
    [valueTimePicker]
  );

  return (
    <Box
      sx={{
        '& .MuiAutocomplete-noOptions': {
          display: 'none !important'
        }
      }}
    >
      <Autocomplete
        sx={{
          minWidth: '100px',
          '& .MuiAutocomplete-popupIndicator': {
            transform: 'none !important'
          }
        }}
        disabled={disabled}
        value={valueTimePicker}
        disablePortal
        id="time-suggestion-picker"
        options={optionsTimePicker}
        onChange={handleTimePickerChange}
        disableClearable
        renderInput={renderInputTimePicker}
        popupIcon={<AccessTimeIcon />}
        forcePopupIcon
        ListboxProps={{ style: { maxHeight: '150px' } }}
      />
    </Box>
  );
}

export default TimeSuggestionField;

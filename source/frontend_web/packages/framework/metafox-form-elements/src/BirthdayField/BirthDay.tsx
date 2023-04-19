import { FormFieldProps } from '@metafox/form';
import { TextField } from '@mui/material';
import { DatePicker, LocalizationProvider } from '@mui/x-date-pickers';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';
import { useField } from 'formik';
import { camelCase } from 'lodash';
import moment from 'moment';
import React from 'react';
import useStyles from './styles';

function BirthDay({ config, name, formik }: FormFieldProps) {
  const {
    label,
    variant,
    margin,
    pickerVariant,
    component,
    value,
    required,
    minDate,
    maxDate,
    ...restConfig
  } = config;

  const classes = useStyles();
  const [field, meta, { setValue, setTouched }] = useField(name ?? 'birthday');
  const [selectedDate, setDate] = React.useState(
    field.value ? moment(field.value).toDate() : null
  );

  const handleDateChange = (date: any, value: string) => {
    setDate(date);
  };

  const handleInputBlur = () => {
    setTouched(true);
  };

  React.useEffect(() => {
    const isValid = moment(selectedDate).isValid();

    if (!isValid) {
      setValue(selectedDate);

      return;
    }

    const newDateTime = moment(selectedDate).format('YYYY-MM-DD');

    setValue(newDateTime);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [selectedDate]);

  const haveError = Boolean(meta.error && (meta.touched || formik.submitCount));

  return (
    <div className={classes.root} data-testid={camelCase(`field ${name}`)}>
      <LocalizationProvider dateAdapter={AdapterDateFns}>
        <DatePicker
          value={selectedDate}
          inputFormat="yyyy-MM-dd"
          onChange={handleDateChange}
          label={label}
          minDate={minDate ? new Date(minDate) : null}
          maxDate={maxDate ? new Date(maxDate) : null}
          renderInput={params => (
            <TextField
              {...params}
              data-testid={camelCase(`input ${name}`)}
              required={required}
              error={haveError}
              helperText={haveError ? meta.error : null}
              onBlur={handleInputBlur}
            />
          )}
          {...restConfig}
        />
      </LocalizationProvider>
    </div>
  );
}

export default BirthDay;

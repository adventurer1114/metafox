/**
 * @type: formElement
 * name: form.element.CountryCityCode
 * chunkName: formExtras
 */
import { FormFieldProps } from '@metafox/form';
import { useGlobal, useSuggestions } from '@metafox/framework';
import {
  Autocomplete,
  Box,
  CircularProgress,
  FormControl,
  FormHelperText,
  TextField
} from '@mui/material';
import { useField, useFormikContext } from 'formik';
import { camelCase } from 'lodash';
import React from 'react';

type ItemShape = {
  label: string;
  value: string;
};

const CountryCityCodeField = ({
  config,
  name,
  disabled: forceDisabled,
  formik
}: FormFieldProps) => {
  const {
    variant = 'outlined',
    label,
    size = 'medium',
    margin = 'normal',
    disabled,
    placeholder,
    required,
    fullWidth = true,
    search_endpoint,
    search_params
  } = config;
  const { values } = useFormikContext();
  const { compactData } = useGlobal();
  const [field, meta, { setValue, setTouched }] = useField(
    name || 'autoComplete'
  );
  const [optionValue, setOptionValue] = React.useState(null);

  const haveError: boolean = !!(
    meta.error &&
    (meta.touched || formik.submitCount)
  );

  const filterParams = React.useMemo(() => {
    return compactData(search_params, values);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [search_params, values]);

  const [data, handleChanged] = useSuggestions<ItemShape>({
    apiUrl: search_endpoint,
    initialParams: filterParams
  });

  React.useEffect(() => {
    if (!field.value) {
      setOptionValue(null);

      return;
    }

    // set init optionValue when loading data api url
    if (typeof field.value === 'object' && !Array.isArray(field.value)) {
      setOptionValue(field.value);
    }

    if (data.items.length) {
      const valueField = data.items.filter(i => i.value == field.value)[0];
      setOptionValue(valueField);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [field.value, data.items]);

  return (
    <FormControl
      variant={variant as any}
      margin={margin}
      fullWidth={fullWidth}
      required={required}
      size={size}
      data-testid={camelCase(`field ${name}`)}
      error={haveError}
    >
      <Autocomplete
        autoHighlight
        openOnFocus
        onBlur={field.onBlur}
        onInputChange={(evt, values) => {
          handleChanged(values);
        }}
        onChange={(evt, newValue) => {
          setValue(newValue || null);
          setOptionValue(newValue);
          setTouched(true);
        }}
        disabled={disabled || forceDisabled || formik.isSubmitting}
        value={optionValue || field.value}
        placeholder={placeholder}
        getOptionLabel={option => option.label}
        options={data.items}
        loading={data.loading}
        loadingText={
          <Box sx={{ display: 'flex', justifyContent: 'center' }}>
            <CircularProgress />
          </Box>
        }
        renderInput={params => (
          <TextField
            onClick={() => {
              handleChanged('');
            }}
            onFocus={() => {
              handleChanged('');
            }}
            {...params}
            label={label}
            data-testid={camelCase(`field ${name}`)}
            error={haveError}
            InputProps={{
              ...params.InputProps
            }}
          />
        )}
      />
      {haveError && <FormHelperText>{meta?.error}</FormHelperText>}
    </FormControl>
  );
};

export default CountryCityCodeField;

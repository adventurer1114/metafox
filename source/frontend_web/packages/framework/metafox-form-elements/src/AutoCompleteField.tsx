/**
 * @type: formElement
 * name: form.element.Autocomplete
 * chunkName: formExtras
 */
import { FormFieldProps } from '@metafox/form';
import { useGlobal, useSuggestions } from '@metafox/framework';
import { TruncateText } from '@metafox/ui';
import {
  Autocomplete,
  Box,
  CircularProgress,
  FormControl,
  TextField,
  FormHelperText
} from '@mui/material';
import { useField, useFormikContext } from 'formik';
import { camelCase } from 'lodash';
import React from 'react';

type ItemShape = {
  label: string;
  value: string;
  caption?: string;
};

const renderOption = (props, option: ItemShape) => {
  return (
    <li {...props}>
      <Box sx={{ overflow: 'hidden' }}>
        <TruncateText lines={1}>{option.label}</TruncateText>
        {option.caption ? (
          <Box>
            <TruncateText
              lines={1}
              color="text.secondary"
              variant="body2"
              fontSize={12}
              component="span"
            >
              {option.caption}
            </TruncateText>
          </Box>
        ) : null}
      </Box>
    </li>
  );
};

const AutoCompleteField = ({
  config,
  name,
  disabled: forceDisabled,
  formik
}: FormFieldProps) => {
  const {
    variant = 'outlined',
    label,
    size = 'medium',
    fieldValue = 'value',
    margin = 'normal',
    disabled,
    placeholder,
    required,
    fullWidth = true,
    search_endpoint,
    search_params
  } = config;
  const { values } = useFormikContext();
  const { compactData, i18n } = useGlobal();
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
    setValue(field.value ? field.value[fieldValue] : null);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

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

  const handleBlur = e => {
    field.onBlur(e);
    setTouched(true);
  };

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
        onBlur={handleBlur}
        onInputChange={(evt, values) => {
          handleChanged(values);
        }}
        onChange={(evt, newValue) => {
          setValue(newValue?.[fieldValue] || null);
          setOptionValue(newValue);
        }}
        noOptionsText={i18n.formatMessage({ id: 'no_options' })}
        disabled={disabled || forceDisabled || formik.isSubmitting}
        value={optionValue}
        placeholder={placeholder}
        getOptionLabel={option => option?.label}
        options={data.items}
        loading={data.loading}
        renderOption={renderOption}
        loadingText={
          <Box sx={{ display: 'flex', justifyContent: 'center' }}>
            <CircularProgress />
          </Box>
        }
        renderInput={params => (
          <TextField
            onFocus={() => handleChanged('')}
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

export default AutoCompleteField;

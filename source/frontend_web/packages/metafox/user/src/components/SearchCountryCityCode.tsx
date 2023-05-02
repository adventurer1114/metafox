/**
 * @type: formElement
 * name: form.element.SearchCountryCityCode
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
import { camelCase, get, omit, isObject } from 'lodash';
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
  const clickedRef = React.useRef(false);
  const haveError: boolean = !!(
    meta.error &&
    (meta.touched || formik.submitCount)
  );

  const filterParams = React.useMemo(() => {
    const data = isObject(values) ? values : {};

    return compactData(search_params, omit(data, ['q']));
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [search_params, values]);

  const [data, handleChanged] = useSuggestions<ItemShape>({
    apiUrl: search_endpoint,
    initialParams: filterParams
  });

  const renderOption = React.useCallback(
    (props: Record<string, any>, option: ItemShape) => {
      // add key value because fix for case duplicate same name label on options

      return (
        <Box component="li" {...props} key={`${option?.value}${option?.label}`}>
          {option.label}
        </Box>
      );
    },
    []
  );

  React.useEffect(() => {
    handleChanged('');
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [filterParams]);
  const isOptionEqualToValue = (
    option: ItemShape,
    value: ItemShape
  ): boolean => {
    // eslint-disable-next-line eqeqeq
    return get(option, 'value') == get(value, 'value');
  };

  const isLoading = data?.loading && field.value;
  const dataSelected =
    field.value && !data?.loading
      ? // eslint-disable-next-line eqeqeq
        data.items.find(i => i.value == field.value)
      : '';

  React.useEffect(() => {
    if (!data?.loading && field?.value && !dataSelected) {
      setValue('');
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data?.loading, field?.value, dataSelected]);

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
        onChange={(evt, newValue) => {
          clickedRef.current = true;
          setValue(newValue?.value);

          setTouched(true);
        }}
        renderOption={renderOption}
        disabled={disabled || forceDisabled || formik.isSubmitting || isLoading}
        value={dataSelected || ''}
        placeholder={placeholder}
        getOptionLabel={option => option.label || ''}
        isOptionEqualToValue={isOptionEqualToValue}
        options={data.items}
        loading={data.loading}
        loadingText={
          <Box sx={{ display: 'flex', justifyContent: 'center' }}>
            <CircularProgress />
          </Box>
        }
        renderInput={params => (
          <TextField
            {...params}
            label={label}
            data-testid={camelCase(`field ${name}`)}
            error={haveError}
            InputProps={{
              ...params.InputProps,
              endAdornment: (
                <>
                  {isLoading ? (
                    <CircularProgress color="inherit" size={20} />
                  ) : null}
                  {params.InputProps.endAdornment}
                </>
              )
            }}
          />
        )}
      />
      {haveError && <FormHelperText>{meta?.error}</FormHelperText>}
    </FormControl>
  );
};

export default CountryCityCodeField;

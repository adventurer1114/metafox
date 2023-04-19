/**
 * @type: formElement
 * name: form.element.Tags
 * chunkName: formExtras
 */
import { FormFieldProps } from '@metafox/form';
import { useSuggestions } from '@metafox/framework';
import {
  Autocomplete,
  ChipProps,
  FormControl,
  FormHelperText,
  TextField
} from '@mui/material';
import { useField } from 'formik';
import { camelCase, isArray } from 'lodash';
import React from 'react';

type ItemShape = string;

const chipProps: ChipProps = { size: 'small', variant: 'outlined' };

const formatInputData = tagsArray => {
  const notEmptyTagFilter = tagsArray.filter(item => item.trim().length > 0);

  return notEmptyTagFilter;
};

function TagsField({
  config,
  name,
  disabled: forceDisabled,
  formik
}: FormFieldProps) {
  const {
    variant = 'outlined',
    label,
    size = 'medium',
    margin = 'normal',
    disabled,
    placeholder,
    description,
    required,
    fullWidth = true,
    search_endpoint,
    disableSuggestion = false
  } = config;
  const [field, meta, { setValue }] = useField(name ?? 'tags');
  const haveError: boolean = !!(
    meta.error &&
    (meta.touched || formik.submitCount)
  );
  const refText = React.useRef<HTMLInputElement>();

  const defaultValue: ItemShape[] = React.useMemo(() => {
    return field.value && isArray(field.value) ? field.value : [];
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const handleChange = (event, newValue) => {
    event.preventDefault();

    if (event.type === 'blur' && refText.current.value === '') return;

    setValue(formatInputData(newValue));
  };

  const [data, handleChanged] = useSuggestions<ItemShape>({
    apiUrl: search_endpoint
  });

  return (
    <FormControl
      variant={variant as any}
      margin={margin}
      fullWidth={fullWidth}
      size={size}
      data-testid={camelCase(`field ${name}`)}
    >
      <Autocomplete<string, true, boolean, boolean>
        multiple
        freeSolo
        autoSelect
        value={field.value}
        selectOnFocus={false}
        filterSelectedOptions
        onBlur={field.onBlur}
        id={`tags-${name}`}
        options={disableSuggestion ? [] : data.items}
        ChipProps={chipProps}
        disabled={disabled || forceDisabled || formik.isSubmitting}
        onInputChange={(evt, values) => {
          !disableSuggestion && handleChanged(values);
        }}
        defaultValue={defaultValue}
        onChange={(event, newValue) => handleChange(event, newValue)}
        renderInput={params => (
          <TextField
            {...params}
            inputRef={refText}
            required={required}
            label={label}
            placeholder={placeholder}
            inputProps={{
              ...params.inputProps,
              'data-testid': camelCase(`input ${name}`)
            }}
            error={haveError}
            helperText={meta.error}
          />
        )}
      />
      {description ? <FormHelperText>{description}</FormHelperText> : null}
    </FormControl>
  );
}

export default TagsField;

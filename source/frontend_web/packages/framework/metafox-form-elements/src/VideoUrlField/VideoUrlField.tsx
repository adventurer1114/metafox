/**
 * @type: formElement
 * name: form.element.VideoUrl
 */
import { useGlobal } from '@metafox/framework';
import { FormFieldProps } from '@metafox/form';
import { TextField as MuiTextField } from '@mui/material';
import { useField } from 'formik';
import { camelCase, debounce } from 'lodash';
import React, { createElement } from 'react';
import Description from '../Description';
import Label from '../Label';

const VideoUrlField = ({
  config,
  disabled: forceDisabled,
  name,
  formik
}: FormFieldProps) => {
  const [field, meta, { setValue }] = useField(name ?? 'VideoUrlField');
  const { apiClient, compactData } = useGlobal();

  const {
    label,
    hint,
    autoComplete,
    placeholder,
    noFeedback,
    variant,
    disabled,
    margin,
    fullWidth,
    type = 'text',
    rows,
    size,
    required,
    description,
    autoFocus,
    readOnly,
    maxLength,
    autoFillValueFromLink = {
      title: ':title',
      text: ':description'
    }
  } = config;

  const query = React.useRef<string>('');
  const [valueAutoFill, setAuto] = React.useState<Record<string, any>>({});
  const [loading, setLoading] = React.useState<boolean>(false);

  React.useEffect(() => {
    Object.keys(valueAutoFill).forEach(key =>
      formik.setFieldValue(key, valueAutoFill[key])
    );
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [valueAutoFill]);

  const debounceAutoFill = React.useMemo(() => {
    const Search = () => {
      if (query.current === '') {
        return;
      }

      setLoading(true);
      apiClient
        .request({
          url: '/link/fetch',
          method: 'post',
          data: { link: query.current }
        })
        .then(res => {
          const response = res.data?.data;
          setAuto(compactData(autoFillValueFromLink, response));
        })
        .finally(() => {
          setLoading(false);
        });
    };

    return debounce(Search, 1000);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [apiClient, compactData, loading, setLoading]);

  // fix: A component is changing an uncontrolled input
  if (!field.value) {
    field.value = config.defaultValue ?? '';
  }

  // how to handle changed

  const handleChange = evt => {
    setValue(evt.target.value);
    query.current = evt.target.value;
    debounceAutoFill();
  };

  const haveError = Boolean(meta.error && (meta.touched || formik.submitCount));

  return createElement(MuiTextField, {
    ...field,
    disabled: disabled || forceDisabled || formik.isSubmitting || loading,
    required,
    label: <Label hint={hint} text={label} />,
    autoFocus,
    variant,
    size,
    'data-testid': camelCase(`field ${name}`),
    inputProps: {
      'data-testid': camelCase(`input ${name}`),
      maxLength,
      autoComplete,
      readOnly
    },
    rows,
    placeholder,
    onChange: handleChange,
    margin,
    error: haveError,
    fullWidth,
    type,
    helperText: noFeedback ? null : haveError ? (
      meta.error
    ) : description ? (
      <Description text={description} hint={hint} />
    ) : null
  });
};

export default VideoUrlField;

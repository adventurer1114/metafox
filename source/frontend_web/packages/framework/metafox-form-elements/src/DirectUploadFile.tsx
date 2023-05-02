/**
 * @type: formElement
 * name: form.element.DirectUploadFile
 * chunkName: formBasic
 */
import { FormFieldProps } from '@metafox/form';
import { useGlobal } from '@metafox/framework';
import { LineIcon, Image } from '@metafox/ui';
import {
  Box,
  Button,
  FormControl,
  Tooltip,
  styled,
  Skeleton
} from '@mui/material';
import { useField } from 'formik';
import { camelCase, get } from 'lodash';
import React from 'react';

const fixInputStyle: React.CSSProperties = {
  width: 2,
  right: 0,
  position: 'absolute',
  opacity: 0
};

const Preview = styled('div', { name: 'Preview' })(({ theme }) => ({
  marginTop: theme.spacing(1),
  borderRadius: theme.spacing(0.5),
  width: 200,
  maxWidth: 200,
  overflow: 'hidden',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  position: 'relative',
  '& button': {
    '& + button': {
      marginLeft: theme.spacing(1)
    }
  }
}));

const RemoveBtn = styled('div', {
  name: 'RemoveBtn',
  slot: 'removeBtn'
})(({ theme }) => ({
  width: theme.spacing(3),
  height: theme.spacing(3),
  borderRadius: theme.spacing(1.5),
  backgroundColor: 'rgba(0,0,0,0.89)',
  color: '#fff',
  position: 'absolute',
  top: theme.spacing(2),
  right: theme.spacing(2),
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  cursor: 'pointer'
}));

function DirectUploadFile({
  config,
  name,
  disabled: forceDisabled,
  formik
}: FormFieldProps) {
  const { i18n, apiClient } = useGlobal();
  const [field, , { setValue }] = useField(name);
  const [loading, setLoading] = React.useState(false);
  const inputRef = React.useRef<HTMLInputElement>();

  const handleFileChange = () => {
    setLoading(true);
    const file = inputRef.current.files.item(0);

    const formData = new FormData();

    formData.append('file', file);

    const type = 'photo';
    formik.setSubmitting(true);

    formData.append('type', type);
    formData.append('name', 'file');
    formData.append('item_type', type);
    formData.append('file_type', type);
    formData.append('file_name', file.name);
    formData.append('file_size', file.size.toString());

    apiClient
      .post('/file', formData)
      .then(response => get(response, 'data.data'))
      .then(data => {
        setValue(data.url);
        formik.setSubmitting(false);
        setLoading(false);
      })

      .catch(err => {
        //
      });
  };

  const handleControlClick = () => {
    inputRef.current.click();
  };

  const handleDeletePhoto = () => {
    setValue('');
  };

  return (
    <>
      <FormControl>
        <Button
          size="small"
          color="primary"
          variant="outlined"
          onClick={handleControlClick}
          startIcon={<LineIcon icon="ico-photo-plus-o" />}
        >
          {i18n.formatMessage({ id: 'add_photo' })}
        </Button>
      </FormControl>
      {loading && (
        <Box mt={1}>
          <Skeleton variant="rounded" width={200} height={100} />
          <Skeleton variant="text" sx={{ fontSize: '15px' }} width="80%" />
        </Box>
      )}
      {field?.value && !loading && (
        <>
          <Preview>
            <Image src={field.value} />
            <Tooltip title={i18n.formatMessage({ id: 'remove' })}>
              <RemoveBtn onClick={handleDeletePhoto}>
                <LineIcon icon="ico-close" />
              </RemoveBtn>
            </Tooltip>
          </Preview>
          <Box mt={1}>{field.value}</Box>
        </>
      )}
      <input
        ref={inputRef}
        type="file"
        accept="image/*"
        data-testid={camelCase(`input ${name}`)}
        onChange={handleFileChange}
        style={fixInputStyle}
      />
    </>
  );
}
export default DirectUploadFile;

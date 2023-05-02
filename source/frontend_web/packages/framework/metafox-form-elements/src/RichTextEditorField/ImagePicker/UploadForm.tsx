import { FormBuilder } from '@metafox/form';
import { useGlobal } from '@metafox/framework';
import React from 'react';

export default function UploadForm({ onChange, onSubmit }) {
  const { i18n } = useGlobal();

  const initialValues = {
    src: '',
    width: 'auto',
    height: 'auto'
  };

  const formSchema = {
    component: 'Form',
    elements: {
      src: {
        name: 'src',
        required: true,
        component: 'DirectUploadFile',
        label: i18n.formatMessage({ id: 'image_url' }),
        fullWidth: true,
        placeholder: i18n.formatMessage({ id: 'image_url' }),
        variant: 'outlined',
        size: 'small'
      },
      size: {
        name: 'size',
        component: 'Container',
        variant: 'horizontal',
        elements: {
          width: {
            name: 'width',
            component: 'Text',
            margin: 'normal',
            label: i18n.formatMessage({ id: 'image_width' }),
            fullWidth: false,
            sx: { maxWith: 200 },
            placeholder: i18n.formatMessage({ id: 'image_width' }),
            description: i18n.formatMessage({ id: 'image_width_desc' }),
            variant: 'outlined',
            size: 'small'
          },
          height: {
            name: 'height',
            component: 'Text',
            margin: 'normal',
            label: i18n.formatMessage({ id: 'image_height' }),
            fullWidth: false,
            sx: { maxWith: 200 },
            placeholder: i18n.formatMessage({ id: 'image_height' }),
            description: i18n.formatMessage({ id: 'image_height_desc' }),
            variant: 'outlined',
            size: 'small'
          }
        }
      },
      footer: {
        name: '_footer',
        component: 'FormFooter',
        elements: {
          submit: {
            name: '_submit',
            component: 'Submit',
            label: i18n.formatMessage({ id: 'ok' }),
            variant: 'contained',
            color: 'primary'
          }
        }
      }
    }
  };

  return (
    <FormBuilder
      formSchema={formSchema}
      onSubmit={onSubmit}
      initialValues={initialValues}
      onChange={onChange}
    />
  );
}

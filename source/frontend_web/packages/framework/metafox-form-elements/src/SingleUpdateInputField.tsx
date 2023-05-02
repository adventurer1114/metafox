/**
 * @type: formElement
 * name: form.element.SingleUpdateInputField
 * chunkName: formExtras
 */

import { useGlobal } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer/HtmlViewer';
import { Box, Button, styled, Typography } from '@mui/material';
import { get, isEmpty, isEqual, omit } from 'lodash';
import React, { useEffect, useState } from 'react';

const StyledItemView = styled('div', {
  name: 'StyledItemView'
})(({ theme }) => ({
  display: 'flex',
  alignItems: 'baseline',
  width: '100%',
  justifyContent: 'flex-start',
  borderBottom: 'solid 1px',
  borderBottomColor: theme.palette.border?.secondary,
  padding: theme.spacing(2.75, 0),
  [theme.breakpoints.down('sm')]: {
    display: 'block'
  }
}));

const StyledTitle = styled('div', {
  name: 'StyledItemView',
  slot: 'StyledTitle'
})(({ theme }) => ({
  width: 190,
  minWidth: 190,
  fontSize: theme.typography.body1.fontSize,
  lineHeight: 1.6,
  fontWeight: theme.typography.fontWeightBold,
  color: theme.palette.text.primary
}));

const StyledContent = styled('div', {
  name: 'StyledItemView',
  slot: 'StyledContent'
})(({ theme }) => ({
  padding: theme.spacing(0, 2),
  width: '100%',
  fontSize: theme.typography.body1.fontSize,
  lineHeight: 1.6,
  color: theme.palette.text.primary
}));

const StyledContentInner = styled('div', {
  name: 'StyledItemView',
  slot: 'StyledContentInner',
  shouldForwardProp: props => props !== 'editComponent'
})<{ editComponent?: string }>(({ theme, editComponent }) => ({
  display: 'flex',
  flexDirection: 'column',
  '& button': {
    marginRight: theme.spacing(1)
  },
  '& div:first-of-type': {
    marginTop: 0,
    ...(editComponent !== 'Location' && {
      marginBottom: 0
    })
  },
  ...(editComponent === 'RadioGroup' && {
    '& .MuiFormGroup-root > div': {
      marginBottom: theme.spacing(1)
    }
  })
}));

const DescriptionSingleInput = styled(Box)(({ theme }) => ({
  padding: theme.spacing(1, 0)
}));

const DescriptionPrivacy = styled(Box)(({ theme }) => ({
  fontSize: theme.typography.body2.fontSize
}));

const mappingDisplayValue = (
  type: string,
  value: any,
  options: Array<any>,
  formValue: any,
  contextualDescription?: string,
  pageParams?: any
) => {
  // this not good solution to continue, just add some field to analyze later.
  const { appName, id } = pageParams;

  switch (type) {
    case 'input':
      return value;
    case 'Select': {
      return options.find(opt => opt.value === value)?.label;
    }
    case 'TypeCategory': {
      const { category_id } = formValue;

      let categorySelect;
      for (const option of options) {
        const temp = option.categories.find(
          category => category.id === category_id
        );

        if (temp) {
          categorySelect = temp;
          break;
        }
      }

      return categorySelect?.name;
    }
    case 'textarea':
      return <HtmlViewer html={value} />;
    case 'editor': {
      return <div dangerouslySetInnerHTML={{ __html: value }} />;
    }
    case 'RadioGroup':
      return (
        <>
          <span>{options[value].label}</span>
          {options[value]?.description && (
            <DescriptionPrivacy>
              {options[value].description}
            </DescriptionPrivacy>
          )}
        </>
      );

    case 'Location':
      return value?.address;
    case 'Text':
      if (contextualDescription)
        return `${contextualDescription}${value || `${appName}/${id}`}`;
      else return value;
    default: {
      return value;
    }
  }
};

export default function EditableField({
  handleSubmitField,
  onReset,
  config,
  ...props
}: any) {
  const { i18n, jsxBackend, usePageParams } = useGlobal();
  const pageParams = usePageParams();
  const [isEdit, setEdit] = useState(false);

  const { formik, name } = props;
  const { required, reloadOnSubmit } = config;

  const value = get(formik?.values, name);

  const handleEdit = () => {
    setEdit(true);
  };

  const handleSave = () => {
    // show err when trim value empty and have required
    if (
      typeof value === 'string' &&
      value.trim() === '' &&
      required &&
      isEmpty(formik.errors)
    ) {
      formik.setFieldValue(name, value.trim());

      return;
    }

    if (
      config.editComponent === 'RadioGroup' &&
      get(formik?.initialValues, name) === Number.parseInt(value)
    ) {
      setEdit(false);

      return;
    }

    if (isEmpty(formik.errors[name])) {
      const data = {
        [name]: value
      };

      // submit only field editing
      handleSubmitField &&
        handleSubmitField(data, {
          ...formik,
          callbackOnSuccess: () => {
            setEdit(false);

            if (reloadOnSubmit) {
              onReset();
            }

            setImmediate(() => formik.resetForm({ values: formik.values }));
          }
        });

      if (isEqual(formik.initialValues, formik.values)) {
        // reset dirty state of form
        formik.resetForm({ values: formik.values });
      }
    }
  };

  const handleCancel = () => {
    setEdit(false);
    const initialValues = get(formik?.initialValues, name);

    formik.setFieldValue(name, initialValues);
  };

  useEffect(() => {
    if (!get(formik.errors, name)) return;

    setEdit(true);
  }, [formik.errors, name]);

  const {
    label,
    options,
    editComponent,
    contextualDescription,
    descriptionSingleInput
  } = config;

  const EditComponent = jsxBackend.get(`form.element.${editComponent}`);

  const modifiedValue = mappingDisplayValue(
    editComponent,
    value,
    options,
    formik?.values || formik?.initialValues,
    contextualDescription,
    pageParams
  );

  return (
    <StyledItemView>
      <StyledTitle>{label}</StyledTitle>
      <StyledContent>
        {isEdit ? (
          <StyledContentInner editComponent={editComponent}>
            {EditComponent ? (
              <>
                <EditComponent
                  {...props}
                  config={omit(config, ['label', 'descriptionSingleInput'])}
                />
                {descriptionSingleInput && (
                  <DescriptionSingleInput>
                    <Typography variant="body2" color="text.hint">
                      {descriptionSingleInput}
                    </Typography>
                  </DescriptionSingleInput>
                )}
              </>
            ) : null}
            <Box sx={{ mb: 1, mt: 3 }}>
              <Button variant="contained" onClick={handleSave}>
                {i18n.formatMessage({ id: 'save' })}
              </Button>
              <Button variant="outlined" onClick={handleCancel}>
                {i18n.formatMessage({ id: 'cancel' })}
              </Button>
            </Box>
          </StyledContentInner>
        ) : (
          <p>{modifiedValue}</p>
        )}
      </StyledContent>
      {!isEdit && (
        <Button variant="text" onClick={handleEdit}>
          {i18n.formatMessage({ id: 'edit' })}
        </Button>
      )}
    </StyledItemView>
  );
}

/**
 * @type: formElement
 * name: form.element.MembershipQuestion
 * chunkName: formElement
 */
import { FormFieldProps } from '@metafox/form';
import { useGlobal } from '@metafox/framework';
import { Add } from '@mui/icons-material';
import {
  Box,
  Button,
  FormControl,
  MenuItem,
  Select,
  TextField
} from '@mui/material';
import { camelCase } from 'lodash';
import React from 'react';
import ErrorMessage from '../ErrorMessage';
import ErrorTooltip from '../ErrorTooltip';
import Answer from './Answer';
import { NEW, TypeQuestion, UPDATE } from './type';

const typeOptions = [
  { value: TypeQuestion.CheckBox, label: 'checkbox' },
  { value: TypeQuestion.Select, label: 'multiple_choice' },
  { value: TypeQuestion.FreeAnswer, label: 'written_answer' }
];

const MembershipQuestionDialog = ({
  name,
  formik,
  disabled: forceDisabled
}: FormFieldProps) => {
  const { i18n } = useGlobal();

  // init fields data
  React.useEffect(() => {
    formik.setFieldValue(
      'question',
      formik.values?.question || formik.initialValues?.question
    );
    formik.setFieldValue(
      'type_id',
      formik.values?.type_id || formik.initialValues?.type_id
    );
    formik.setFieldValue(
      'options',
      formik.values?.options || formik.initialValues?.options || []
    );
  }, []);

  const handleAddAnswer = () => {
    const newValue = [
      ...formik.values?.options,
      { title: '', id: new Date().getMilliseconds(), type: NEW }
    ];

    formik.setFieldValue('options', newValue);
  };

  const handleChangeAnswer = (e, item) => {
    const { value } = e.target;

    // eslint-disable-next-line no-confusing-arrow
    const newValue = formik.values.options.map(answer =>
      answer.id === item.id
        ? {
            ...answer,
            title: value,
            type:
              !!formik.initialValues?.options && item.type !== NEW
                ? UPDATE
                : NEW
          }
        : answer
    );

    formik.setFieldValue('options', newValue);
  };

  const handleBlurAnswer = item => {
    if (!item.title.trim()) handleRemoveAnswer(item.id);
  };

  const handleBlurQuestion = e => {
    if (!formik.values?.question?.trim()) formik.setFieldValue('question', '');

    formik.handleBlur(e);
  };

  const handleRemoveAnswer = (id: number) => {
    const newValue = formik.values.options?.filter(item => item.id !== id);
    formik.setFieldValue('options', newValue);
  };

  return (
    <FormControl
      fullWidth
      margin="normal"
      data-testid={camelCase(`field ${name}`)}
    >
      <Box sx={{ height: '40px', display: 'flex' }}>
        <ErrorTooltip name={'question'} showErrorTooltip>
          <TextField
            id="question"
            name="question"
            sx={{ flex: 1, minWidth: 0, marginRight: 2 }}
            size="small"
            label={i18n.formatMessage({ id: 'add_a_question' })}
            variant="outlined"
            inputProps={{
              maxLength: 255
            }}
            error={!!(formik.submitCount && formik.errors.question)}
            value={formik.values?.question}
            onChange={formik.handleChange}
            onBlur={handleBlurQuestion}
          />
        </ErrorTooltip>
        <Select
          id="type_id"
          name="type_id"
          sx={{ height: '100%' }}
          size="small"
          value={formik.values?.type_id}
          onChange={formik.handleChange}
        >
          {typeOptions.map(({ value, label }, index) => (
            <MenuItem key={index.toString()} value={value}>
              {i18n.formatMessage({ id: label })}
            </MenuItem>
          ))}
        </Select>
      </Box>
      {formik.values.type_id !== TypeQuestion.FreeAnswer ? (
        <>
          {formik.values?.options?.map(item => {
            return (
              <Answer
                key={item.id.toString()}
                value={item.title}
                type={formik.values.type_id}
                onBlur={() => handleBlurAnswer(item)}
                onRemove={() => handleRemoveAnswer(item.id)}
                onChange={e => handleChangeAnswer(e, item)}
              />
            );
          })}
          <Button
            sx={{ marginTop: 2, width: '130px' }}
            startIcon={<Add />}
            size="small"
            disabled={forceDisabled}
            onClick={handleAddAnswer}
            variant="contained"
          >
            {i18n.formatMessage({ id: 'more_options' })}
          </Button>
          <ErrorMessage error={formik.errors?.options} />
        </>
      ) : null}
    </FormControl>
  );
};

export default MembershipQuestionDialog;

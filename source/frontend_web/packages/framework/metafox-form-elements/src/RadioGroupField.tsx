/**
 * @type: formElement
 * name: form.element.RadioGroup
 * chunkName: formExtras
 */
import { FormFieldProps } from '@metafox/form';
import { LineIcon } from '@metafox/ui';
import {
  Box,
  FormControl,
  FormControlLabel,
  Radio,
  RadioGroup,
  styled,
  Typography
} from '@mui/material';
import { useField } from 'formik';
import { camelCase } from 'lodash';
import React from 'react';
import ErrorMessage from './ErrorMessage';
import Label from './Label';

const Title = styled(Typography, {
  name: 'Title',
  shouldForwardProp: prop => prop !== 'styleGroup'
})<{ styleGroup?: string }>(({ theme, styleGroup }) => ({
  ...(styleGroup === 'question' && {
    color: theme.palette.text.secondary,
    fontWeight: theme.typography.fontWeightBold
  }),
  ...(styleGroup === 'review_post' && {
    color: theme.palette.text.hint
  }),
  ...(styleGroup === 'normal' && {})
}));

const LabelStyled = styled(Box)(({ theme }) => ({
  display: 'flex',
  alignItems: 'center'
}));

const RadioStyled = styled(Radio)(({ theme }) => ({
  height: 42,
  width: 42
}));

const DescriptionStyled = styled(Typography)(({ theme }) => ({
  marginLeft: theme.spacing(3.75)
}));

const RadioGroupField = ({
  config,
  name,
  disabled: forceDisabled,
  formik
}: FormFieldProps) => {
  const [field, meta] = useField(name ?? 'RadioGroupField');
  const {
    options,
    label,
    margin = 'normal',
    variant,
    fullWidth,
    disabled,
    labelPlacement,
    inline,
    size,
    required,
    hasFormOrder = false,
    order,
    styleGroup = 'normal',
    description,
    titleConfig,
    descriptionConfig
  } = config;

  const style = inline ? { flexDirection: 'row' } : undefined;

  const orderLabel = hasFormOrder && order ? `${order}. ` : null;

  const haveError: boolean = !!(
    meta.error &&
    (meta.touched || formik.submitCount)
  );

  return (
    <FormControl
      component="fieldset"
      margin={margin}
      variant={variant as any}
      disabled={disabled || forceDisabled || formik.isSubmitting}
      fullWidth={fullWidth}
      size={size}
      error={haveError}
      data-testid={camelCase(`field ${name}`)}
      required={required}
    >
      {orderLabel && label ? (
        <Title mb={1} {...titleConfig} styleGroup={styleGroup}>
          {orderLabel}
          {label}
        </Title>
      ) : null}
      {description ? (
        <Typography
          my={1}
          color="text.secondary"
          variant="body2"
          {...descriptionConfig}
        >
          {description}
        </Typography>
      ) : null}
      <RadioGroup
        aria-label={label}
        name={field.name}
        value={field.value?.toString()}
        onChange={field.onChange}
        style={style}
        sx={{ alignItems: 'start' }}
      >
        {options
          ? options.map((item, index) => (
              <Box key={index.toString()}>
                <FormControlLabel
                  sx={{ alignItems: 'center' }}
                  labelPlacement={labelPlacement}
                  value={item.value?.toString()}
                  disabled={
                    disabled ||
                    item.disabled ||
                    forceDisabled ||
                    formik.isSubmitting
                  }
                  control={<RadioStyled disabled={item.disabled} />}
                  label={
                    <LabelStyled>
                      {item.icon ? (
                        <LineIcon
                          sx={{ marginRight: '8px' }}
                          icon={item.icon}
                        />
                      ) : null}
                      <Label text={item.label} hint={item.hint} />
                    </LabelStyled>
                  }
                />
                {item?.description ? (
                  <DescriptionStyled
                    variant="body2"
                    color={
                      disabled ||
                      item.disabled ||
                      forceDisabled ||
                      formik.isSubmitting
                        ? 'text.disabled'
                        : 'text.secondary'
                    }
                  >
                    {item.description}
                  </DescriptionStyled>
                ) : null}
              </Box>
            ))
          : null}
      </RadioGroup>
      {haveError ? <ErrorMessage error={meta.error} /> : null}
    </FormControl>
  );
};

export default RadioGroupField;

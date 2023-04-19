/**
 * @type: formElement
 * name: form.element.CustomButton
 * chunkName: formBasic
 */
import { FormFieldProps, useFormSchema } from '@metafox/form';
import { useGlobal } from '@metafox/framework';
import { Button as MuiButton, FormControl } from '@mui/material';
import { camelCase } from 'lodash';
import React from 'react';

const CancelButtonField = ({
  config,
  disabled: forceDisabled,
  formik,
  onReset
}: FormFieldProps) => {
  const {
    type = 'button',
    variant,
    disabled,
    color,
    size,
    label,
    className,
    margin,
    fullWidth,
    name,
    onClick,
    customAction
  } = config;
  const { navigate, dialog, onCancel } = useFormSchema();
  const { dispatch } = useGlobal();
  const { useDialog } = useGlobal();
  const { closeDialog } = useDialog();

  const handleFormBehavior = () => {
    if (onCancel) {
      onCancel();
    } else if (onClick) {
      onClick();
    } else if (dialog) {
      closeDialog();
    } else {
      navigate(-1);
    }
  };

  const meta = () => {
    onReset();
  };

  const handleClick = React.useCallback(() => {
    dispatch({ type: customAction.type, payload: customAction.payload, meta });
    handleFormBehavior();
  }, []);

  return (
    <FormControl
      margin={margin}
      fullWidth={fullWidth}
      data-testid={camelCase(`field ${name}`)}
      sx={{ display: 'block' }}
    >
      <MuiButton
        fullWidth={fullWidth}
        variant={variant as any}
        color={color}
        size={size}
        type={type}
        className={className}
        disabled={disabled || forceDisabled || formik.isSubmitting}
        data-testid={camelCase(`button ${name}`)}
        onClick={handleClick}
      >
        {label}
      </MuiButton>
    </FormControl>
  );
};

export default CancelButtonField;

/**
 * @type: formElement
 * name: form.element.Submit
 * chunkName: formBasic
 */
import MuiButton from '@mui/lab/LoadingButton';
import { FormControl, useMediaQuery } from '@mui/material';
import { useTheme } from '@mui/material/styles';
import { useField } from 'formik';
import { camelCase } from 'lodash';
import React from 'react';
import { FormFieldProps } from '@metafox/form';
import { useGlobal } from '@metafox/framework';

function SubmitButtonField({
  config,
  name,
  disabled: forceDisabled,
  formik
}: FormFieldProps) {
  const {
    disabled,
    color = 'primary',
    size,
    type,
    margin,
    flexWidth,
    value: submitValue, // @submit with send value must `submitValue` in configuration.
    label,
    fullWidth = false,
    className,
    variant = 'contained',
    confirmation,
    disableWhenClean
  } = config;
  const [, , { setValue }] = useField(name ?? 'SubmitButtonField');
  const { dialogBackend, setNavigationConfirm } = useGlobal();
  const [clicked, setClicked] = React.useState(false);

  const theme = useTheme();
  const isSmallScreen = useMediaQuery(theme.breakpoints.down('sm'));

  const handleClick = React.useCallback(
    evt => {
      // prevent submit duplication on firefox
      evt.preventDefault();

      setClicked(true);
      // don't keep draft button after re-render

      if (submitValue) {
        setValue(submitValue);
      }

      if (confirmation) {
        dialogBackend.confirm(confirmation).then(ok => {
          if (!ok) {
            setClicked(false);

            return;
          }

          formik.submitForm().finally(() => {
            setValue(undefined);
            setClicked(false);
            setNavigationConfirm(false);
          });
        });

        return;
      }

      formik.submitForm().finally(() => {
        setValue(undefined);
        setClicked(false);
        setNavigationConfirm(false);
      });
      // does not allow change submitValue after render
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [formik, setValue]
  );

  return (
    <FormControl
      margin={margin}
      fullWidth={fullWidth}
      sx={{
        minWidth: !isSmallScreen && flexWidth ? '275px' : 'auto'
      }}
      data-testid={camelCase(`field ${name}`)}
    >
      <MuiButton
        fullWidth={fullWidth}
        variant={variant as any}
        color={color}
        size={isSmallScreen ? 'medium' : size}
        loading={clicked && formik.isSubmitting}
        className={className}
        type={type}
        role="button"
        disabled={
          disabled ||
          forceDisabled ||
          (disableWhenClean && !formik.dirty) ||
          clicked ||
          formik.isSubmitting
        }
        data-testid={camelCase(`button ${name}`)}
        onClick={handleClick}
      >
        {label}
      </MuiButton>
    </FormControl>
  );
}

export default SubmitButtonField;

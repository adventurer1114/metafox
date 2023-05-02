/**
 * @type: formElement
 * name: form.element.LoginByGoogleButton
 * chunkName: formBasic
 */
import { FormControl } from '@mui/material';
import MuiButton from '@mui/material/Button';
import { camelCase } from 'lodash';
import React from 'react';
import { FormFieldProps } from '@metafox/form';
import { useScript, useGlobal } from '@metafox/framework';

function LoginByGoogleButton({
  config,
  name,
  disabled: forceDisabled,
  formik
}: FormFieldProps) {
  const {
    disabled,
    controlProps = {},
    color = 'primary',
    margin,
    size = 'large',
    fullWidth = false,
    className,
    variant,
    sx,
    icon
  } = config;

  const status = useScript('https://accounts.google.com/gsi/client');
  const ref = React.useRef();

  const { getSetting, dispatch } = useGlobal();

  const [tokenClient, setTokenClient] = React.useState({});
  const client_id = getSetting('socialite.google.client_id');

  const handleClick = () => {
    tokenClient.requestAccessToken();
  };

  const handleCallback = res => {
    dispatch({
      type: 'login/callback',
      payload: { accessToken: res.access_token, provider: 'google' }
    });
  };

  React.useEffect(() => {
    if (status === 'ready') {
      setTokenClient(
        google.accounts.oauth2.initTokenClient({
          client_id,
          scope: 'email profile',
          callback: handleCallback
        })
      );
    }
  }, [status]);

  return (
    <FormControl
      margin={margin}
      fullWidth={fullWidth}
      {...controlProps}
      sx={sx}
      data-testid={camelCase(`field ${name}`)}
    >
      <MuiButton
        fullWidth={fullWidth}
        variant={variant as any}
        role="button"
        color={color}
        size={size}
        type="button"
        className={className}
        disabled={disabled || formik.isSubmitting || forceDisabled}
        data-testid={camelCase(`button ${name}`)}
        onClick={handleClick}
        ref={ref}
      >
        <img src={icon} alt="google" width={32} />
      </MuiButton>
    </FormControl>
  );
}
export default LoginByGoogleButton;

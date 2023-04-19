/**
 * @type: formElement
 * name: form.element.IconButton
 * chunkName: formBasic
 */

import { Link, useGlobal } from '@metafox/framework';
import React from 'react';
import { FormFieldProps } from '@metafox/form';
import { styled, Tooltip } from '@mui/material';
import { LineIcon } from '@metafox/ui';

const IconStyled = styled(LineIcon, { name: 'IconStyled' })(
  ({ theme }) => ({
    color: theme.palette.text.secondary,
    fontSize: theme.mixins.pxToRem(16),
    marginLeft: theme.spacing(1),
    '&:hover': {
      color: theme.palette.primary.main
    }
  })
);

function IconButtonField({ config, name, formik }: FormFieldProps) {
  const { icon, tooltip, linkTo } = config;
  const { i18n } = useGlobal();

  return (
    <Tooltip title={i18n.formatMessage({ id: tooltip })}>
      <Link to={linkTo} sx={{ textDecoration: 'none!important' }}>
        <IconStyled icon={icon} />
      </Link>
    </Tooltip>
  );
}
export default IconButtonField;

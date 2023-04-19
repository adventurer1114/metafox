/**
 * @type: formElement
 * name: form.element.ErrorTooltip
 */
import WarningIcon from '@mui/icons-material/Warning';
import { styled, Tooltip } from '@mui/material';
import { useField } from 'formik';
import React, { useState } from 'react';

const ToBeStyledTooltip = ({ className, children, ...props }) => (
  <Tooltip
    {...props}
    title={props.title}
    className={className}
    disableHoverListener
    classes={{ tooltip: className }}
  >
    {children}
  </Tooltip>
);
const StyledTooltip = styled(ToBeStyledTooltip)(({ theme }) => ({
  backgroundColor: theme.palette.background.paper,
  borderColor: 'border.secondary',
  borderStyle: 'solid',
  color: 'red',
  '& .MuiTooltip-arrow': {
    color: 'red'
  }
}));

const StyledTooltipTitle = styled('div')(({ theme }) => ({
  display: 'flex',
  alignItems: 'center'
}));

type ErrorTooltipProps = {
  children: any;
  name: string;
  showErrorTooltip?: boolean;
};

const ErrorTooltip = ({
  children,
  name,
  showErrorTooltip
}: ErrorTooltipProps) => {
  const [open, setOpen] = useState(false);
  const [, meta] = useField(name);

  if (!showErrorTooltip) return children;

  return (
    <StyledTooltip
      className={{}}
      open={open && !!meta?.error}
      onClose={() => setOpen(false)}
      onOpen={() => setOpen(true)}
      title={
        <StyledTooltipTitle>
          <WarningIcon sx={{ marginRight: 0.5 }} fontSize="inherit" />{' '}
          {meta?.error}
        </StyledTooltipTitle>
      }
    >
      {children}
    </StyledTooltip>
  );
};

export default ErrorTooltip;

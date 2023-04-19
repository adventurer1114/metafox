import { LineIcon } from '@metafox/ui';
import { IconButton, Theme, Tooltip } from '@mui/material';
import { createStyles, withStyles } from '@mui/styles';
import React from 'react';

const styles = (theme: Theme) =>
  createStyles({
    root: {
      boxShadow: theme.shadows[1]
    },
    sizeSmall: {
      fontSize: '13px',
      padding: 8
    },

    colorInherit: {
      color: theme.palette.text.secondary,
      backgroundColor: 'rgba(255,255,255,0.9)',
      '&:hover': {
        backgroundColor: 'rgba(255,255,255,0.96)',
        color: theme.palette.text.primary
      }
    },
    colorPrimary: {
      color: 'white !important',
      backgroundColor: 'rgba(0,0,0,0.7) !important',
      '&:hover': {
        backgroundColor: 'rgba(0,0,0,0.8) !important',
        color: 'white !important'
      }
    }
  });

function MyIconButton({ title, classes, icon, ...rest }) {
  return (
    <Tooltip title={title}>
      <IconButton classes={classes} {...rest}>
        <LineIcon icon={icon} />
      </IconButton>
    </Tooltip>
  );
}

export default withStyles(styles)(MyIconButton);

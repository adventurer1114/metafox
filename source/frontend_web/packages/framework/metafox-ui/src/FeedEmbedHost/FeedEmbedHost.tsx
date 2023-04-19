import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';
import React from 'react';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        color: theme.palette.text.secondary,
        textTransform: 'uppercase',
        fontWeight: theme.typography.fontWeightBold,
        fontSize: theme.mixins.pxToRem(15),
        marginTop: 2
      }
    }),
  { name: 'MuiFeedEmbedHost' }
);

export default function FeedEmbedHost({ host }: { host: string }) {
  const classes = useStyles();

  if (!host) return null;

  return <div className={classes.root}>{host}</div>;
}

import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {},
      item: {
        display: 'flex',
        marginTop: theme.spacing(1),
        fontSize: theme.mixins.pxToRem(13),
        lineHeight: 1.5
      },
      itemInfo: {
        display: 'flex'
      }
    }),
  {
    name: 'AttachmentField'
  }
);

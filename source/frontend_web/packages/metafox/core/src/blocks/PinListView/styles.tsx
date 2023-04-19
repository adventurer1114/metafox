import { createStyles, makeStyles } from '@mui/styles';
import { Theme } from '@mui/material';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        display: 'flex',
        flexWrap: 'wrap',
        alignItems: 'flex-start'
      },
      column: {
        position: 'relative',
        flexBasis: 0,
        display: 'flex',
        flexDirection: 'column',
        minWidth: '200px',
        flexGrow: 1
      }
    }),
  { name: 'PinView' }
);

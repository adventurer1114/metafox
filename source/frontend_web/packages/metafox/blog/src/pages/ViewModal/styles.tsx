import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        padding: '0 !important',
        overflowY: 'visible',
        display: 'flex'
      },
      contentWrapper: {
        width: '1020px',
        maxWidth: '100%'
      }
    }),
  { name: 'MuiBlogViewModal' }
);

export default useStyles;

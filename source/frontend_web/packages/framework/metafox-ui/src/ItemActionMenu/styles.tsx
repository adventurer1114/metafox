import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {},
      popper: {
        zIndex: theme.zIndex.snackbar,
        minWidth: '180px',
        width: 'auto',
        maxHeight: '70vh'
      },
      menu: { padding: theme.spacing(1, 0) }
    }),
  { name: 'MuiActionMenu' }
);

export default useStyles;

import { createStyles, makeStyles } from '@mui/styles';
import { Theme } from '@mui/material';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        minWidth: 130,
        cursor: 'pointer',
        transition: 'all 0.2s ease',
        position: 'relative',
        '&:hover': {
          boxShadow: theme.shadows[4],
          opacity: 0.8
        },
        minHeight: 150
      }
    }),
  { name: 'MuiPhotoCoverItemView' }
);

export default useStyles;

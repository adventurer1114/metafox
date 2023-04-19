import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      scroll: {
        '& > *:first-of-type': {
          maxHeight: '350px !important'
        }
      },
      tabs: {
        marginLeft: theme.spacing(2)
      },
      tab: {
        height: 50,
        fontSize: theme.mixins.pxToRem(15),
        color: theme.palette.text.secondary,
        fontWeight: 'bold',
        borderBottom: '2px solid transparent',
        minWidth: 0,
        padding: 0,
        marginRight: theme.spacing(4),
        textTransform: 'uppercase',
        '& .Mui-selected': {
          borderBottomColor: theme.palette.primary.main,
          color: theme.palette.primary.main
        },
        '&:hover': {
          color: theme.palette.primary.main
        }
      }
    }),
  { name: 'ManageHidden' }
);

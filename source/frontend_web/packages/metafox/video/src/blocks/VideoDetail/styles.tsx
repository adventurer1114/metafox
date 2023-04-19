import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        padding: '0 !important',
        display: 'flex',
        [theme.breakpoints.down('sm')]: {
          flexFlow: 'column'
        }
      },
      dialogVideo: {
        backgroundColor: '#000',
        width: '720px',
        height: '405px',
        borderTopLeftRadius: theme.shape.borderRadius,
        borderBottomLeftRadius: theme.shape.borderRadius,
        overflow: 'hidden',
        '& iframe': {
          width: '100%',
          height: '100%'
        },
        [theme.breakpoints.down('md')]: {
          width: '65%'
        },
        [theme.breakpoints.down('sm')]: {
          width: '100%',
          height: 'auto',
          borderRadius: 0,
          overflow: 'initial'
        }
      },
      dialogStatistic: {
        height: '405px',
        width: '480px',
        flexGrow: 1,
        [theme.breakpoints.down('md')]: {
          width: '35%'
        },
        [theme.breakpoints.down('sm')]: {
          width: '100%',
          height: 'auto'
        }
      },
      paper: {
        '& .MuiDialog-paper': {
          overflowY: 'unset',
          margin: `${theme.spacing(8)}px`
        }
      },
      closeButton: {
        position: 'absolute !important',
        top: theme.mixins.pxToRem(-57),
        right: theme.mixins.pxToRem(-37),
        minWidth: 'unset',
        width: theme.mixins.pxToRem(27),
        height: `${theme.mixins.pxToRem(27)} !important`,
        color: '#fff !important'
      },
      icon: {
        fontSize: `${theme.mixins.pxToRem(24)} !important`
      }
    }),
  { name: 'MuiVideoViewDetail' }
);

export default useStyles;

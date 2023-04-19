import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        height: 320,
        overflow: 'clip',
        position: 'relative',
        [theme.breakpoints.down('sm')]: {
          height: 180,
          marginBottom: 0
        }
      },
      bgBlur: {
        backgroundColor: theme.palette.background.default,
        height: 320,
        filter: 'blur(100px)',
        overflow: 'hidden',
        position: 'absolute',
        top: 0,
        bottom: 0,
        left: 0,
        right: 0
      },
      bgCover: {
        transform: 'translate(0px, 0px)',
        backgroundRepeat: 'no-repeat',
        backgroundPosition: 'center',
        backgroundSize: 'cover',
        height: 320,
        [theme.breakpoints.down('sm')]: {
          height: 180
        }
      }
    }),
  { name: 'MuiBannerEventViewDetail' }
);

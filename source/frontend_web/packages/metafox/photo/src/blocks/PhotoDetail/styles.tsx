import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        padding: 0,
        paddingTop: '0 !important',
        borderRadius: theme.shape.borderRadius,
        maxWidth: 1200,
        margin: 'auto',
        display: 'flex',
        [theme.breakpoints.down('xs')]: {
          flexFlow: 'column'
        }
      },
      imageContainer: {
        height: '600px',
        display: 'flex',
        alignItems: 'center',
        backgroundColor: '#000',
        justifyContent: 'center',
        position: 'relative',
        flexGrow: 1,
        borderRadius: `${theme.shape.borderRadius}px 0 0 ${theme.shape.borderRadius}px `,
        [theme.breakpoints.down('md')]: {
          width: '65%'
        },
        [theme.breakpoints.down('sm')]: {
          width: '55%'
        },
        [theme.breakpoints.down('xs')]: {
          width: '100%',
          height: 'auto',
          minHeight: '250px',
          maxHeight: '350px',
          borderRadius: `${theme.shape.borderRadius}px ${theme.shape.borderRadius}px 0 0`
        }
      },
      image: {
        maxWidth: '720px',
        maxHeight: '600px',
        float: 'left'
      },
      statistic: {
        height: '600px',
        width: '480px',
        [theme.breakpoints.down('md')]: {
          width: '35%'
        },
        [theme.breakpoints.down('sm')]: {
          width: '45%'
        },
        [theme.breakpoints.down('xs')]: {
          width: '100%',
          height: '400px',
          float: 'none'
        }
      },
      nextPhoto: {
        position: 'absolute',
        right: theme.spacing(2),
        top: '50%',
        color: '#fff',
        fontSize: theme.mixins.pxToRem(13),
        backgroundColor: 'rgba(0,0,0,0.4)',
        width: '32px',
        height: '32px',
        borderRadius: '32px',
        minWidth: '32px',
        '&:hover': {
          backgroundColor: 'rgba(0,0,0,0.8)'
        }
      },
      prevPhoto: {
        position: 'absolute',
        left: theme.spacing(2),
        top: '50%',
        color: '#fff',
        fontSize: theme.mixins.pxToRem(13),
        backgroundColor: 'rgba(0,0,0,0.4)',
        width: '32px',
        height: '32px',
        borderRadius: '32px',
        minWidth: '32px',
        '&:hover': {
          backgroundColor: 'rgba(0,0,0,0.8)'
        }
      },
      actionBar: {
        position: 'absolute',
        right: 0,
        padding: theme.spacing(1),
        display: 'flex',
        justifyContent: 'flex-end',
        zIndex: 1,
        bottom: 0
      },
      tagFriend: {
        color: '#fff',
        minWidth: '32px',
        height: '32px',
        margin: theme.spacing(0, 1)
      },
      dropDown: {
        color: '#fff',
        minWidth: '32px',
        height: '32px'
      },
      imageBox: {
        position: 'relative',
        maxWidth: '100%',
        maxHeight: '600px'
      },
      taggedFriend: {
        position: 'absolute',
        backgroundColor: 'rgba(0,0,0,.8)',
        borderRadius: theme.shape.borderRadius,
        padding: theme.spacing(0.5, 1)
      },
      tagLink: {
        color: '#fff',
        fontSize: theme.mixins.pxToRem(13),
        fontWeight: theme.typography.fontWeightBold
      },
      taggedBox: {
        position: 'absolute',
        left: 0,
        right: 0,
        top: 0,
        bottom: 0
      },
      tagBox: {
        width: '100px',
        height: '100px',
        border: '2px solid',
        borderColor: theme.palette.background.paper,
        boxShadow: theme.shadows[20],
        position: 'absolute'
      },
      tagFriendBox: {
        cursor: 'pointer'
      },
      suggestFriend: {
        backgroundColor: theme.mixins.backgroundColor('paper'),
        marginTop: '110px',
        marginLeft: '-70px',
        border: theme.mixins.border('secondary'),
        borderRadius: theme.shape.borderRadius,
        '&  .MuiFilledInput-root': {
          background: 'none'
        }
      },
      smallAvatar: {
        width: '32px',
        height: '32px',
        marginRight: theme.spacing(1)
      }
    }),
  { name: 'MuiPhotoViewDetail' }
);

export default useStyles;

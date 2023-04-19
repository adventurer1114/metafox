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
        [theme.breakpoints.down('xs')]: {
          flexFlow: 'column'
        }
      },
      imageContainer: {
        display: 'flex',
        alignItems: 'center',
        backgroundColor: '#000',
        justifyContent: 'center',
        position: 'relative',
        flexGrow: 1,
        borderRadius: `${theme.shape.borderRadius}px 0 0 ${theme.shape.borderRadius}px `,
        [theme.breakpoints.down('sm')]: {
          borderRadius: '0'
        }
      },
      fullScreenView: {
        position: 'fixed',
        left: 0,
        right: 0,
        top: 0,
        bottom: 0,
        zIndex: 99
      },
      image: {
        maxWidth: '720px',
        float: 'left'
      },
      statistic: {
        width: '480px',
        [theme.breakpoints.down('md')]: {
          width: '35%'
        },
        [theme.breakpoints.down('sm')]: {
          width: '45%'
        },
        [theme.breakpoints.down('xs')]: {
          width: '100%',
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
        zIndex: 1,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
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
        zIndex: 1,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
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
        maxWidth: '100%'
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
      },
      header: {
        display: 'flex',
        flexDirection: 'row',
        marginBottom: theme.spacing(2),
        padding: theme.spacing(0, 2)
      },
      headerInfo: {
        padding: '4px 0',
        flex: 1
      },
      headerAvatarHolder: {
        paddingRight: theme.spacing(1.5)
      },
      profileLink: {
        fontWeight: theme.typography.fontWeightBold,
        paddingRight: theme.spacing(0.5),
        color: theme.palette.text.primary
      },
      privacyBlock: {
        display: 'flex',
        flexDirection: 'row',
        alignItems: 'center',
        color: theme.palette.text.secondary
      },
      separateSpans: {
        display: 'flex',
        alignItems: 'center',
        '& span + span:before': {
          content: '"·"',
          display: 'inline-block',
          padding: `${theme.spacing(0, 0.5)}`
        }
      },
      photoReaction: {
        padding: theme.spacing(0, 2)
      },
      photoDescription: {
        padding: theme.spacing(2),
        fontSize: theme.mixins.pxToRem(15),
        color: theme.palette.text.primary
      },
      commentCompose: {
        backgroundColor: theme.palette.background.paper,
        padding: theme.spacing(0, 2)
      },
      info: {
        fontSize: theme.mixins.pxToRem(15),
        color: theme.palette.text.primary,
        padding: theme.spacing(2)
      }
    }),
  { name: 'MuiPhotoViewDetail' }
);

export default useStyles;

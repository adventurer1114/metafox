import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {},
      imgWraper: {
        height: 300,
        overflow: 'hidden'
      },
      wrapper: {
        display: 'block'
      },
      coverPhotoWrapper: {
        display: 'block',
        height: 320,
        overflow: 'hidden',
        position: 'relative'
      },
      coverPhotoInner: {
        display: 'block',
        position: 'absolute',
        left: 0,
        top: 0,
        right: 0,
        bottom: 0,
        '& img': {
          position: 'relative',
          width: '100%',
          objectFit: 'cover'
        }
      },
      userContainer: {
        padding: theme.spacing(2, 2, 3),
        backgroundColor: theme.mixins.backgroundColor('paper'),
        [theme.breakpoints.down('sm')]: {
          paddingBottom: 0
        }
      },
      userInfoContainer: {
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'flex-start',

        [theme.breakpoints.down('sm')]: {
          flexFlow: 'column',
          width: '100%',
          alignItems: 'center',
          paddingBottom: 0
        }
      },
      userInfo: {
        [theme.breakpoints.down('sm')]: {
          flexFlow: 'column',
          width: '100%',
          alignItems: 'center',
          marginBottom: theme.spacing(2)
        }
      },
      wrapperButtonInline: {
        display: 'flex',
        justifyContent: ' center',
        alignItems: 'center',
        flexFlow: 'column',
        width: 272,
        marginTop: theme.spacing(0.5),
        '& button': {
          height: theme.spacing(4),
          fontSize: theme.mixins.pxToRem(13),
          fontWeight: 'bold',
          width: '100%',
          '& .ico': {
            fontSize: theme.mixins.pxToRem(15)
          }
        },
        '& .MuiButton-contained': {
          color: theme.palette.text.primary,
          backgroundColor: theme.palette.action.selected,
          '&:hover': {
            backgroundColor: theme.palette.background.default
          },
          '&.MuiButton-containedPrimary': {
            color: '#fff',
            backgroundColor: theme.palette.primary.main,
            '&:hover': {
              backgroundColor: theme.palette.primary.dark
            }
          }
        },
        [theme.breakpoints.down('sm')]: {
          flexFlow: 'column wrap',
          minHeight: 40,
          padding: '4px 0px',
          width: '100%'
        }
      },
      link: {
        width: '100%',
        marginTop: theme.spacing(2),
        fontSize: theme.mixins.pxToRem(15),
        color: theme.palette.text.secondary,
        overflow: 'hidden',
        textOverflow: 'ellipsis',
        whiteSpace: 'nowrap'
      },
      title: {
        fontWeight: 'bold',
        fontSize: theme.mixins.pxToRem(32),
        color: theme.palette.text.primary,
        margin: 0,
        padding: 0
      },
      summary: {
        color: theme.palette.text.secondary,
        fontSize: theme.mixins.pxToRem(18),
        paddingTop: theme.spacing(1)
      },
      avatarWrapper: {
        marginTop: '-96px',
        marginRight: theme.spacing(3),
        position: 'relative',
        [theme.breakpoints.down('sm')]: {
          marginRight: 0
        }
      },
      userAvatar: {
        width: '168px',
        height: '168px',
        border: '4px solid',
        borderColor: theme.palette.background.paper
      },
      profileUserWrapper: {},
      wrapperMenu: {
        display: 'flex',
        flexGrow: 1,
        [theme.breakpoints.down('sm')]: {
          width: 'auto',
          maxWidth: '100%'
        }
      },
      profileMenu: {
        flex: 1,
        minWidth: 0,
        height: 72
      },
      profileHeaderContainer: {},
      profileHeaderBottom: {
        backgroundColor: theme.mixins.backgroundColor('paper'),
        borderTop: 'solid 1px',
        borderTopColor: theme.palette.border?.secondary,
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        borderBottomLeftRadius: '8px',
        borderBottomRightRadius: '8px',
        overflow: 'hidden',
        [theme.breakpoints.down('sm')]: {
          flexWrap: 'wrap',
          flexDirection: 'column-reverse',
          borderTop: 'none'
        }
      },
      actionButtons: {
        display: 'flex',
        paddingRight: theme.spacing(2),
        '& button': {
          marginLeft: theme.spacing(1),
          textTransform: 'capitalize',
          fontWeight: 'bold',
          whiteSpace: 'nowrap',
          borderRadius: theme.spacing(0.5),
          fontSize: theme.mixins.pxToRem(13),
          padding: theme.spacing(0.5, 1.25),
          marginBottom: theme.spacing(1),
          minWidth: theme.spacing(4),
          height: theme.spacing(4),
          '& .ico': {
            fontSize: theme.mixins.pxToRem(13)
          }
        },
        [theme.breakpoints.down('sm')]: {
          width: '100%',
          alignItems: 'center',
          justifyContent: 'flex-start',
          borderBottom: 'solid 1px',
          borderBottomColor: theme.palette.border?.secondary,
          '& button': {
            margin: theme.spacing(2, 0, 2, 2)
          },
          '& button + button': {
            marginLeft: theme.spacing(1)
          }
        }
      },
      userStickyWrapper: {
        display: 'flex',
        alignItems: 'center',
        height: 72
      },
      userAvatarSticky: {
        width: 48,
        height: 48,
        textTransform: 'uppercase'
      },
      userNameSticky: {
        fontSize: theme.mixins.pxToRem(18),
        fontWeight: 'bold',
        marginLeft: theme.spacing(1.5),
        WebkitLineClamp: 2,
        display: '-webkit-box',
        padding: '0',
        overflow: 'hidden',
        maxWidth: '100%',
        whiteSpace: 'normal',
        textOverflow: 'ellipsis',
        WebkitBoxOrient: 'vertical'
      }
    }),
  { name: 'PageProfileHeader' }
);

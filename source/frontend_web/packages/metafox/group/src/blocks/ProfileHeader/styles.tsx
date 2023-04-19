import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      profileUserWrapper: {},
      wrapperMenu: {
        display: 'flex',
        maxWidth: '100%',
        flexGrow: 1,
        [theme.breakpoints.down('sm')]: {
          width: 'auto',
          maxWidth: 'calc(100% - 60px)',
          paddingLeft: theme.spacing(2),
          flex: 1,
          minWidth: 0
        }
      },
      profileMenu: {
        flex: 1,
        minWidth: 0
      },
      profileHeaderContainer: {
        padding: 0
      },
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
        height: theme.spacing(9)
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
          alignItems: 'center',
          justifyContent: 'center',
          paddingRight: theme.spacing(1),
          '& button': {
            margin: theme.spacing(2, 1)
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
        height: 48
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
      },
      buttonJoin: {
        '&.Mui-disabled': {
          ...(theme.palette.mode === 'dark' && {
            color: `${theme.palette.text.hint} !important`
          })
        }
      }
    }),
  { name: 'GroupProfileHeader' }
);

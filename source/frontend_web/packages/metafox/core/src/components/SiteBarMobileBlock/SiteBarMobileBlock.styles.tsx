import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      blockHeader: {
        minHeight: 60
      },
      menuWrapper: {
        display: 'flex',
        height: 60,
        boxShadow: '0px 2px 1px 0 rgba(0, 0, 0, 0.05)',
        backgroundColor: theme.mixins.backgroundColor('paper'),
        position: 'fixed',
        left: 0,
        right: 0,
        top: 0,
        zIndex: 1300
      },
      menuButton: {
        flex: '1',
        minWidth: '64px',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        color: theme.palette.text.secondary,
        borderBottom: 'solid 2px #fff'
      },
      active: {
        color: `${theme.palette.primary.main} !important`,
        borderBottomColor: theme.palette.primary.main
      },
      menuButtonIcon: {
        width: '24px',
        height: '24px',
        fontSize: '24px'
      },
      menuInner: {
        position: 'relative'
      },
      number: {
        width: 20,
        height: 20,
        backgroundColor: theme.palette.error.main,
        color: '#fff',
        fontSize: theme.mixins.pxToRem(13),
        borderRadius: '50%',
        display: 'flex',
        justifyContent: 'center',
        position: 'absolute',
        top: -10,
        right: -10
      },
      dot: {
        width: 8,
        height: 8,
        backgroundColor: theme.palette.error.main,
        borderRadius: '50%',
        position: 'absolute',
        top: 0,
        right: -4
      },
      logo: {
        height: '35px',
        display: 'inline-block'
      },
      menuGuestWrapper: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'space-between',
        height: '60px',
        boxShadow: '0px 2px 1px 0 rgba(0, 0, 0, 0.05)',
        backgroundColor: theme.mixins.backgroundColor('paper'),
        padding: theme.spacing(0, 2),
        position: 'fixed',
        left: 0,
        right: 0,
        top: 0,
        zIndex: 2,
        '& $menuButton': {
          flex: 'none'
        }
      },
      button: {
        fontSize: 15,
        height: 32,
        padding: theme.spacing(0, 3),
        textTransform: 'capitalize'
      },
      popover: {
        '& .MuiPopover-paper': {
          maxWidth: '100%',
          width: '100%',
          borderRadius: 0,
          top: '60px !important',
          bottom: 0,
          boxShadow: 'none !important',
          borderTop: `solid 1px ${theme.palette.border?.secondary}`,
          marginTop: '-1px'
        }
      },
      dropdownMenuWrapper: {
        width: '100%',
        minHeight: 'calc(100vh - 60px)'
      },
      menuItem: {
        color: theme.palette.text.primary,
        padding: theme.spacing(1.5, 2)
      },
      menuItemLink: {
        padding: 0
      },
      link: {
        color: theme.palette.text.primary,
        textDecoration: 'none',
        padding: theme.spacing(1.5, 2),
        display: 'block',
        flexGrow: 1
      },
      icon: {
        textAlign: 'center',
        marginRight: theme.spacing(1.5)
      },
      toggleDarkMode: {
        fontSize: '32px',
        marginLeft: 'auto',
        lineHeight: '20px',
        width: '32px',
        height: '20px',
        color: theme.palette.text.disabled
      },
      userBlock: {
        display: 'flex',
        alignItems: 'center',
        padding: theme.spacing(2),
        borderBottom: `solid 1px ${theme.palette.border?.secondary}`
      },
      userAvatar: {
        width: 48,
        marginRight: theme.spacing(1)
      },
      userInner: {
        flex: 1,
        minWidth: 0
      },
      userName: {
        fontSize: theme.mixins.pxToRem(18),
        lineHeight: 1.5,
        color: theme.palette.text.primary,
        fontWeight: 'bold'
      },
      linkInfo: {
        fontSize: theme.mixins.pxToRem(15),
        lineHeight: 1,
        color: theme.palette.text.secondary
      },
      userAction: {
        marginLeft: 'auto',
        '& > .ico': {
          width: 32,
          height: 32,
          fontSize: 16,
          color: theme.palette.text.secondary,
          cursor: 'pointer',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          marginRight: -16
        }
      },
      profile: {},
      menuApp: {
        borderTop: 'solid 8px',
        borderTopColor: theme.palette.border?.secondary,
        paddingTop: 16,
        '& ul': {
          marginLeft: theme.spacing(-1),
          paddingBottom: theme.spacing(4)
        },
        '& ul > li': {
          marginLeft: 0,
          marginRight: 0,
          marginBottom: theme.spacing(1),
          paddingLeft: 0,
          '& > a': {
            display: 'flex',
            padding: theme.spacing(1),
            fontSize: '0.9375rem',
            alignItems: 'center',
            fontWeight: 'bold'
          }
        }
      },
      dialog: {
        padding: '0 !important'
      },
      searchMobile: {
        display: 'block',
        zIndex: '1301',
        position: 'absolute',
        top: '0',
        width: '100%',
        height: '100%',
        '& > div': {
          width: '100%'
        },
        '& form': {
          width: 'calc(100% - 96px)'
        }
      },
      cancelButton: {
        position: 'absolute',
        zIndex: '1',
        right: '16px',
        top: '50%',
        transform: 'translateY(-50%)'
      }
    }),
  { name: 'SiteBarMobileBlock' }
);

export default useStyles;

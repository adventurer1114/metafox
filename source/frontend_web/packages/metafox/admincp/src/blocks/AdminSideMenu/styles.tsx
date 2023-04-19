import { createStyles, makeStyles } from '@mui/styles';
import { Theme } from '@mui/material';

const ACTIVE_BG = '#354661';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      menuItem: {
        padding: '12px 16px',
        fontSize: 14,
        display: 'flex',
        alignItems: 'center',
        minHeight: 24,
        justifyContent: 'center',
        cursor: 'pointer',
        userSelect: 'none', // harmed user selection
        position: 'relative',
        '&:before': {
          content: '""',
          position: 'absolute',
          top: 0,
          bottom: 0,
          left: 0,
          width: '3px',
          background: 'transparent'
        },
        '&:hover': {
          backgroundColor: 'rgba(0, 0, 0, 0.08)',
          color: 'rgb(238, 238, 238)',
          '&:not($menuItemMinimize)': {
            '&:before': {
              background: theme.palette.common.white
            }
          }
        }
      },
      menuItemMinimize: {
        padding: '12px',
        '& $menuItemIcon': {
          margin: '0 !important',
          textAlign: 'center'
        }
      },
      menuItemMinimizeOpen: {
        '&:before': {
          background: theme.palette.common.white
        },
        '& $menuItemIcon': {
          color: theme.palette.common.white
        }
      },
      menuItemLink: {
        flex: 1,
        display: 'flex',
        alignItems: 'center',
        minHeight: 24,
        color: 'rgba(238,238,238,0.7)',
        cursor: 'pointer',
        '&:hover': {
          '& $menuItemIcon': {
            color: theme.palette.common.white
          },
          color: theme.palette.common.white
        }
      },
      menuItemActive: {
        backgroundColor: ACTIVE_BG,
        color: theme.palette.common.white,

        '& $menuItemLink': {
          color: theme.palette.common.white
        },
        '&$menuItemMinimize': {
          backgroundColor: `${ACTIVE_BG} !important`,
          color: 'rgb(255, 255, 255)',
          '& $menuItemIcon': {
            color: 'rgb(255, 255, 255) !important'
          }
        }
      },
      menuItemIcon: {
        width: 24,
        fontSize: 18,
        color: 'rgb(238, 238, 238,0.6)',
        marginRight: 8,
        display: 'inline-block'
      },
      iconArrow: {
        color: 'rgb(238, 238, 238,0.6)',
        fontSize: 13
      },
      subMenuItem: {},
      menuItemLinkPoper: {},
      menuItemLinkPoperParent: {},
      subMenuItemLink: {
        padding: '12px 16px 12px 48px',
        color: 'rgba(238, 238, 238, 0.7)',
        fontSize: 13,
        display: 'block',
        '&$menuItemLinkPoper': {
          padding: '12px 16px'
        },
        '&:not($menuItemLinkPoperParent):hover': {
          backgroundColor: 'rgba(0, 0, 0, 0.08)',
          color: theme.palette.common.white
        }
      },
      subMenuItemActive: {
        backgroundColor: ACTIVE_BG,
        color: theme.palette.common.white,
        fontWeight: 'bold',
        '& $subMenuItemLink': {
          color: theme.palette.common.white
        }
      }
    }),
  {
    name: 'AdminSideMenu'
  }
);

export default useStyles;

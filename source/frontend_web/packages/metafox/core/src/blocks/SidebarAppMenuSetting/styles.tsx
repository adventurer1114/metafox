import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {},
      menu: {},
      text: {
        flexGrow: 1
      },
      colapseIcon: {},
      icon: {
        color: '#fff',
        backgroundColor: theme.palette.primary.main,
        borderRadius: '32px',
        width: '32px',
        height: '32px',
        textAlign: 'center',
        marginRight: theme.spacing(2),
        lineHeight: '32px'
      },
      dropDownIcon: {},
      menuItemAvatar: {},
      menuItemIcon: {
        fontSize: theme.mixins.pxToRem(18),
        display: 'inline-block',
        textAlign: 'center',
        marginRight: theme.spacing(1),
        backgroundColor: '#e0dddd',
        borderRadius: '50%',
        lineHeight: '32px',
        width: theme.spacing(4)
      },
      menuItem: {
        height: theme.spacing(7),
        fontWeight: `${theme.typography.fontWeightRegular} !important`,
        margin: `${theme.spacing(0, 1)} !important`,
        borderRadius: `${theme.spacing(1)} !important`,
        padding: `${theme.spacing(1)} !important`,
        '& a': {
          padding: theme.spacing(0, 2)
        },
        '&:hover': {
          backgroundColor: theme.palette.action.selected,
          borderRadius: theme.shape.borderRadius
        }
      },
      menuItemText: {
        fontSize: theme.mixins.pxToRem(15),
        fontWeight: `${theme.typography.fontWeightRegular} !important`,
        '& .MuiListItemText-primary': {
          fontWeight: `${theme.typography.fontWeightRegular} !important`
        }
      },
      menuItemLink: {
        color:
          theme.palette.mode === 'light'
            ? theme.palette.text.primary
            : theme.palette.text.secondary,
        display: 'block',
        height: '100%',
        lineHeight: `${theme.spacing(7)}px`
      },
      activeMenuItem: {
        color: `${
          theme.palette.mode === 'dark'
            ? theme.palette.border.primary
            : theme.palette.primary.main
        } !important`,
        background: 'transparent !important',
        '& $menuItemLink': {
          color: theme.palette.primary.main,
          fontWeight: 'bold'
        },
        '& $menuItemIcon': {
          color: theme.palette.background.paper,
          backgroundColor: theme.palette.primary.main
        },
          '& $menuItemText .MuiListItemText-primary': {
            fontWeight: `${theme.typography.fontWeightMedium} !important`
          }
      },
      menuItemButton: {
        marginTop: theme.spacing(2),
        '& .ico': {
          marginRight: theme.spacing(1)
        }
      },
      buttonLink: {
        fontSize: theme.mixins.pxToRem(18),
        fontWeight: 'bold',
        height: theme.spacing(5)
      },
      menuHeading: {
        fontSize: `${theme.mixins.pxToRem(18)} !important`,
        lineHeight: '2 !important',
        fontWeight: 'bold !important',
        color: `${theme.palette.grey[600]} !important`,
        margin: `${theme.spacing(1, 0)} !important`
      },
      headerBlock: {
        fontSize: theme.mixins.pxToRem(18),
        fontWeight: 'bold',
        color:
          theme.palette.mode === 'light'
            ? theme.palette.text.secondary
            : theme.palette.text.primary,
        padding: theme.spacing(2, 0)
      }
    }),
  { name: 'SidebarAppMenu' }
);

export default useStyles;

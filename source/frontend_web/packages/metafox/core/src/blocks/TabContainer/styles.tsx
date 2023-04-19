import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {},
      disableGutter: {
        padding: 0
      },
      tab: {
        color: theme.palette.text.secondary,
        fontWeight: 'bold',
        fontSize: theme.mixins.pxToRem(15),
        padding: '0 !important',
        width: 'fit-content !important',
        minWidth: 'fit-content !important',
        textTransform: 'uppercase',
        '& + $tab': {
          marginLeft: theme.spacing(2.5)
        }
      },
      header: {
        width: '100%',
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'baseline'
      },
      subTabWrapper: {
        // fix FOXSOCIAL5-1827
        // height: theme.mixins.pxToRem(60),
        padding: theme.spacing(1.5, 0),
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        [theme.breakpoints.down('sm')]: {
          flexWrap: 'wrap',
          height: 'auto',
          padding: 0,
          '.MuiBox-root + .MuiBox-root > &': {
            paddingLeft: theme.spacing(2),
            paddingRight: theme.spacing(2)
          }
        }
      },
      popperMenu: {
        width: 240,
        boxShadow: theme.shadows[20],
        borderRadius: theme.shape.borderRadius,
        overflow: 'hidden',
        backgroundColor: theme.palette.background.paper,
        zIndex: 9999
      },
      menuItem: {
        width: 240,
        minHeight: '40px',
        display: 'block',
        padding: theme.spacing(1, 2),
        alignItems: 'center',
        justifyContent: 'center',
        textDecoration: 'none',
        textTransform: 'uppercase',
        fontSize: '15px',
        color: theme.palette.text.secondary,
        '&:hover': {
          textDecoration: 'none !important',
          backgroundColor: theme.palette.action.hover,
          cursor: 'pointer'
        }
      },
      secondMenu: {
        listStyle: 'none none outside',
        margin: 0,
        padding: 0,
        display: 'inline-flex'
      },
      tabItem: {
        height: '100%',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        float: 'left',
        textDecoration: 'none',
        textTransform: 'uppercase',
        fontSize: '15px',
        fontWeight: 'bold',
        color: `${theme.palette.text.secondary} !important`,
        position: 'relative',
        whiteSpace: 'nowrap',
        '&:hover': {
          textDecoration: 'none',
          color: `${theme.palette.primary.main} !important`
        },
        [theme.breakpoints.down('xs')]: {
          padding: `26px ${theme.spacing(1)}px`,
          marginBottom: 0
        },
        minWidth: 60,
        cursor: 'pointer',
        marginRight: 0,
        flexGrow: 1
      },
      tabSelect: {
        padding: `${theme.spacing(1)} ${theme.spacing(2)} `
      },
      tabItemActive: {
        color: `${theme.palette.primary.main} !important`
      },
      hiddenTabs: {
        visibility: 'hidden',
        position: 'absolute'
      }
    }),
  { name: 'TabMenuBlock' }
);

export default useStyles;

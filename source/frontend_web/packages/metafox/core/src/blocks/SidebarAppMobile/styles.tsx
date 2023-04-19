import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {},
      headerBlock: {
        width: '100%',
        display: 'flex',
        justifyContent: 'space-between',
        backgroundColor: theme.palette.background.paper,
        padding: theme.spacing(2, 2)
      },
      title: {
        display: 'flex',
        alignItems: 'center',
        fontSize: theme.mixins.pxToRem(24),
        fontWeight: theme.typography.fontWeightMedium
      },
      btn: {
        minWidth: theme.mixins.pxToRem(32),
        padding: 0
      },
      contentWrapper: {
        width: '100%',
        marginTop: theme.spacing(-1)
      },
      popover: {
        paddingTop: theme.mixins.pxToRem(16),
        '& .MuiPopover-paper': {
          maxWidth: '100%',
          width: '100%',
          borderRadius: 0,
          top: '120px !important',
          bottom: 0,
          left: '0 !important'
        },
        '& .MuiPaper-elevation8': {
          overflow: 'hidden',
          boxShadow: 'unset'
        }
      }
    }),
  { name: 'SidebarAppMobile' }
);

export default useStyles;

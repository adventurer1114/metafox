import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        backgroundColor: theme.palette.primary.main,
        minHeight: '100vh',
        display: 'flex',
        alignItems: 'center'
      },
      container: {
        display: 'flex',
        minHeight: '100vh',
        flexFlow: 'column',
        maxWidth: '1074px'
      },
      containerGrid: {
        backgroundColor: theme.palette.background.paper,
        margin: 'auto',
        marginBottom: theme.spacing(5),
        borderRadius: theme.shape.borderRadius,
        backgroundPosition: 'bottom left',
        backgroundRepeat: 'no-repeat',
        [theme.breakpoints.down('md')]: {
          backgroundImage: 'none! important'
        }
      },
      multipleAccess: {
        position: 'relative'
      },
      signedIn: {},
      welcomeContent: {
        backgroundPosition: 'bottom left',
        backgroundRepeat: 'no-repeat',
        display: 'flex',
        height: '100%',
        width: '100%',
        flexFlow: 'column'
      },
      gridLeft: {
        padding: theme.spacing(2),
        marginBottom: theme.spacing(4),
        [theme.breakpoints.up('sm')]: {
          borderRight: 'solid 1px',
          borderRightColor: theme.palette.border?.secondary,
          padding: theme.spacing(6),
          marginBottom: 0
        }
      },
      logo: {
        maxWidth: '127px'
      },
      title: {
        fontWeight: 'bold',
        marginBottom: theme.spacing(2)
      },
      subTitle: {
        paddingTop: theme.spacing(2)
      },
      formContent: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        [theme.breakpoints.up('md')]: {
          padding: theme.spacing(14, 0)
        },
        [theme.breakpoints.down('md')]: {
          padding: theme.spacing(0, 0, 3)
        }
      },
      userItem: {
        marginRight: theme.spacing(3)
      },
      displayLimit: {
        position: 'relative'
      },
      contentHeader: {
        flexGrow: 1
      }
    }),
  { name: 'LoginContent' }
);

export default useStyles;

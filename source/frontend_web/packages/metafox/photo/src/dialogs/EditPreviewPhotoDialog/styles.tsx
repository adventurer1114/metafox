import { Theme } from '@mui/material';
import { createStyles } from '@mui/styles';

const styles = (theme: Theme) =>
  createStyles({
    dialogTitle: {},
    dialogContent: {
      padding: 0,
      display: 'flex',
      flexDirection: 'row'
    },
    dialogActions: {},
    leftSide: {
      width: 360,
      display: 'flex',
      flexDirection: 'column',
      padding: `${theme.spacing(2)} ${theme.spacing(2)} ${theme.spacing(2)} 0`
    },
    leftSideActions: {
      display: 'flex',
      flex: 1,
      flexDirection: 'column',
      padding: theme.spacing(2, 0)
    },
    leftSideBottom: {
      display: 'flex',
      flexDirection: 'row',
      '& > :first-of-type': {
        marginRight: theme.spacing(1)
      }
    },
    mainContent: {
      display: 'flex',
      flexDirection: 'column',
      backgroundColor: theme.palette.background.default,
      width: 720,
      height: 600,
      position: 'relative',
      textAlign: 'center'
    },
    mask: {
      position: 'absolute',
      left: 0,
      right: 0,
      top: 0,
      bottom: 0,
      backgroundColor: 'rgba(0,0,0,0.6)'
    },
    backdrop: {
      backgroundColor: theme.mixins.backgroundColor('paper'),
      textAlign: 'center',
      overflow: 'hidden',
      display: 'flex',
      justifyContent: 'center',
      alignItems: 'center',
      position: 'absolute',
      left: 0,
      right: 0,
      top: 0,
      bottom: 0
    },
    blurContainer: {
      width: '10%',
      height: '10%',
      transform: 'scale(14)',
      filter: 'blur(3px)',
      WebkitFilter: 'blur(3px)'
    },
    blurImg: {
      objectFit: 'cover',
      width: '100%',
      height: '100%',
      border: 0
    },
    mediaContainer: {
      position: 'relative',
      maxWidth: '100%',
      height: '100%',
      overflow: 'hidden',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      flexDirection: 'column',
      padding: theme.spacing(2),
      flexGrow: 1
    },
    mediaWrapper: {
      maxHeight: '100%',
      position: 'relative',
      justifyContent: 'center',
      alignItems: 'center',
      display: 'flex'
    },
    largeImage: {
      maxWidth: '100%',
      maxHeight: '100%',
      objectFit: 'contain',
      position: 'relative'
    },
    largeImageCropping: {},
    imageError: {
      color: 'white',
      fontSize: theme.mixins.pxToRem(20)
    }
  });

export default styles;

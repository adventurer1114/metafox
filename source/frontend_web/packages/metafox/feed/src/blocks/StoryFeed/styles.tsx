import { createStyles, makeStyles } from '@mui/styles';
import { Theme } from '@mui/material';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {},
      storyWrapper: {
        display: 'flex',
        margin: theme.spacing(0, -0.5),
        overflowX: 'hidden'
      },
      active: {},
      storyItem: {
        margin: theme.spacing(0, 0.5),
        width: 76,
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'center',
        alignItems: 'center',
        '&$active': {
          '& $avatar': {
            borderColor: theme.palette.primary.main,
            '& img': {
              borderColor: '#fff'
            }
          }
        },
        '&:hover $userName': {
          color: theme.palette.primary.main
        }
      },
      avatar: {
        width: 76,
        height: 76,
        border: 'solid 2px',
        borderColor: 'transparent',
        '& img': {
          borderRadius: '50%',
          border: 'solid 2px',
          borderColor: 'transparent'
        }
      },
      userName: {
        marginTop: theme.spacing(0.5),
        color: theme.palette.text.primary,
        textAlign: 'center',
        width: '100%',
        whiteSpace: 'nowrap',
        overflow: 'hidden',
        textOverflow: 'ellipsis'
      },
      addStory: {
        position: 'relative',
        '& $userName': {
          color: theme.palette.primary.main
        }
      },
      iconPlus: {
        position: 'absolute',
        top: theme.spacing(6.25),
        right: theme.spacing(0.5),
        borderRadius: '50%',
        width: 24,
        height: 24,
        background: theme.palette.primary.main,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        color: '#fff',
        border: 'solid 2px',
        borderColor: theme.palette.background.paper,
        fontSize: theme.mixins.pxToRem(12)
      }
    }),
  {
    name: 'Story'
  }
);

export default useStyles;

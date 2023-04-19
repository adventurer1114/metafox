import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        display: 'block',
        position: 'relative',
        overflow: 'hidden',
        margin: theme.spacing(1.5)
      },
      listContainer: {
        position: 'relative',
        display: 'flex',
        flexWrap: 'wrap'
      },
      itemRoot: {
        position: 'relative',
        display: 'flex',
        flexBasis: '50%',
        padding: theme.spacing(0.5)
      },
      item0: {},
      item1: {},
      item2: {},
      item3: {},
      item4: {},
      videoItem: {
        width: '100%',
        maxWidth: '100%',
        borderRadius: theme.spacing(1)
      },
      item: {
        display: 'block',
        padding: theme.spacing(0.25)
      },
      preset1: {
        '& $item0': {
          flexBasis: '100%'
        }
      },
      preset2: {},
      preset3: {
        '& $item0': {
          flexBasis: '100%'
        }
      },
      preset4: {},
      removeBtn: {
        position: 'absolute !important',
        top: theme.spacing(2),
        right: theme.spacing(2),
        zIndex: 1,
        opacity: 0.9
      },
      remainBackdrop: {
        position: 'absolute',
        left: 0,
        right: 0,
        top: 0,
        bottom: 0,
        backgroundColor: 'rgba(0,0,0,0.3)',
        borderRadius: theme.shape.borderRadius,
        '&:hover': {
          backgroundColor: 'rgba(0,0,0,0.1)'
        }
      },
      remainText: {
        color: 'white',
        position: 'absolute',
        left: '50%',
        top: '50%',
        fontSize: '2rem',
        transform: 'translate(-50%,-50%)'
      },
      actionBar: {
        position: 'absolute',
        top: 0,
        right: 0,
        left: 0,
        display: 'flex',
        flexDirection: 'row',
        padding: theme.spacing(2),
        justifyContent: 'space-between'
      },
      buttonGroup: {
        '& > *': {
          marginRight: `${theme.spacing(1)} !important`
        },
        [theme.breakpoints.down('sm')]: {
          display: 'flex',
          flexDirection: 'column',
          margin: theme.spacing(-1.5, -1, -1),
          '& button': {
            marginTop: theme.spacing(0.5)
          }
        }
      }
    }),
  { name: 'PhotoPreviews' }
);

export default useStyles;

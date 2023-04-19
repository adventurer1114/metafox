import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        backgroundColor: theme.mixins.backgroundColor('paper'),
        [theme.breakpoints.down('sm')]: {
          '& $bgCover': {
            height: 179
          },
          '& $viewContainer': {
            borderRadius: 0,
            marginTop: '0 !important'
          }
        }
      },
      bgCover: {
        backgroundRepeat: 'no-repeat',
        backgroundPosition: 'center',
        backgroundSize: 'cover',
        height: 320
      },
      hasBgCover: {},
      viewContainer: {
        width: '100%',
        backgroundColor: theme.mixins.backgroundColor('paper'),
        padding: theme.spacing(2),
        position: 'relative'
      },
      actionMenu: {
        width: 32,
        height: 32,
        position: 'absolute !important',
        top: theme.spacing(-1),
        right: theme.spacing(-1),
        '& .ico': {
          color: theme.palette.text.secondary,
          fontSize: 13
        },
        '& button:hover': {
          backgroundColor: theme.palette.action.selected
        }
      },
      pendingNoticeWrapper: {
        padding: theme.spacing(0, 0, 0.5, 0),
        marginBottom: theme.spacing(2)
      },
      pendingNotice: {
        borderRadius: theme.spacing(1),
        height: 48,
        width: 'auto',
        backgroundColor: theme.palette.action.selected,
        display: 'flex',
        alignItems: 'center',
        padding: theme.spacing(2),
        justifyContent: 'space-between'
      },
      pendingTitle: {
        fontSize: theme.mixins.pxToRem(15),
        color: theme.palette.text.secondary
      },
      pendingAction: {
        display: 'flex'
      },
      pendingButton: {
        fontSize: theme.mixins.pxToRem(15),
        color: theme.palette.primary.main,
        textTransform: 'uppercase',
        marginLeft: theme.spacing(2),
        cursor: 'pointer',
        fontWeight: theme.typography.fontWeightBold,
        '&:hover': {
          color: theme.palette.primary.light
        }
      },
      contentWrapper: {
        position: 'relative'
      },
      titleWrapper: {
        paddingRight: theme.spacing(2)
      },
      itemFlag: {
        display: 'inline-flex',
        margin: theme.spacing(0, 0.5, 0, -0.5)
      },
      viewTitle: {
        fontSize: theme.spacing(3),
        lineHeight: 1,
        fontWeight: theme.typography.fontWeightBold,
        display: 'inline',
        verticalAlign: 'middle'
      },
      author: {
        display: 'flex',
        marginTop: theme.spacing(2)
      },
      authorInfo: {
        marginLeft: theme.spacing(1.2)
      },
      userName: {
        fontSize: 15,
        fontWeight: 'bold',
        color: theme.palette.text.primary,
        display: 'block'
      },
      date: {
        fontSize: 13,
        color: theme.palette.text.secondary,
        marginTop: theme.spacing(0.5)
      },
      itemContent: {
        fontSize: 15,
        lineHeight: 1.33,
        marginTop: theme.spacing(3),
        '& p + p': {
          marginBottom: theme.spacing(2.5)
        }
      },
      voteForm: {
        [theme.breakpoints.up('sm')]: {
          maxWidth: 320
        }
      },
      attachmentTitle: {
        fontSize: theme.mixins.pxToRem(18),
        marginTop: theme.spacing(4),
        color: theme.palette.text.secondary,
        fontWeight: theme.typography.fontWeightBold
      },
      attachment: {
        width: '100%',
        display: 'flex',
        flexWrap: 'wrap',
        marginTop: theme.spacing(2),
        justifyContent: 'space-between'
      },
      attachmentItem: {
        marginTop: theme.spacing(2),
        flexGrow: 0,
        flexShrink: 0,
        flexBasis: 'calc(50% - 8px)',
        minWidth: 300
      }
    }),
  { name: 'MuiPollViewDetail' }
);

export default useStyles;

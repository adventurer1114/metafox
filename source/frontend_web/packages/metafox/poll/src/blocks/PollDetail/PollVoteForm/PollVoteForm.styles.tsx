import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        border: theme.mixins.border('secondary'),
        borderRadius: theme.shape.borderRadius,
        marginTop: theme.spacing(2),
        padding: theme.spacing(2)
      },
      answerWrapper: {},
      answerItem: {
        marginLeft: 0,
        marginBottom: theme.spacing(1),
        '&:last-child': {
          marginBottom: theme.spacing(0)
        }
      },
      answerItemChecked: {
        color: theme.palette.text.primary
      },
      radioAnswer: {
        padding: theme.spacing(0, 1)
      },
      radioAnswerChecked: {
        color: `${theme.palette.primary.main} !important`
      },
      votedAnswer: {
        fontWeight: 'bold',
        color: theme.palette.text.primary
      },
      btnToggle: {
        color: theme.palette.text.primary,
        fontWeight: 'bold',
        '&:hover': {
          textDecoration: 'underline'
        }
      },
      formControl: {},
      button: {
        fontWeight: 'bold',
        textTransform: 'capitalize'
      },
      cancelButton: {
        fontWeight: 'bold',
        marginLeft: theme.spacing(1)
      },
      progressAnswerWrapper: {
        paddingRight: theme.spacing(1),
        '&:last-child': {
          marginBottom: theme.spacing(0)
        }
      },
      progressAnswer: {
        color: theme.palette.text.hint,
        marginBottom: theme.spacing(1),
        '&:last-child': {
          marginBottom: theme.spacing(0)
        }
      },
      answerLabel: {},
      yourAnswer: {
        fontWeight: 'bold'
      },
      progressItem: {
        height: theme.spacing(2.5),
        display: 'flex',
        alignItems: 'center'
      },
      progress: {
        flex: 1,
        minWidth: 0,
        margin: 0,
        marginRight: theme.spacing(1),
        height: `${theme.spacing(1)} !important`,
        borderRadius: theme.spacing(0.5),
        backgroundColor: theme.palette.action.selected,
        '& > div': {
          borderRadius: theme.spacing(0.5)
        }
      },
      progressPercent: {
        width: 20,
        marginLeft: theme.spacing(1),
        color: theme.palette.text.secondary,
        fontSize: theme.mixins.pxToRem(13)
      },
      voteStatistic: {
        marginTop: theme.spacing(2),
        color: theme.palette.text.hint,
        fontSize: theme.mixins.pxToRem(13),
        display: 'flex'
      },
      buttonWrapper: {
        display: 'flex',
        marginTop: theme.spacing(2)
      },
      totalVote: {
        '&:hover': {
          textDecoration: 'underline'
        }
      },
      activeTotalVote: {
        fontSize: theme.mixins.pxToRem(13),
        color: theme.palette.primary.main,
        cursor: 'pointer',
        '&:hover': {
          textDecoration: 'underline'
        }
      },
      timeLeft: {
        marginLeft: theme.spacing(2)
      },
      buttonWrapperVoteCancel: {
        display: 'flex'
      },
      noShowAnswer: {
        display: 'flex',
        alignItems: 'center',
        paddingBottom: theme.spacing(2),
        '&:last-child': {
          paddingBottom: 0
        }
      },
      iconNoShowAnswer: {
        padding: theme.spacing(1, 1, 1, 0),
        fontSize: theme.mixins.pxToRem(16)
      }
    }),
  { name: 'PollVoteForm' }
);

export default useStyles;

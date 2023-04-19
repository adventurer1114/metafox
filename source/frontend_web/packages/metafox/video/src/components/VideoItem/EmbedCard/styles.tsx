import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      title: {
        '& a': {
          color: theme.palette.text.primary
        }
      },
      description: {
        color: theme.palette.text.secondary,
        '& p': {
          margin: 0
        }
      },
      subInfo: {
        textTransform: 'uppercase'
      },
      itemInner: {
        borderBottomLeftRadius: '8px',
        borderBottomRightRadius: '8px',
        border: theme.mixins.border('secondary'),
        borderTop: 'none',
        padding: theme.spacing(3),
        display: 'flex',
        flexDirection: 'column'
      },
      totalView: {
        color: theme.palette.text.secondary
      },
      price: {
        fontWeight: theme.typography.fontWeightBold,
        color: theme.palette.warning.main
      },
      wrapperInfoFlag: {
        marginTop: 'auto'
      },
      flagWrapper: {
        marginLeft: 'auto'
      }
    }),
  { name: 'VideoEmbedView' }
);

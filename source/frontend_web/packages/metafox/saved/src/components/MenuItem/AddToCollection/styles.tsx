import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        width: 225
      },
      heading: {
        textTransform: 'uppercase',
        color: theme.palette.text.secondary,
        backgroundColor: theme.palette.background.default,
        fontWeight: 'bold',
        padding: theme.spacing(1, 1.5)
      },
      headingCollapse: {
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        cursor: 'pointer'
      },
      listCollection: {
        display: 'flex',
        flexDirection: 'column',
        maxHeight: 200,
        overflowY: 'auto'
      },
      itemWrapper: {
        width: '100%',
        margin: '0 !important',
        padding: theme.spacing(0.5, 1, 0.5, 0),
        position: 'relative',
        '& + $itemWrapper:before': {
          content: '""',
          borderTop: 'solid 1px',
          borderColor: theme.palette.border?.secondary,
          position: 'absolute',
          top: 0,
          left: theme.spacing(2),
          right: theme.spacing(2)
        }
      },
      createForm: {
        backgroundColor: theme.palette.background.default,
        padding: theme.spacing(2)
      },
      input: {
        '& .MuiInputBase-root': {
          borderRadius: 4,
          backgroundColor: theme.mixins.backgroundColor('paper'),
          height: theme.spacing(4)
        }
      },
      actions: {
        marginTop: theme.spacing(1),
        display: 'flex',
        '& button': {
          flex: 1,
          minWidth: 0
        },
        '& button + button': {
          marginLeft: theme.spacing(1)
        }
      }
    }),
  {
    name: 'MenuItemAddToCollection'
  }
);

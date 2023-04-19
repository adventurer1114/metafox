import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles((theme: Theme) =>
  createStyles({
    item: {
      '& $link': {
        height: theme.spacing(7),
        padding: theme.spacing(0, 2, 0, 2),
        flex: 1,
        minWidth: 0,
        '&:hover': {
          backgroundColor: theme.palette.action.selected,
          borderRadius: theme.shape.borderRadius
        }
      }
    },
    hasSubs: {
      display: 'flex',
      alignItems: 'center',
      '& button': {
        position: 'absolute'
      },
      '& .ico': {
        paddingLeft: 0
      }
    },
    link: {
      cursor: 'pointer',
      fontSize: theme.mixins.pxToRem(15),
      display: 'flex',
      flexDirection: 'row',
      alignItems: 'center',
      color:
        theme.palette.mode === 'light'
          ? theme.palette.text.primary
          : theme.palette.text.secondary,
      textDecoration: 'none',
      position: 'relative'
    },
    itemActive: {
      '& $link': {
        color: theme.palette.primary.main,
        fontWeight: theme.typography.fontWeightBold
      }
    },
    span: {},
    subCategory: {
      borderLeft: `1px solid ${theme.palette.border.secondary}`,
      listStyle: 'none',
      margin: theme.spacing(0, 0, 0, 2),
      padding: 0
    },
    icon: {
      position: 'absolute',
      width: 32,
      height: 32,
      right: theme.spacing(1),
      '& .ico': {
        fontSize: theme.mixins.pxToRem(15)
      }
    }
  })
);

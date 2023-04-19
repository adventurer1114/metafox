import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        height: 64,
        position: 'relative',
        backgroundColor: theme.palette.background.paper,
        '&:hover': {
          backgroundColor: theme.palette.action.hover
        }
      },
      itemOuter: {
        height: '100%'
      },
      itemInner: {
        height: '100%',
        display: 'flex',
        margin: theme.spacing(0, 2, 0, 2),
        borderBottom: 'solid 1px',
        borderBottomColor: theme.palette.border?.secondary
      },
      itemMedia: {
        width: theme.spacing(4),
        height: '100%',
        display: 'flex',
        alignItems: 'center',
        borderRadius: '100%'
      },
      imgWrapper: {
        borderRadius: '100%'
      },
      itemInfo: {
        marginLeft: theme.spacing(1.5),
        display: 'flex',
        alignItems: 'center'
      },
      button: {
        display: 'flex',
        height: '100%',
        alignItems: 'center',
        position: 'absolute',
        right: theme.spacing(2),
        top: 0
      },
      itemTitle: {
        fontSize: theme.mixins.pxToRem(15),
        color: theme.palette.text.primary,
        whiteSpace: 'nowrap',
        overflow: 'hidden',
        textOverflow: 'ellipsis',
        fontWeight: theme.typography.fontWeightBold,
        minHeight: 15,
        minWidth: 100
      },
      unhideButton: {
        fontSize: theme.mixins.pxToRem(13),
        fontWeight: 'bold'
      },
      undoButton: {
        fontSize: theme.mixins.pxToRem(13),
        fontWeight: 'bold',
        color: theme.palette.text.primary,
        borderColor: theme.palette.border?.secondary,
        '&:hover': {
          backgroundColor: theme.palette.action.hover
        }
      }
    }),
  { name: 'UserItem_HiddenUser' }
);

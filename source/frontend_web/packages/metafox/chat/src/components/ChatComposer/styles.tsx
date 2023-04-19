import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        width: '100%'
      },
      composeOuter: {
        display: 'flex',
        padding: theme.spacing(0.5, 0.5, 0.5, 2),
        minHeight: theme.spacing(4.25),
        alignItems: 'center',
        overflowY: 'auto',
        height: '100%'
      },
      'composeOuter-dense': {
        padding: theme.spacing(0.5)
      },
      composeInner: {
        flex: 1,
        minWidth: 0
      },
      composeInputWrapper: {
        width: '100%',
        flexFlow: 'wrap',
        display: 'flex',
        alignItems: 'center'
        // minHeight: theme.spacing(4.25),
        // borderRadius: theme.spacing(2),
        // backgroundColor: theme.palette.action.hover,
        // border: theme.mixins.border('secondary'),
      },
      expand: {
        '& $composeInputWrapper': {
          flex: 'none',
          width: '100%'
        }
      },
      composer: {
        // padding: '6px 8px 0px 12px',
        maxHeight: '200px',
        overflowY: 'auto',
        minHeight: 0,
        flex: 1,
        flexBasis: 'auto',
        minWidth: 0,
        display: 'flex',
        ' & .DraftEditor-root': {
          width: '100%'
        },
        '& .public-DraftEditorPlaceholder-root': {
          position: 'absolute',
          color: theme.palette.text.secondary,
          fontSize: theme.mixins.pxToRem(15)
        },
        '& .DraftEditor-editorContainer': {
          minWidth: theme.spacing(2),
          fontSize: theme.mixins.pxToRem(15)
        },
        '& .mentionSuggestionsWrapper': {
          position: 'absolute',
          zIndex: 1,
          minWidth: 180,
          backgroundColor: theme.mixins.backgroundColor('paper'),
          borderRadius: 4,
          padding: theme.spacing(1),
          marginTop: theme.spacing(1),
          boxShadow:
            '0px 10px 13px -6px rgba(0,0,0,0.2), 0px 20px 31px 3px rgba(0,0,0,0.14)'
        },
        '& .mentionText': {
          color: theme.palette.primary.main
        },
        '& .hashtagStyle': {
          color: theme.palette.primary.main
        }
      },
      attachIconsWrapper: {
        display: 'inline-flex',
        alignItems: 'flex-end',
        padding: '0 2px',
        marginLeft: 'auto',
        '& .ico-smile-o': {
          fontSize: theme.mixins.pxToRem(15)
        }
      },
      attachBtn: {
        padding: 0,
        display: 'inline-flex',
        alignItems: 'center',
        justifyContent: 'center',
        width: theme.spacing(3.5),
        height: theme.spacing(3.5),
        minWidth: theme.spacing(3.5),
        color: theme.palette.text.secondary
      },
      attachBtnIcon: {
        fontSize: '14px'
      },
      extraDataPhoto: {
        maxWidth: theme.spacing(26)
      },
      extraDataSticker: {
        width: theme.spacing(10),
        height: theme.spacing(10)
      },
      cancelBtn: {
        fontSize: 'small',
        color: theme.palette.text.secondary,
        marginLeft: theme.spacing(5),
        marginBottom: theme.spacing(1),
        '& span': {
          color: theme.palette.primary.main
        }
      }
    }),
  { name: 'ChatCompose' }
);

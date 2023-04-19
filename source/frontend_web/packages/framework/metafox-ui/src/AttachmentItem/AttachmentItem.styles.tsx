import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      attachmentWrapper: {
        borderRadius: theme.spacing(1),
        border: theme.mixins.border('secondary'),
        display: 'flex',
        width: '100%',
        position: 'relative',
        alignItems: 'center'
      },
      attachmentPhoto: {
        width: 112,
        marginRight: theme.spacing(2),
        borderRadius: theme.spacing(1, 0, 0, 1),
        textAlign: 'center',
        overflow: 'hidden'
      },
      largeSize: {
        height: 112
      },
      miniSize: {
        '& $attachmentPhoto': {
          width: 64
        },
        '& $downloadButton': {
          width: 32,
          height: 32
        },
        '& $downloadIcon': {
          fontSize: theme.mixins.pxToRem(14)
        }
      },
      fileName: {
        fontWeight: theme.typography.fontWeightBold,
        fontSize: theme.mixins.pxToRem(15),
        width: '100%'
      },
      statistic: {
        width: 'calc(100% - 200px)',
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'space-around',
        height: 48
      },
      fileSize: {
        color: theme.palette.text.secondary,
        fontSize: theme.mixins.pxToRem(13)
      },
      downloadButton: {
        borderRadius: theme.spacing(0.5),
        border: theme.mixins.border('primary'),
        width: 40,
        height: 40,
        display: 'flex',
        alignContent: 'space-around',
        flexWrap: 'wrap',
        justifyContent: 'center',
        position: 'absolute',
        right: theme.spacing(2)
      },
      largeDownloadButton: {
        right: theme.spacing(4)
      },
      downloadIcon: {
        fontSize: theme.mixins.pxToRem(18),
        color: theme.palette.primary.main,
        marginTop: 'auto',
        marginBottom: 'auto'
      },
      attachmentTypeIcon: {
        margin: theme.spacing(0, 2, 0, 1),
        fontSize: theme.mixins.pxToRem(48),
        color: theme.palette.text.secondary
      }
    }),
  { name: 'AttachmentItem' }
);

export default useStyles;

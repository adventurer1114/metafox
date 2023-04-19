import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        margin: theme.spacing(2, 0, 1)
      },
      formLabel: {
        fontSize: theme.mixins.pxToRem(13)
      },
      preview: {
        marginTop: -5,
        height: 120,
        borderRadius: 4,
        overflow: 'hidden',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        position: 'relative',
        '& button': {
          '& + button': {
            marginLeft: theme.spacing(1)
          }
        }
      },
      previewImage: {
        position: 'absolute',
        top: 0,
        bottom: 0,
        left: 0,
        right: 0,
        backgroundRepeat: 'no-repeat',
        backgroundSize: 'cover',
        backgroundPosition: 'center'
      },
      controls: {
        height: 120,
        borderRadius: 4,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center'
      },
      actions: {}
    }),
  {
    name: 'ItemPhotoField'
  }
);

import { createStyles, makeStyles } from '@mui/styles';
import { Theme } from '@mui/material';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {},
      mediaLink: {
        display: 'block',
        float: 'left',
        '&:after': {
          content: '""',
          display: 'block',
          backgroundImage:
            '-webkit-linear-gradient(90deg, rgba(0,0,0,0.8) 0, rgba(0,0,0,0.10196) 100%)',
          transition: 'opacity 0.3s',
          position: 'absolute',
          bottom: 0,
          left: 0,
          right: 0,
          opacity: 0,
          top: 0
        }
      },
      pinItem: {
        position: 'relative',
        flexBasis: 0,
        display: 'flex',
        flexDirection: 'column',
        minWidth: '200px',
        flexGrow: 1,
        '&:hover $mediaLink:after': {
          opacity: 1
        },
        '&:hover': {
          '& $photoInfo': {
            opacity: 1,
            zIndex: 4
          }
        }
      },
      image: {
        maxWidth: '100%',
        maxHeight: '100%',
        minWidth: '100%',
        float: 'left',
        display: 'block'
      },
      photoActions: {
        position: 'absolute',
        bottom: theme.spacing(1),
        right: theme.spacing(2),
        width: '32px',
        height: '32px',
        lineHeight: '30px',
        backgroundColor: 'rgba(0,0,0,.4)',
        textAlign: 'center',
        borderRadius: '100%',
        zIndex: 4
      },
      photoActionsDropdown: {
        color: '#fff',
        fontSize: theme.mixins.pxToRem(13),
        padding: theme.spacing(1)
      },
      iconButton: {},
      features: {
        position: 'absolute',
        top: theme.spacing(2),
        right: '-2px',
        zIndex: 1,
        display: 'flex',
        flexFlow: 'column',
        alignItems: 'flex-end'
      },
      btnRemove: {
        position: 'absolute',
        bottom: 0,
        zIndex: 2
      },
      photoInfo: {
        position: 'absolute',
        bottom: 0,
        left: 0,
        right: 0,
        padding: theme.spacing(2),
        opacity: 0,
        color: '#fff'
      },
      photoTitle: {
        marginBottom: theme.spacing(1.5),
        whiteSpace: 'nowrap',
        textOverflow: 'ellipsis',
        overflow: 'hidden'
      },
      photoLike: {}
    }),
  { name: 'MuiPhotoPinItemView' }
);

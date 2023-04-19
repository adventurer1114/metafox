import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        display: 'block'
      },
      'bottomSpacing-normal': {
        paddingBottom: theme.spacing(2)
      },
      'bottomSpacing-dense': {
        paddingBottom: theme.spacing(0)
      },
      itemOuter: {
        display: 'flex',
        borderRadius: '8px',
        border: theme.mixins.border('secondary'),
        backgroundColor: theme.mixins.backgroundColor('paper'),
        overflow: 'hidden'
      },
      grid: {
        '& $itemOuter': {
          flexDirection: 'column',
          '& $media': {
            width: '100%'
          }
        }
      },
      list: {
        '& $itemOuter': {
          flexDirection: 'row',
          [theme.breakpoints.down('sm')]: {
            flexDirection: 'column',
            '& .media': {
              width: '100% !important'
            }
          }
        }
      }
    }),
  { name: 'MuiFeedEmbedCard' }
);

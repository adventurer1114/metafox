import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      imageThumbnail: {
        width: '100%',
        height: '100%'
      }
    }),
  { name: 'VideoPlayer' }
);

export default useStyles;

import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      textInfo: {
        fontSize: theme.mixins.pxToRem(15),
        color: theme.palette.text.primary,
        marginBottom: theme.spacing(2)
      }
    }),
  { name: 'MuiUserProfileDetailsPage' }
);

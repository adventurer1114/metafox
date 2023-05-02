import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';

export default makeStyles(
  (theme: Theme) =>
    createStyles({
      collectionRoot: {},
      collectionTitle: {
        fontWeight: theme.typography.fontWeightBold,
        fontSize: '1.5rem',
        padding: theme.spacing(2, 0)
      },
      collectionContent: {},
      itemRoot: {
        cursor: 'pointer',
        paddingBottom: '60%',
        position: 'relative'
      },
      itemBg: {
        position: 'absolute',
        left: 0,
        top: 0,
        bottom: 0,
        right: 0,
        display: 'block',
        backgroundSize: 'cover',
        '&:before': {
          content: "''",
          position: 'absolute',
          top: 0,
          left: 0,
          right: 0,
          bottom: 0,
          pointerEvents: 'none',
          border: '4px solid transparent'
        }
      },
      selected: {
        '&:before': {
          borderColor: theme.palette.primary.main
        }
      },
      itemLabel: {}
    }),
  { name: 'BgStatusList' }
);

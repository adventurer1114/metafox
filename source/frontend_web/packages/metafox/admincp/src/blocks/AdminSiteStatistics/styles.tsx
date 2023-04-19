import { createStyles, makeStyles } from '@mui/styles';
import { Theme } from '@mui/material';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        display: 'flex',
        flexWrap: 'wrap'
      },
      item: {
        height: '91px',
        minWidth: '160px',
        flex: 1
      },
      icon: {
        fontSize: '40px',
        lineHeight: '40px',
        marginRight: theme.spacing(2)
      },
      value: {
        fontSize: '22px',
        lineHeight: '30px'
      },
      label: {
        maxWidth: '100%',
        display: 'block',
        overflow: 'hidden',
        padding: '0',
        fontSize: '16px',
        textOverflow: 'ellipsis',
        lineHeight: '20px',
        maxHeight: '40px',
        whiteSpace: 'normal',
        color: '#a2a2a2',
        wordBreak: 'break-word',
        wordWrap: 'break-word'
      },
      item0: { color: '#536dfe' },
      item1: { color: '#a1560f' },
      item2: { color: '#2bbd7e' },
      item3: { color: '#ff6e40' }
    }),
  {
    name: 'ItemStats'
  }
);
export default useStyles;

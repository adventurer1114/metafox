import { Box } from '@mui/material';
import { styled } from '@mui/material/styles';
import { Breakpoint } from '@mui/system';

export interface StyledContainerProps {
  maxWidth?: Breakpoint;
  editMode?: number;
  master?: boolean;
  minHeight?: string | number;
  previewContainer?: boolean;
}
const StyledContainer = styled(Box, {
  name: 'StyledContainer',
  shouldForwardProp: prop =>
    prop !== 'maxWidth' &&
    prop !== 'master' &&
    prop !== 'editMode' &&
    prop !== 'minHeight' &&
    prop !== 'previewContainer'
})<StyledContainerProps>(
  ({ theme, maxWidth, master, minHeight, editMode, previewContainer }) => ({
    width: '100%',
    marginLeft: 'auto',
    boxSizing: 'border-box',
    marginRight: 'auto',
    display: 'block', // Fix IE11 layout when used with main.
    ...(maxWidth &&
      (maxWidth !== 'xs'
        ? {
            [theme.breakpoints.up(maxWidth)]: {
              maxWidth: theme.breakpoints.values[maxWidth]
            }
          }
        : {
            [theme.breakpoints.up('xs')]: {
              maxWidth: Math.max(theme.breakpoints.values.xs, 444)
            }
          })),
    ...(editMode && {
      padding: theme.spacing(1, 1, 4, 2)
    }),
    ...(editMode &&
      master && {
        border: '1px dashed',
        borderColor: theme.palette.primary.main
      }),
    ...(previewContainer && {
      border: theme.mixins.border('secondary'),
      margin: '2px',
      backgroundColor: '#f2f2f2'
    }),
    ...(minHeight === 'appbar' && {
      minHeight: 58
    })
  })
);

export default StyledContainer;

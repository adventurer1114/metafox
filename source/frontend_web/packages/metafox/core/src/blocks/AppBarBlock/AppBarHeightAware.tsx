import { Box } from '@mui/material';
import { styled } from '@mui/material/styles';

export const AppBarHeightAware = styled(Box, {
  name: 'AppBarHeightAware',
  slot: 'Root'
})({
  width: '100%',
  display: 'block',
  height: 58,
  position: 'relative'
});

export default AppBarHeightAware;

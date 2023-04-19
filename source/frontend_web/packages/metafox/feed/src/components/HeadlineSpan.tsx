import { styled } from '@mui/material';

export const AvatarWrapper = styled('div', { name: 'AvatarWrapper' })(
  ({ theme }) => ({
    marginRight: theme.spacing(1.5)
  })
);

const HeadlineSpan = styled('span', { name: 'HeadlineSpan' })(({ theme }) => ({
  paddingRight: theme.spacing(0.5),
  color: theme.palette.text.secondary,
  '& > span': {
    paddingRight: theme.spacing(0)
  }
}));

export default HeadlineSpan;

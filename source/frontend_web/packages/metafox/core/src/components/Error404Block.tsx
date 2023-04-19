/**
 * @type: block
 * name: core.block.error404
 * title: Error 404 - Page Not Found
 * keywords: general
 * description:
 * thumbnail:
 * experiment: true
 */
import { useGlobal } from '@metafox/framework';
import { Box, Typography } from '@mui/material';
import { styled } from '@mui/material/styles';
import * as React from 'react';

type ErrorBlockProps = {
  title?: string;
  image?: string;
  color?:
    | 'inherit'
    | 'initial'
    | 'primary'
    | 'secondary'
    | 'textPrimary'
    | 'textSecondary'
    | 'error';
};

const Error404Root = styled(Box, {
  name: 'Error404',
  slot: 'Root'
})(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  flexDirection: 'column',
  padding: theme.spacing(5, 2),
  '& + .error404Block': {
    display: 'none'
  }
}));

const Error404Image = styled('img', {
  name: 'Error404',
  slot: 'Image'
})(({ theme }) => ({
  maxWidth: '100%'
}));

const Error404Title = styled(Typography, {
  name: 'Error404',
  slot: 'Title'
})<{ component: string; variant: string }>(({ theme }) => ({
  maxWidth: '100%',
  marginBottom: theme.spacing(6),
  fontWeight: 'bold',
  textAlign: 'center',
  [theme.breakpoints.down('sm')]: {
    fontSize: theme.mixins.pxToRem(40)
  },
  [theme.breakpoints.down('xs')]: {
    fontSize: theme.mixins.pxToRem(20)
  }
}));

const Error404Block = ({
  title = '404 - Page Not Found',
  color = 'primary'
}: ErrorBlockProps) => {
  const { usePageParams, assetUrl } = useGlobal();
  const pageParams = usePageParams();

  const image = assetUrl('layout.image_error_404');

  return (
    <Error404Root className="error404Block" data-testid="error404">
      <Error404Title color={color} component="h1" variant="h1">
        {pageParams?.title || title}
      </Error404Title>
      <Error404Image src={image} alt={title} />
    </Error404Root>
  );
};
export default Error404Block;

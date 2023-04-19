import * as React from 'react';
import RouteLink, { LinkProps } from './RouteLink';
import { Link } from '@mui/material';

const MuiLink = React.forwardRef((props: LinkProps, ref: any) => (
  <Link {...props} ref={ref} component={RouteLink} />
));

export default MuiLink;

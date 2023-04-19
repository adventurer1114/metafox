/**
 * This component handle global error wrapper whole site.
 */
import { Box, Typography } from '@mui/material';
import React from 'react';

type State = {
  error?: any;
  errorInfo?: any;
};

export default class SiteErrorBoundary extends React.Component<{}, State> {
  state: State = {};

  static getDerivedStateFromError(error) {
    return { error };
  }

  componentDidCatch(error, errorInfo) {
    this.setState({
      error,
      errorInfo
    });
  }

  render() {
    const { error } = this.state;

    if (error) {
      // trace error name for more information.
      // const name = error.name
      // try to clear localStorage
      if (localStorage) localStorage.clear();

      return (
        <Box
          sx={{
            margin: '5em 2em',
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center'
          }}
        >
          <Box>
            <Typography component="h1" variant="h4">
              Whoops,
              <br />
              Something went wrong :(
            </Typography>
            <Typography paragraph variant="body1" sx={{ paddingTop: '1em' }}>
              {error.message}
            </Typography>
          </Box>
        </Box>
      );
    }

    return this.props.children;
  }
}

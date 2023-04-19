import { Container, ItemView } from '@metafox/ui';
import { Box, Skeleton } from '@mui/material';
import * as React from 'react';
import useStyles from './styles';

export default function LoadingSkeleton({ wrapAs, wrapProps }) {
  const classes = useStyles();

  return (
    <ItemView wrapAs={wrapAs} wrapProps={wrapProps}>
      <div className={classes.wrapper}>
        <Box>
          <div className={classes.coverPhotoWrapper}>
            <div className={classes.coverPhotoInner}>
              <Skeleton variant="rectangular" />
            </div>
          </div>
          <div className={classes.profileUserWrapper}>
            <Container maxWidth="md">
              <Box
                display="flex"
                justifyContent="space-between"
                alignItems="flex-start"
                py={2}
                mb={2}
              >
                <Box
                  display="flex"
                  justifyContent="space-between"
                  alignItems="flex-start"
                >
                  <div className={classes.avatarWrapper}>
                    <Skeleton variant="circular" width={160} height={160} />
                  </div>
                  <Box>
                    <Skeleton variant="text" />
                    <Skeleton variant="text" />
                  </Box>
                </Box>
                <div className={classes.wrapperButtonInline}>
                  <Skeleton variant="rectangular" />
                  <Skeleton variant="rectangular" />
                </div>
              </Box>
              <Skeleton variant="rectangular" />
            </Container>
          </div>
        </Box>
      </div>
    </ItemView>
  );
}

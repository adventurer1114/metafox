import { useGlobal, useResourceAction } from '@metafox/framework';
import { usePageParams } from '@metafox/layout';
import { List, Typography, Box } from '@mui/material';
import * as React from 'react';
import { isArray } from 'lodash';
import { useFetchDetail } from '@metafox/rest-client';
import ErrorBoundary from '@metafox/core/pages/ErrorPage/Page';
import Item from './Item';

export default function Sections({
  searchAllAction = 'viewSections',
  ...others
}) {
  const pageParams = usePageParams();
  const { useIsMobile } = useGlobal();

  const { appName, resourceName } = pageParams;
  const dataSource = useResourceAction(appName, resourceName, searchAllAction);
  const isMobile = useIsMobile();

  const [data, loading, error] = useFetchDetail({
    dataSource,
    pageParams
  });

  return (
    <ErrorBoundary
      error={error}
      loading={loading}
      emptyComponent={data?.length > 0 ? undefined : 'core.block.no_results'}
    >
      <Box>
        <List disablePadding>
          {isArray(data)
            ? data.map((item, key) => (
                <Box key={key}>
                  <Typography variant="h4" sx={{ py: 2, pl: isMobile ? 2 : 0 }}>
                    {item.label}
                  </Typography>
                  <Item item={item} {...others} />
                </Box>
              ))
            : null}
        </List>
      </Box>
    </ErrorBoundary>
  );
}

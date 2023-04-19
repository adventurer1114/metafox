import { useGlobal, useResourceAction, Link } from '@metafox/framework';
import { useFetchDetail } from '@metafox/rest-client';
import { LineIcon } from '@metafox/ui';
import { Box, Typography } from '@mui/material';
import { isArray } from 'lodash';
import React from 'react';

const ActivityPointSummary = ({ isOwner }) => {
  const { usePageParams, i18n } = useGlobal();
  const pageParams = usePageParams();

  const dataSource = useResourceAction(
    'activitypoint',
    'activitypoint',
    'getStatistic'
  );

  const [data] = useFetchDetail({ dataSource, pageParams });

  if (!dataSource || !data) return null;

  const { items } = Object.assign({}, data);

  const summary = isArray(items) && items[0];

  if (!summary) return null;

  if (!isOwner) {
    return (
      <Box display="flex" color="border.primary" alignItems={'center'}>
        <LineIcon icon="ico-star-circle-o" sx={{ pr: 0.5 }} />
        {i18n.formatMessage(
          { id: 'activity_point_summary' },
          {
            value: () => (
              <Typography sx={{ pl: 0.5 }} variant="h6">
                {summary?.value}
              </Typography>
            )
          }
        )}
      </Box>
    );
  }

  return (
    <Box display="flex" color="border.primary" alignItems={'center'}>
      <LineIcon icon="ico-star-circle-o" sx={{ pr: 0.5 }} />
      <Link to={'activitypoint'}>
        <Box display="flex" color="border.primary" alignItems={'center'}>
          {i18n.formatMessage(
            { id: 'activity_point_summary' },
            {
              value: () => (
                <Typography sx={{ pl: 0.5 }} variant="h6">
                  {summary?.value}
                </Typography>
              )
            }
          )}
        </Box>
      </Link>
    </Box>
  );
};

export default ActivityPointSummary;

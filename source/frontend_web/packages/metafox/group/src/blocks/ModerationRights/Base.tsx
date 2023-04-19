import { RemoteFormBuilder } from '@metafox/form';
import {
  BlockViewProps,
  useGlobal,
  useResourceAction,
  useSession
} from '@metafox/framework';
import { APP_GROUP, RESOURCE_GROUP } from '@metafox/group';
import {
  Block,
  BlockContent,
  BlockHeader,
  usePageParams
} from '@metafox/layout';
import { Card, Typography } from '@mui/material';
import React from 'react';

export default function ModerationRights({
  title,
  blockProps
}: BlockViewProps) {
  const { loggedIn } = useSession();
  const pageParams = usePageParams();
  const { i18n } = useGlobal();

  const dataSource = useResourceAction(
    APP_GROUP,
    RESOURCE_GROUP,
    'viewModerationRight'
  );

  if (!loggedIn) {
    return null;
  }

  return (
    <Block>
      <BlockHeader title={title} />
      <Typography variant="body2" paragraph {...blockProps.contentStyle}>
        {i18n.formatMessage({ id: 'moderation_right_description' })}
      </Typography>
      <BlockContent>
        <Card sx={{ boxShadow: 'none', paddingX: 2 }}>
          <RemoteFormBuilder
            noHeader
            dataSource={dataSource}
            pageParams={pageParams}
          />
        </Card>
      </BlockContent>
    </Block>
  );
}

import { useGlobal } from '@metafox/framework';
import { TruncateText, LineIcon } from '@metafox/ui';
import { Box } from '@mui/material';
import React from 'react';

export default function Reply({ item }: { item: Record<string, any> }) {
  const { i18n } = useGlobal();

  return (
    <Box mb={1} color={'text.hint'}>
      <TruncateText variant={'body2'} lines={2}>
        <Box
          mr={1}
          component={'span'}
          sx={{ display: 'inline-flex', transform: 'scaleX(-1)' }}
        >
          <LineIcon icon="ico-reply" />
        </Box>
        {i18n.formatMessage(
          {
            id: 'replying_to_user'
          },
          {
            user_name: item?.user_full_name
          }
        )}
        {item.text ? <>: {item.text}</> : null}
      </TruncateText>
    </Box>
  );
}

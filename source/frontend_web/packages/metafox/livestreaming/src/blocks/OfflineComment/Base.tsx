import { useGlobal } from '@metafox/framework';
import * as React from 'react';
import { LivestreamDetailViewProps } from '../../types';

function LivestreamDetailOfflineComment({
  item,
  identity,
  state,
  handleAction
}: LivestreamDetailViewProps) {
  const { ItemDetailInteraction } = useGlobal();

  return (
    <ItemDetailInteraction
      identity={identity}
      state={state}
      handleAction={handleAction}
    />
  );
}

export default LivestreamDetailOfflineComment;

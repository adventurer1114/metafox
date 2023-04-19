/**
 * @type: service
 * name: ShareActButton
 */
import { GlobalState, HandleAction, useGlobal } from '@metafox/framework';
import { ActButton } from '@metafox/ui';
import React from 'react';
import { useSelector } from 'react-redux';

export interface ShareActButtonProps {
  identity: string;
  onlyIcon?: boolean;
  handleAction: HandleAction;
}

export default function ShareActButton({
  identity,
  onlyIcon,
  handleAction
}: ShareActButtonProps) {
  const { ItemActionMenu, i18n } = useGlobal();

  const shareMenu = useSelector(
    (state: GlobalState) => state.share.shareOptions || []
  );

  return (
    <ItemActionMenu
      icon="ico-share-o"
      items={shareMenu}
      testid="menuShare"
      identity={identity}
      handleAction={handleAction}
      control={
        <ActButton
          data-testid="buttonShare"
          icon="ico-share-o"
          label={!onlyIcon && i18n.formatMessage({ id: 'share' })}
        />
      }
    />
  );
}

/**
 * @type: service
 * name: ShareActButton
 */
import { HandleAction, useGlobal } from '@metafox/framework';
import { ActButton } from '@metafox/ui';
import { filterShowWhen } from '@metafox/utils';
import React from 'react';
import { APP_FEED } from '../constants';

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
  const { ItemActionMenu, i18n, useGetItem, useAppMenu } = useGlobal();

  const item = useGetItem(identity);

  const menu = useAppMenu(APP_FEED, 'itemShareActionsMenu');

  if (!menu || !menu.items) return null;

  const filteredItems = filterShowWhen(menu.items, { item });

  return (
    <ItemActionMenu
      icon="ico-share-o"
      items={filteredItems}
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

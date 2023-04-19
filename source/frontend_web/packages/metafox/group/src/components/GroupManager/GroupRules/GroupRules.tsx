/**
 * @type: itemView
 * name: groupRule.itemView.mainCard
 */

import { connectItemView, connectSubject, useGlobal } from '@metafox/framework';
import { ItemText, ItemTitle, ItemView, ItemAction } from '@metafox/ui';
import React from 'react';
import { GroupRuleProps } from '../MembershipQuestion/types';

const GroupRuleCard = ({
  item,
  identity,
  wrapAs,
  wrapProps,
  state,
  handleAction
}: GroupRuleProps) => {
  const { ItemActionMenu } = useGlobal();

  if (!item) return null;

  const { title, description } = item;

  return (
    <ItemView testid={'GroupRuleCard'} wrapAs={wrapAs} wrapProps={wrapProps}>
      <ItemText>
        <ItemTitle marginBottom={2} marginRight={3}>
          {title}
        </ItemTitle>
        <ItemAction placement="top-end">
          <ItemActionMenu
            identity={identity}
            icon={'ico-dottedmore-vertical-o'}
            state={state}
            handleAction={handleAction}
          />
        </ItemAction>
        <span dangerouslySetInnerHTML={{ __html: description }}></span>
      </ItemText>
    </ItemView>
  );
};

ItemView.displayName = 'GroupRuleItem_MainCard';

export default connectSubject(connectItemView(GroupRuleCard, () => {}));

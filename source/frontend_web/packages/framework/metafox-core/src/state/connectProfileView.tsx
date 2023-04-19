import { GlobalState, ItemViewBaseProps, useGlobal } from '@metafox/framework';
import { ItemUserShape } from '@metafox/ui';
import React from 'react';
import { useSelector } from 'react-redux';
import {
  getItemSelector,
  getProfileActionMenuSelector,
  getProfileMenuSelector,
  getUserSelector
} from '../selectors';

export default function connectProfileView(
  BaseView: ItemViewBaseProps,
  actionCreators: any
) {
  const Enhancer = (props: any) => {
    const { useActionControl } = useGlobal();
    const { identity } = props;

    const item = useSelector<GlobalState, any>(state =>
      getItemSelector(state, identity)
    );

    const profileMenu = useSelector<GlobalState>(state =>
      getProfileMenuSelector(
        state,
        item?.module_name || item?.resource_name,
        item?.resource_name
      )
    );

    const profileActionMenu = useSelector<GlobalState>(state =>
      getProfileActionMenuSelector(
        state,
        item?.module_name || item?.resource_name,
        item?.resource_name
      )
    );

    const user = useSelector<GlobalState>(state =>
      getUserSelector(state, item?.user)
    ) as ItemUserShape;

    const [handleAction, state, setState, actions] = useActionControl<
      unknown,
      unknown
    >(identity, {}, actionCreators);

    return (
      <BaseView
        {...props}
        identity={identity}
        item={item}
        user={user}
        profileMenu={profileMenu}
        profileActionMenu={profileActionMenu}
        state={state}
        actions={actions}
        setState={setState}
        handleAction={handleAction}
      />
    );
  };

  Enhancer.LoadingSkeleton = BaseView.LoadingSkeleton;
  Enhancer.displayName = `Connected_${BaseView.displayName}`;

  return Enhancer;
}

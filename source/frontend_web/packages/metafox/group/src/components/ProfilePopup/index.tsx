/**
 * @type: popover
 * name: group.popover.groupProfilePopup
 * path: /group/:id
 * bundle: web
 */
import { fetchDetail, useGlobal } from '@metafox/framework';
import React, { useEffect } from 'react';
import { actionCreators, connectItemView } from '../../hocs/connectGroupItem';
import ItemView from './ProfilePopup';

const ConnectedView = connectItemView(ItemView, actionCreators);

const Popup = ({ id, ...rest }) => {
  const { dispatch } = useGlobal();
  const [loaded, setLoad] = React.useState(false);

  useEffect(() => {
    dispatch(fetchDetail('/group/:id', { id }, () => setLoad(true)));
  }, [dispatch, id]);

  const identity = `group.entities.group.${id}`;

  return <ConnectedView identity={identity} loaded={loaded} {...rest} />;
};

export default Popup;

/**
 * @type: popover
 * name: page.popover.pageProfilePopup
 * path: /page/:id
 */
import { fetchDetail, useGlobal } from '@metafox/framework';
import React, { useEffect } from 'react';
import { actionCreators, connectItemView } from '../../hocs/connectPageItem';
import ItemView from './ProfilePopup';

const ConnectedView = connectItemView(ItemView, actionCreators);

const Popup = ({ id, ...rest }) => {
  const { dispatch } = useGlobal();
  const [loaded, setLoad] = React.useState(false);

  useEffect(() => {
    dispatch(fetchDetail('/page/:id', { id }, () => setLoad(true)));
  }, [dispatch, id]);

  const identity = `page.entities.page.${id}`;

  if (!loaded) return null;

  return <ConnectedView identity={identity} loaded={loaded} {...rest} />;
};

export default Popup;

/**
 * @type: itemView
 * name: group.itemView.pendingPost
 */

import { useGlobal, withItemView } from '@metafox/framework';
import { GroupItemProps } from '@metafox/group';
import groupManagerActions from '@metafox/group/actions/groupManagerActions';
import { ItemView } from '@metafox/ui';
import {
  Button,
  CardActions,
  CardContent,
  Divider,
  styled
} from '@mui/material';
import * as React from 'react';

const StyledItemView = styled(ItemView)(() => ({
  maxWidth: '800px',
  padding: 0
}));

const StyledButtonMore = styled(Button, {
  name: 'pendingPost',
  slot: 'button-more'
})(({ theme }) => ({
  padding: 0,
  minWidth: 32
}));

const PendingPostCard = ({
  identity,
  wrapAs: WrapAs,
  wrapProps,
  actions,
  state,
  handleAction
}: GroupItemProps) => {
  const { jsxBackend, i18n, ItemActionMenu } = useGlobal();

  const FeedContent = jsxBackend.get('feed.itemView.content');

  if (!identity || !FeedContent) return null;

  const ContentWrapper = withItemView({}, () => {})(FeedContent);

  const content = ContentWrapper({ identity });

  return (
    <StyledItemView
      testid="PendingPostCard"
      wrapAs={WrapAs}
      wrapProps={wrapProps}
    >
      <CardContent>
        {content}
        <Divider />
      </CardContent>
      <CardActions sx={{ p: 2, pt: 0 }}>
        <Button
          component="h5"
          sx={{ width: '100px' }}
          variant="contained"
          size="small"
          onClick={actions.approvePendingPost}
        >
          {i18n.formatMessage({ id: 'approve' })}
        </Button>
        <Button
          component="h5"
          sx={{ width: '100px' }}
          variant="outlined"
          size="small"
          onClick={actions.declinePendingPost}
        >
          {i18n.formatMessage({ id: 'decline' })}
        </Button>
        <StyledButtonMore component="h5" variant="outlined" size="small">
          <ItemActionMenu
            menuName="itemPendingActionMenu"
            identity={identity}
            state={state}
            handleAction={handleAction}
            size="small"
            color="primary"
            icon="ico-dottedmore-o"
          />
        </StyledButtonMore>
      </CardActions>
    </StyledItemView>
  );
};

export default withItemView({}, groupManagerActions)(PendingPostCard);

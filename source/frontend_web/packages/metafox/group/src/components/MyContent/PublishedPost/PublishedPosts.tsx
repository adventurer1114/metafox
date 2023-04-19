/**
 * @type: itemView
 * name: group.itemView.myPublishedPost
 */

import { connectItemView, useGlobal, withItemView } from '@metafox/framework';
import { useGetItem } from '@metafox/framework/hooks';
import { GroupItemProps } from '@metafox/group';
import { ItemView } from '@metafox/ui';
import { Button, CardActions, CardContent, styled } from '@mui/material';
import * as React from 'react';

const StyledItemView = styled(ItemView)(() => ({
  maxWidth: '800px',
  padding: 0
}));

const PublishedPosts = ({
  identity,
  wrapAs: WrapAs,
  wrapProps,
  actions
}: GroupItemProps) => {
  const { jsxBackend, navigate, i18n } = useGlobal();
  const item = useGetItem(identity);
  const [visible, setVisible] = React.useState<boolean>(true);

  const FeedContent = jsxBackend.get('feed.itemView.content');

  if (!identity || !FeedContent) return null;

  const ContentWrapper = withItemView({}, () => {})(
    FeedContent as React.FC<any>
  );
  const Content = connectItemView(ContentWrapper, () => {});

  const handleViewPost = () => {
    navigate(item.link);
  };

  if (!visible) return null;

  return (
    <StyledItemView
      testid="publishedPosts"
      wrapAs={WrapAs}
      wrapProps={wrapProps}
    >
      <CardContent sx={{ pt: 2 }}>
        <Content
          identity={identity}
          setVisible={setVisible}
          isItemAction={false}
        />
      </CardContent>
      <CardActions sx={{ pt: 0, pb: 2 }}>
        <Button
          sx={{ width: '100%' }}
          variant="outlined"
          onClick={handleViewPost}
        >
          {i18n.formatMessage({ id: 'view_post' })}
        </Button>
      </CardActions>
    </StyledItemView>
  );
};

export default PublishedPosts;

/**
 * @type: itemView
 * name: blog.itemView.mainCard
 */
import actionCreators from '@metafox/blog/actions/blogItemActions';
// types
import { BlogItemProps as ItemProps } from '@metafox/blog/types';
import { connectItemView, Link, useGlobal } from '@metafox/framework';
// components
import {
  CategoryList,
  DraftFlag,
  FeaturedFlag,
  FormatDate,
  ItemAction,
  ItemMedia,
  ItemSubInfo,
  ItemSummary,
  ItemText,
  ItemTitle,
  ItemView,
  PendingFlag,
  SponsorFlag,
  Statistic
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { styled } from '@mui/material';
import React from 'react';

const name = 'BlogItemView';

const FlagWrapper = styled('span', {
  slot: 'FlagWrapper',
  name,
  shouldForwardProp: prop => prop !== 'isMobile'
})<{ isMobile: boolean }>(({ theme, isMobile }) => ({
  display: 'inline-flex',
  ...(isMobile && {
    display: 'flex',
    marginBottom: theme.spacing(1)
  })
}));
const ItemTitleStyled = styled(ItemTitle, {
  name,
  slot: 'ItemTitleStyled',
  shouldForwardProp: prop => prop !== 'isMobile'
})<{ isMobile: boolean }>(({ theme, isMobile }) => ({
  ...(!isMobile && {
    maxWidth: 'calc(100% - 32px)'
  })
}));

export function BlogItemView({
  identity,
  itemProps,
  item,
  user,
  state,
  handleAction,
  wrapAs,
  wrapProps
}: ItemProps) {
  const { ItemActionMenu, useIsMobile, useGetItems, usePageParams } =
    useGlobal();
  const { tab } = usePageParams();
  const isMobile = useIsMobile();
  const categories = useGetItems<{ id: number; name: string }>(
    item?.categories
  );

  if (!item || !user) return null;

  const { link: to, creation_date } = item;

  const cover = getImageSrc(item?.image, '500');

  return (
    <ItemView wrapAs={wrapAs} wrapProps={wrapProps} testid="blog">
      <ItemMedia src={cover} link={to} alt={item.title} backgroundImage />
      <ItemText>
        <CategoryList
          to="/blog/category"
          data={categories}
          sx={{ mb: { sm: 1, xs: 0 } }}
        />
        <ItemTitleStyled isMobile={isMobile}>
          <FlagWrapper isMobile={isMobile}>
            <FeaturedFlag variant="itemView" value={item.is_featured} />
            <SponsorFlag variant="itemView" value={item.is_sponsor} />
            <PendingFlag variant="itemView" value={item.is_pending} />
          </FlagWrapper>
          <DraftFlag
            sx={{ fontWeight: 'normal' }}
            value={item.is_draft && tab !== 'draft'}
            variant="h4"
            component="span"
          />
          <Link to={item.link}>{item.title}</Link>
        </ItemTitleStyled>
        {itemProps.showActionMenu ? (
          <ItemAction placement="top-end">
            <ItemActionMenu
              identity={identity}
              icon={'ico-dottedmore-vertical-o'}
              state={state}
              handleAction={handleAction}
            />
          </ItemAction>
        ) : null}
        <ItemSummary sx={{ my: 1 }}>{item.description}</ItemSummary>
        <ItemSubInfo sx={{ color: 'text.secondary', mt: 1 }}>
          <Link
            color="inherit"
            to={user.link}
            children={user.full_name}
            hoverCard={`/user/${user.id}`}
          />
          <FormatDate
            data-testid="creationDate"
            value={creation_date}
            format="ll"
          />
          <Statistic
            values={item.statistic}
            display={'total_view'}
            component={'span'}
            skipZero={false}
          />
        </ItemSubInfo>
      </ItemText>
    </ItemView>
  );
}

export default connectItemView(BlogItemView, actionCreators, {});

import { Link, useGlobal } from '@metafox/framework';
import {
  ItemAction,
  ItemMedia,
  ItemSummary,
  ItemText,
  ItemTitle,
  ItemView,
  LineIcon,
  Statistic
} from '@metafox/ui';
import { styled } from '@mui/material';
import React from 'react';

const ItemWrapperContent = styled('div')(({ theme }) => ({
  flex: 1,
  display: 'flex',
  justifyContent: 'space-between',
  alignItems: 'center'
}));
const WrapperLink = styled(Link)(({ theme }) => ({
  flex: 1,
  display: 'flex',
  '&:hover': {
    textDecoration: 'none !important'
  },
  '&:hover .ItemView-title': {
    textDecoration: 'underline'
  }
}));

const WrapperTitle = styled(ItemTitle)(() => ({
  '& > *': {
    whiteSpace: 'inherit'
  }
}));

function CollectionItemMainCard({
  item,
  identity,
  actions,
  handleAction,
  state,
  itemProps,
  user,
  wrapAs,
  wrapProps
}) {
  const { ItemActionMenu, usePageParams } = useGlobal();

  const { collection_id } = usePageParams();

  // eslint-disable-next-line eqeqeq
  const selected = collection_id == item.id;

  if (!item) return null;

  const to = `/saved/list/${item.id}`;

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
      color="inherit"
    >
      <ItemWrapperContent>
        <WrapperLink color={selected ? 'primary' : 'inherit'} to={to}>
          <ItemMedia>
            <LineIcon
              icon="ico-folder-alt-o"
              sx={{ fontSize: '1rem' }}
              color={selected ? 'primary' : 'textPrimary'}
            />
          </ItemMedia>
          <ItemText>
            <WrapperTitle>{item.name}</WrapperTitle>
            <ItemSummary>
              <Statistic
                values={{ total_item: item.statistic.total_saved_item }}
                display="total_item"
                skipZero={false}
              />
            </ItemSummary>
          </ItemText>
        </WrapperLink>
        {itemProps.showActionMenu && (
          <ItemAction visible="hover">
            <ItemActionMenu
              identity={identity}
              icon={'ico-dottedmore-vertical-o'}
              state={state}
              handleAction={handleAction}
            />
          </ItemAction>
        )}
      </ItemWrapperContent>
    </ItemView>
  );
}

export default CollectionItemMainCard;

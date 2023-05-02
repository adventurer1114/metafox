import { Link, useGlobal } from '@metafox/framework';
import { useBlock } from '@metafox/layout';
import { QuizItemProps } from '@metafox/quiz/types';
import {
  FeaturedFlag,
  ItemAction,
  ItemMedia,
  ItemText,
  ItemTitle,
  ItemView,
  SponsorFlag,
  PendingFlag,
  Statistic
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { styled } from '@mui/material';
import React from 'react';
import useStyles from './styles';

const FlagWrapper = styled('span', {
  name: 'FlagWrapper'
})(({ theme }) => ({
  display: 'flex',
  marginBottom: theme.spacing(1)
}));

export default function QuizItemMainCard({
  item,
  itemProps,
  user,
  handleAction,
  state,
  identity,
  wrapAs,
  wrapProps
}: QuizItemProps) {
  const { ItemActionMenu, useIsMobile } = useGlobal();
  const classes = useStyles();
  const isMobile = useIsMobile();

  const { itemLinkProps = {} } = useBlock();

  if (!item) return null;

  const to = `/quiz/${item.id}`;

  const cover = getImageSrc(item.image, '240');

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <ItemMedia
        asModal={itemLinkProps.asModal}
        src={cover}
        link={to}
        alt={item.title}
        backgroundImage
      />
      <ItemText>
        {isMobile ? (
          <FlagWrapper>
            <FeaturedFlag variant="itemView" value={item.is_featured} />
            <SponsorFlag variant="itemView" value={item.is_sponsor} />
            <PendingFlag variant="itemView" value={item.is_pending} />
          </FlagWrapper>
        ) : null}
        <ItemTitle>
          <Link to={to} children={item.title} color={'inherit'} />
        </ItemTitle>
        {itemProps.showActionMenu ? (
          <ItemAction placement="top-end" spacing="normal">
            <ItemActionMenu
              identity={identity}
              icon={'ico-dottedmore-vertical-o'}
              state={state}
              handleAction={handleAction}
            />
          </ItemAction>
        ) : null}
        <div className={classes.itemMinor}>
          <Link to={`/user/${user?.id}`} children={user.full_name} />
        </div>
        <div className={classes.itemStatistic}>
          <Statistic
            values={item.statistic}
            display={'total_play'}
            skipZero={false}
          />
        </div>
        {!isMobile ? (
          <div className={classes.itemFlag}>
            <FeaturedFlag variant="itemView" value={item.is_featured} />
            <SponsorFlag variant="itemView" value={item.is_sponsor} />
            <PendingFlag variant="itemView" value={item.is_pending} />
          </div>
        ) : null}
      </ItemText>
    </ItemView>
  );
}

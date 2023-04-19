import { useGlobal, getItemSelector, GlobalState } from '@metafox/framework';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import { EventDetailViewProps as Props } from '@metafox/event/types';
import React from 'react';
import { styled } from '@mui/material';
import { UserAvatar, Statistic } from '@metafox/ui';
import ProfileLink from '@metafox/feed/components/FeedItemView/ProfileLink';
import { useSelector } from 'react-redux';
import { getImageSrc } from '@metafox/utils';

const HeadlineSpan = styled('span', { name: 'HeadlineSpan' })(({ theme }) => ({
  '& a': {
    fontWeight: theme.typography.fontWeightBold,
    color: theme.palette.text.primary
  }
}));

const BlockContentStyled = styled(BlockContent, { name: 'BlockContentStyled' })(
  ({ theme }) => ({
    display: 'flex'
    // padding: 16
  })
);
const InfoStyled = styled('div', { name: 'InfoStyled' })(({ theme }) => ({
  alignSelf: 'center',
  paddingLeft: theme.spacing(1.5)
}));

const OwnerStyled = styled(ProfileLink, { name: 'OwnerStyled' })(
  ({ theme }) => ({
    fontWeight: theme.typography.fontWeightBold,
    color: theme.palette.text.primary,
    fontSize: theme.mixins.pxToRem(15),
    '&:hover': {
      textDecoration: 'underline'
    }
  })
);

export default function OverviewGroup({
  item,
  user,
  title,
  gridVariant = 'listView',
  actions,
  ...rest
}: Props) {
  const { assetUrl } = useGlobal();
  const owner = useSelector((state: GlobalState) =>
    getItemSelector(state, item?.owner)
  );

  if (owner?.resource_name === user?.resource_name) return null;

  const cover = getImageSrc(
    owner?.cover,
    '1024',
    assetUrl('group.cover_no_image')
  );

  return (
    <Block {...rest}>
      <BlockHeader title={owner?.item_type || title} />
      <BlockContentStyled>
        <UserAvatar size={48} variant="rounded" src={cover} />
        <InfoStyled>
          <HeadlineSpan>
            <OwnerStyled user={owner} />
          </HeadlineSpan>
          <Statistic
            values={owner?.statistic}
            display={
              owner?.statistic?.total_member ? 'total_member' : 'total_like'
            }
            skipZero={false}
            truthyValue
          />
        </InfoStyled>
      </BlockContentStyled>
    </Block>
  );
}

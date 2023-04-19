import { Link, useGlobal } from '@metafox/framework';
import { GroupItemProps } from '@metafox/group/types';
import {
  ButtonList,
  FeaturedFlag,
  ItemMedia,
  ItemSummary,
  ItemText,
  ItemTitle,
  ItemView,
  LineIcon,
  SponsorFlag,
  Statistic,
  Image,
  PendingFlag
} from '@metafox/ui';
import { filterShowWhen, withDisabledWhen, getImageSrc } from '@metafox/utils';
import { Box, IconButton, styled, Typography } from '@mui/material';
import * as React from 'react';

const TypographyStyled = styled(Typography)(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(15),
  fontWeight: '600'
}));

const FlagWrapper = styled('div', {
  name: 'GroupMainCardItem',
  slot: 'flagWrapper'
})(({ theme }) => ({
  marginBottom: theme.spacing(1)
}));

const ItemTextWrapper = styled(ItemText, {
  name: 'ItemTextWrapper'
})(({ theme }) => ({
  width: '100%'
}));

const ItemMediaWrapper = styled(ItemMedia, {
  name: 'ItemMediaWrapper'
})(({ theme }) => ({
  '& img': {
    border: 'none'
  }
}));

const IconButtonWrapper = styled(IconButton, { slot: 'IconButtonWrapper' })(
  ({ theme }) => ({
    width: '100%',
    '& p': {
      marginLeft: theme.spacing(1)
    }
  })
);

export default function GroupMainCardItem({
  item,
  itemActionMenu,
  identity,
  handleAction,
  user,
  itemProps,
  wrapProps,
  wrapAs
}: GroupItemProps) {
  const { ItemActionMenu, useSession, i18n, getAcl, getSetting, assetUrl } =
    useGlobal();
  const { loggedIn, user: authUser } = useSession();

  if (!item) return null;

  const { title, id, statistic, link = '' } = item || {};
  const to = link || `/group/${id}`;

  const acl = getAcl();
  const setting = getSetting();
  const condition = {
    item,
    acl,
    setting,
    isAuth: authUser?.id === user?.id
  };

  const actionMenuItems = withDisabledWhen(
    filterShowWhen(itemActionMenu, condition),
    condition
  );

  const reactButton: any = actionMenuItems.splice(0, 1)[0];

  const cover = getImageSrc(item.image, '500', assetUrl('blog.no_image'));

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
      mediaPlacement="top"
    >
      <ItemMediaWrapper>
        <Image link={item.link} src={cover} aspectRatio={'13'} />
      </ItemMediaWrapper>
      <ItemTextWrapper>
        <ItemTitle>
          {item.is_featured || item.is_sponsor || item.is_pending ? (
            <FlagWrapper>
              <FeaturedFlag value={item.is_featured} variant="itemView" />
              <SponsorFlag value={item.is_sponsor} variant="itemView" />
              <PendingFlag variant="itemView" value={item.is_pending} />
            </FlagWrapper>
          ) : null}
          <Link to={to} color={'inherit'} hoverCard>
            {title}
          </Link>
        </ItemTitle>
        <ItemSummary>
          <Statistic
            values={statistic}
            display={'total_member'}
            skipZero={false}
            truthyValue
          />
        </ItemSummary>
        <Box sx={{ mt: 2 }}>
          {loggedIn ? (
            <ButtonList>
              {reactButton && (
                <Box sx={{ flex: 1, minWidth: 0 }}>
                  <IconButtonWrapper
                    size="medium"
                    color="primary"
                    variant="outlined-square"
                    disabled={reactButton?.disabled}
                    onClick={() => handleAction(reactButton.value)}
                    className={reactButton.name}
                  >
                    <LineIcon icon={reactButton?.icon} />
                    <TypographyStyled variant="body1">
                      {i18n.formatMessage({ id: reactButton.label })}
                    </TypographyStyled>
                  </IconButtonWrapper>
                </Box>
              )}
              {item.extra && itemProps.showActionMenu ? (
                <ItemActionMenu
                  identity={identity}
                  items={actionMenuItems}
                  handleAction={handleAction}
                  size="medium"
                  variant="outlined-square"
                  color="primary"
                  icon="ico-dottedmore-o"
                  tooltipTitle={i18n.formatMessage({ id: 'more_options' })}
                  sx={{ ml: 1 }}
                />
              ) : null}
            </ButtonList>
          ) : null}
        </Box>
      </ItemTextWrapper>
    </ItemView>
  );
}

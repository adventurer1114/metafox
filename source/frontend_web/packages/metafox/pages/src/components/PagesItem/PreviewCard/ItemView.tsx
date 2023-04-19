import { Link, useGlobal } from '@metafox/framework';
import { PagesItemProps } from '@metafox/pages/types';
import { mappingRelationship } from '@metafox/pages/utils';
import {
  ButtonList,
  FeaturedFlag,
  ItemAction,
  ItemMedia,
  ItemSummary,
  ItemText,
  ItemTitle,
  ItemView,
  LineIcon,
  SponsorFlag,
  Statistic,
  UserAvatar
} from '@metafox/ui';
import { Box, IconButton, Tooltip, useMediaQuery, styled } from '@mui/material';
import { useTheme } from '@mui/material/styles';
import * as React from 'react';

const ItemTitleWrapper = styled(ItemTitle, { slot: 'ItemTitleWrapper' })(
  ({ theme }) => ({
    '& .MuiTypography-root': {
      display: 'flex',
      flexDirection: 'column'
    },
    '& .MuiLink-root': {
      display: 'block',
      overflow: 'hidden',
      textOverflow: 'ellipsis',
      whiteSpace: 'nowrap'
    }
  })
);

export default function PagePreviewCard({
  item,
  identity,
  handleAction,
  state,
  actions,
  wrapAs,
  wrapProps
}: PagesItemProps) {
  const { useSession, i18n } = useGlobal();
  const { loggedIn } = useSession();
  const theme = useTheme();
  const isSmallScreen = useMediaQuery(theme.breakpoints.down('sm'));

  if (!item) return null;

  const { title, statistic, is_liked, extra, link: to, is_owner } = item;

  const reactButton = mappingRelationship(
    is_owner,
    is_liked,
    extra?.can_unlike || false,
    extra?.can_like || false,
    actions
  );

  const Actions = () => {
    return (
      <ItemAction>
        {loggedIn && (
          <ButtonList>
            {reactButton?.textId && (
              <Tooltip
                title={i18n.formatMessage({
                  id: reactButton?.textId
                })}
              >
                <IconButton
                  aria-label={reactButton?.textId}
                  size="medium"
                  color="primary"
                  onClick={reactButton.actions}
                  variant="outlined-square"
                >
                  <LineIcon icon={reactButton.icon} />
                </IconButton>
              </Tooltip>
            )}
          </ButtonList>
        )}
      </ItemAction>
    );
  };

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <ItemMedia>
        <UserAvatar user={item} size={isSmallScreen ? 56 : 80} />
      </ItemMedia>
      <ItemText>
        <ItemTitleWrapper>
          <Box
            sx={{
              display: { md: 'inline', sm: 'block' },
              marginBottom: theme.spacing(0.75)
            }}
          >
            <FeaturedFlag value={item.is_featured} variant="itemView" />
            <SponsorFlag value={item.is_sponsor} variant="itemView" />
          </Box>
          <Link to={to} color={'inherit'}>
            {title}
          </Link>
        </ItemTitleWrapper>
        <ItemSummary>
          <Statistic
            values={statistic}
            display={'total_like'}
            skipZero={false}
          />
        </ItemSummary>
        <Box
          sx={{
            display: { xs: 'block', sm: 'none' },
            marginTop: theme.spacing(2)
          }}
        >
          <Actions />
        </Box>
      </ItemText>
      <Box sx={{ display: { xs: 'none', sm: 'block' } }}>
        <Actions />
      </Box>
    </ItemView>
  );
}

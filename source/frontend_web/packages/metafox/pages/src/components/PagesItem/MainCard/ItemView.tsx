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
  SponsorFlag,
  Statistic,
  UserAvatar,
  LineIcon, 
  PendingFlag
} from '@metafox/ui';
import {
  Box,
  IconButton,
  useMediaQuery,
  styled,
  Typography
} from '@mui/material';
import { useTheme } from '@mui/material/styles';
import * as React from 'react';

const TypographyStyled = styled(Typography)(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(15),
  fontWeight: '600'
}));

const IconButtonWrapper = styled(IconButton, { slot: 'IconButtonWrapper' })(
  ({ theme }) => ({
    width: 'auto',
    padding: theme.spacing(1, 2),
    '& p': {
      marginLeft: theme.spacing(1)
    }
  })
);

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

const ItemMediaStyled = styled(ItemMedia)(({ theme }) => ({
  height: '100%'
}));

const ButtonListWrapper = styled(ButtonList, { slot: 'ButtonListWrapper' })(
  ({ theme }) => ({
    marginTop: theme.spacing(1)
  })
);

export default function PageItemMainCard({
  item,
  identity,
  handleAction,
  state,
  actions,
  wrapAs,
  wrapProps
}: PagesItemProps) {
  const { ItemActionMenu, useSession, i18n } = useGlobal();
  const { loggedIn } = useSession();
  const theme = useTheme();
  const isSmallScreen = useMediaQuery(theme.breakpoints.down('sm'));

  if (!item) return null;

  const { title, statistic, is_liked, extra, link: to, is_owner } = item;

  const reactButton = mappingRelationship(
    is_owner,
    is_liked,
    extra?.can_un_like || false,
    extra?.can_like || false,
    actions
  );

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <ItemMediaStyled>
        <UserAvatar user={item} size={isSmallScreen ? 56 : 80} />
      </ItemMediaStyled>
      <ItemText>
        <Box>
          <ItemTitleWrapper>
            <Box
              sx={{
                display: { md: 'inline', sm: 'block' },
                mb: 0.75
              }}
            >
              <FeaturedFlag value={item.is_featured} variant="itemView" />
              <SponsorFlag value={item.is_sponsor} variant="itemView" />
              <PendingFlag variant="itemView" value={item.is_pending} />
            </Box>
            <Link to={to} color={'inherit'} hoverCard={`/page/${item?.id}`}>
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
        </Box>
        {loggedIn ? (
          <ItemAction>
            <ButtonListWrapper>
              <IconButtonWrapper
                aria-label={reactButton.textId}
                size="medium"
                color="primary"
                variant={reactButton.variant}
                disabled={reactButton.disabled}
                onClick={reactButton.actions}
                className={reactButton.textId}
              >
                <LineIcon icon={reactButton.icon} />
                <TypographyStyled variant="body1">
                  {i18n.formatMessage({ id: reactButton.textId })}
                </TypographyStyled>
              </IconButtonWrapper>
              {item.extra ? (
                <ItemActionMenu
                  identity={identity}
                  state={state}
                  handleAction={handleAction}
                  size="medium"
                  variant="outlined-square"
                  color="primary"
                  icon="ico-dottedmore-o"
                  tooltipTitle={i18n.formatMessage({ id: 'more_options' })}
                />
              ) : null}
            </ButtonListWrapper>
          </ItemAction>
        ) : null}
      </ItemText>
    </ItemView>
  );
}

import {
  EventDetailViewProps as Props,
  SETTING_24H,
  BOUNDS_EAST,
  BOUNDS_NORTH,
  BOUNDS_SOUTH,
  BOUNDS_WEST
} from '@metafox/event';
import InterestedButton from '@metafox/event/components/InterestedButton';
import { isEventEnd, mappingTimeDisplay } from '@metafox/event/utils';
import { AttachmentItemShape, RouteLink, useGlobal } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent } from '@metafox/layout';
import {
  AttachmentItem,
  CategoryList,
  FeaturedFlag,
  FormatDate,
  ItemTitle,
  LineIcon,
  PendingFlag,
  SponsorFlag,
  PrivacyIcon
} from '@metafox/ui';
import {
  Box,
  Button,
  IconButton,
  styled,
  Typography,
  useTheme
} from '@mui/material';
import { isEqual, isEmpty } from 'lodash';
import React from 'react';
import useStyles from './styles';

const name = 'EventDetail';

const AttachmentTitle = styled('div', { name, slot: 'attachmentTitle' })(
  ({ theme }) => ({
    fontSize: theme.mixins.pxToRem(18),
    marginTop: theme.spacing(2),
    color: theme.palette.text.secondary,
    fontWeight: theme.typography.fontWeightBold
  })
);

const IconButtonAction = styled(IconButton, { name, slot: 'IconButtonAction' })(
  ({ theme }) => ({
    ...(theme.palette.mode === 'dark' && {
      '&:hover': {
        backgroundColor: theme.palette.grey[700]
      }
    })
  })
);

const Attachment = styled('div', { name, slot: 'attachment' })(({ theme }) => ({
  width: '100%',
  display: 'flex',
  flexWrap: 'wrap',
  justifyContent: 'space-between'
}));
const AttachmentItemWrapper = styled('div', {
  name,
  slot: 'attachmentItemWrapper'
})(({ theme }) => ({
  marginTop: theme.spacing(2),
  flexGrow: 0,
  flexShrink: 0,
  flexBasis: '100%',
  minWidth: 300
}));

const OwnerStyled = styled(Typography)(({ theme }) => ({
  backgroundColor: theme.palette.background.default,
  borderRadius: 4,
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  height: '32px',
  padding: `0 ${theme.spacing(2)}`,
  marginBottom: theme.spacing(1.5),
  transform: 'translateY(-4px)'
}));

const ButtonInviteStyled = styled(Button, { name: 'ButtonInviteStyled' })(
  ({ theme }) => ({
    height: '100%',
    borderColor: theme.palette.primary.main,
    '& .MuiButton-startIcon': {
      marginLeft: 0
    }
  })
);

const LocationStyled = styled('div', { name: 'LocationStyled' })(
  ({ theme }) => ({
    marginTop: theme.spacing(1.5),
    display: 'flex'
  })
);

const LocationTextStyled = styled('div', { name: 'LocationTextStyled' })(
  ({ theme }) => ({})
);

const LinkStyled = styled(Box, { name: 'LinkStyled' })(({ theme }) => ({
  paddingTop: theme.spacing(0.5),
  cursor: 'pointer',
  fontSize: theme.mixins.pxToRem(13),
  color: theme.palette.primary.main,
  margin: theme.spacing(0, 0.5)
}));

const ActionGroupStyled = styled(Box, {
  name: 'ActionGroup',
  shouldForwardProp: props => props !== 'isEnd'
})<{ isEnd?: boolean }>(({ theme, isEnd }) => ({
  height: theme.spacing(5),
  display: isEnd ? 'flex' : 'inline-flex',
  marginTop: theme.spacing(2),
  [theme.breakpoints.down('xs')]: {
    display: 'flex'
  },
  [theme.breakpoints.down('sm')]: {
    width: 'calc(100% - 56px)',
    '& > .MuiButtonBase-root': {
      justifyContent: 'inherit',
      paddingLeft: theme.spacing(2),
      paddingRight: theme.spacing(2),
      '& .ico-caret-down': {
        marginLeft: 'auto'
      }
    }
  },
  '& >*': {
    marginRight: `${theme.spacing(1)} !important`
  }
}));

export default function EventDetail({
  handleAction,
  identity,
  state,
  item,
  user
}: Props) {
  const classes = useStyles();
  const {
    i18n,
    ItemActionMenu,
    ItemDetailInteraction,
    useGetItems,
    useSession,
    getSetting,
    navigate
  } = useGlobal();

  const { user: authUser, loggedIn } = useSession();
  const isOwner = authUser?.id === user?.id;
  const theme = useTheme();
  const settingTime = getSetting('event.default_time_format') as number;

  const categories = useGetItems<{ id: number; name: string }>(
    item?.categories
  );

  const attachments = useGetItems<AttachmentItemShape>(item?.attachments);

  if (!item || isEmpty(item)) return null;

  const { rsvp, description, is_pending, location } = item;

  const [startTime, endTime] = mappingTimeDisplay(
    item.start_time,
    item.end_time,
    settingTime === SETTING_24H
  );

  const isEnd = isEventEnd(item.end_time);

  const rsvpButton = !isOwner ? (
    <InterestedButton
      disabled={isEqual(item.extra?.can_rsvp, false)}
      identity={identity}
      handleAction={handleAction}
      rsvp={rsvp}
    />
  ) : null;

  const onViewMap = () => {
    const north = location.lat;
    const west = location.lng;
    const east = location.lng;
    const south = location.lat;

    navigate({
      pathname: '/event/search-map/',
      search: `?${BOUNDS_NORTH}=${north}&${BOUNDS_EAST}=${east}&${BOUNDS_WEST}=${west}&${BOUNDS_SOUTH}=${south}`
    });
  };

  return (
    <Block testid={`detailview ${item.resource_name}`}>
      <BlockContent>
        <div className={classes.root}>
          <div className={classes.viewContainer}>
            <div className={classes.headingWrapper}>
              <div className={classes.calendar}>
                <div className={classes.month}>
                  <FormatDate
                    data-testid="startDate"
                    value={item.start_time}
                    format="MMM"
                  />
                </div>
                <div className={classes.day}>
                  <FormatDate
                    data-testid="startTime"
                    value={item.start_time}
                    format="DD"
                  />
                </div>
                <div className={classes.year}>
                  <FormatDate
                    data-testid="startYear"
                    value={item.start_time}
                    format="YYYY"
                  />
                </div>
              </div>
              <div className={classes.heading}>
                {isOwner ? (
                  <OwnerStyled>
                    <Typography
                      variant="body2"
                      fontWeight={600}
                      color="text.hint"
                    >
                      {i18n.formatMessage({ id: 'your_event' })}
                    </Typography>
                  </OwnerStyled>
                ) : null}
                <div className={classes.categories}>
                  <CategoryList
                    to="/event/category"
                    data={categories}
                    sx={{ mb: 1, mr: 2 }}
                  />
                </div>
                <ItemTitle variant="h3" component={'div'} pr={2} showFull>
                  <div className={classes.itemFlag}>
                    <FeaturedFlag variant="itemView" value={item.is_featured} />
                    <SponsorFlag variant="itemView" value={item.is_sponsor} />
                    <PendingFlag variant="itemView" value={is_pending} />
                  </div>
                  <Typography
                    component="h1"
                    variant="h3"
                    className={classes.itemTitle}
                    sx={{ display: 'inline' }}
                  >
                    {item.title}
                  </Typography>
                </ItemTitle>
              </div>
            </div>
            <div className={classes.timeLocation}>
              <div className={classes.time}>
                <LineIcon icon="ico-clock-o" />
                {startTime &&
                  i18n.formatMessage(
                    {
                      id: 'event_start_at'
                    },
                    { value: startTime }
                  )}
                {endTime &&
                  i18n.formatMessage(
                    {
                      id: 'event_end_at'
                    },
                    { value: endTime }
                  )}
              </div>
              {location?.lat && location?.lng && (
                <LocationStyled>
                  <LineIcon icon="ico-checkin-o" />
                  <LocationTextStyled>
                    <div>{location?.address}</div>
                    <Box sx={{ display: 'flex' }}>
                      <LinkStyled component="span" onClick={onViewMap}>
                        {i18n.formatMessage({ id: 'view_on_map' })}
                      </LinkStyled>
                      {'·'}
                      <LinkStyled
                        component="a"
                        href={`http://maps.google.com/?q=${location?.address}`}
                        target="_blank"
                      >
                        {i18n.formatMessage({ id: 'view_on_google_maps' })}
                      </LinkStyled>
                    </Box>
                  </LocationTextStyled>
                </LocationStyled>
              )}
              {item.event_url && (
                <div className={classes.location}>
                  <LineIcon icon="ico-link" />
                  <RouteLink to="" href={item.event_url} target="_blank">
                    {item.event_url}
                  </RouteLink>
                </div>
              )}
              {item.privacy_detail !== null && (
                <div className={classes.location}>
                  <PrivacyIcon
                    value={item?.privacy}
                    item={item?.privacy_detail}
                    privacyText
                  />
                </div>
              )}
            </div>
            {loggedIn ? (
              <ActionGroupStyled isEnd={isEnd}>
                {!isEnd ? (
                  rsvpButton
                ) : (
                  <Button
                    className={classes.eventIsEnd}
                    variant="contained"
                    disabled
                    sx={{
                      backgroundColor: `${theme.palette.background.default} !important`,
                      color: `${theme.palette.text.hint} !important`,
                      flex: 1,
                      minWidth: 0
                    }}
                  >
                    {i18n.formatMessage({ id: 'event_is_end' })}
                  </Button>
                )}
                <Box>
                  <ItemActionMenu
                    menuName="itemInviteMenu"
                    state={state}
                    handleAction={handleAction}
                    className={classes.actionMenu}
                    control={
                      <ButtonInviteStyled
                        size="small"
                        variant="outlined"
                        component="h5"
                        startIcon={
                          <LineIcon
                            sx={{ marginLeft: '0 !important' }}
                            icon={'ico-envelope'}
                          />
                        }
                      >
                        <span>{i18n.formatMessage({ id: 'invite' })}</span>
                      </ButtonInviteStyled>
                    }
                  />
                </Box>
                <Box>
                  <ItemActionMenu
                    identity={identity}
                    state={state}
                    handleAction={handleAction}
                    className={classes.actionMenu}
                    control={
                      <IconButtonAction
                        color="primary"
                        variant={'outlined-square'}
                        size="medium"
                      >
                        <LineIcon icon={'ico-dottedmore-o'} />
                      </IconButtonAction>
                    }
                  />
                </Box>
              </ActionGroupStyled>
            ) : null}
            <Box component="div" mt={3} className={classes.itemContent}>
              <HtmlViewer html={description} />
            </Box>
            {attachments?.length > 0 && (
              <>
                <AttachmentTitle>
                  {i18n.formatMessage({ id: 'attachments' })}
                </AttachmentTitle>
                <Attachment>
                  {attachments.map(item => (
                    <AttachmentItemWrapper key={item?.id.toString()}>
                      <AttachmentItem
                        fileName={item?.file_name}
                        downloadUrl={item?.download_url}
                        isImage={item?.is_image}
                        fileSizeText={item?.file_size_text}
                        size="large"
                        image={item?.image}
                      />
                    </AttachmentItemWrapper>
                  ))}
                </Attachment>
              </>
            )}
            <ItemDetailInteraction
              identity={identity}
              state={state}
              handleAction={handleAction}
              hideListComment
              hideComposerInListComment
            />
          </div>
        </div>
      </BlockContent>
    </Block>
  );
}

EventDetail.displayName = 'EventDetail';

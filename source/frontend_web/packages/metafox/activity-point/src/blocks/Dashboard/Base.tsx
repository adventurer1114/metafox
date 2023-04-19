import { ActivityPointItem, APP_ACTIVITY } from '@metafox/activity-point';
import {
  BlockViewProps,
  ButtonLink,
  useGlobal,
  useResourceAction
} from '@metafox/framework';
import { Typography, Tooltip, Box, styled } from '@mui/material';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import React, { useEffect } from 'react';
import { LineIcon, UserAvatar } from '@metafox/ui';
import ErrorPage from '@metafox/core/pages/ErrorPage/Page';

export type Props = BlockViewProps;

const StatisticPointWrapperStyled = styled(Box, {
  name: 'StatisticPointWrapper'
})(() => ({
  display: 'flex',
  flexWrap: 'wrap',
  marginBottom: '-48px',
  marginTop: '40px'
}));

const StatisticPointStyled = styled(Box, { name: 'StatisticPoint' })(
  ({ theme }) => ({
    paddingBottom: '48px',
    width: '25%',
    [theme.breakpoints.down('sm')]: {
      width: '50%'
    }
  })
);

const Wrapper = styled(Box, { name: 'Wrapper' })(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  '& .MuiAvatar-root': {
    marginRight: theme.spacing(4)
  },
  [theme.breakpoints.down('sm')]: {
    flexDirection: 'column',
    alignItems: 'flex-start',
    '& .MuiAvatar-root': {
      marginRight: theme.spacing(0),
      marginBottom: theme.spacing(2)
    }
  }
}));

const StatisticPoint = ({ data }: { data: ActivityPointItem }) => {
  const { label, value, hint } = data;

  return (
    <StatisticPointStyled>
      <Typography variant="body1" color="text.secondary" sx={{ mb: 1 }}>
        {label}
        <Tooltip title={hint} sx={{ pl: 1 }}>
          <LineIcon icon=" ico-question-circle-o" />
        </Tooltip>
      </Typography>
      <Typography variant="h1" sx={{ fontWeight: 'fontWeightRegular' }}>
        {value}
      </Typography>
    </StatisticPointStyled>
  );
};

export default function Base({
  title,
  emptyPage = 'core.block.no_results',
  ...rest
}: Props) {
  const { useFetchDetail, usePageParams, i18n, jsxBackend, dispatch } =
    useGlobal();
  const pageParams = usePageParams();

  const dataSource = useResourceAction(
    APP_ACTIVITY,
    APP_ACTIVITY,
    'getStatistic'
  );

  const [data, loading, error, response] = useFetchDetail({
    dataSource,
    pageParams: { id: pageParams.authId, purchase_id: pageParams.purchase_id }
  });

  useEffect(() => {
    if (!response?.data?.message) return;

    dispatch({ type: '@handleActionFeedback', payload: response });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [response]);

  let content = null;

  const { items, user } = Object.assign({}, data);

  if (!items || !user) content = jsxBackend.render({ component: emptyPage });

  if (items && user)
    content = (
      <>
        <Wrapper>
          <UserAvatar user={user} size={168} />
          <Box>
            <Typography variant="h2">{user.full_name}</Typography>
            <Typography
              variant="subtitle1"
              color="text.hint"
              sx={{ fontWeight: 'fontWeightRegular' }}
            >
              {user.location}
            </Typography>
            <ButtonLink
              sx={{ mt: 2 }}
              variant="outlined"
              startIcon={<LineIcon icon="ico-text-file-search" />}
              to={'/activitypoint/transactions-history'}
            >
              {i18n.formatMessage({ id: 'view_all_transactions' })}
            </ButtonLink>
          </Box>
        </Wrapper>
        <StatisticPointWrapperStyled>
          {items.map((item, index) => (
            <StatisticPoint data={item} key={index} />
          ))}
        </StatisticPointWrapperStyled>
      </>
    );

  return (
    <Block testid="activityPointBlock" {...rest}>
      <BlockHeader title={title} />
      <BlockContent>
        <Box sx={{ p: 4 }}>
          <ErrorPage loading={loading} error={error}>
            {content}
          </ErrorPage>
        </Box>
      </BlockContent>
    </Block>
  );
}

Base.displayName = 'ActivityPoint_Dashboard';

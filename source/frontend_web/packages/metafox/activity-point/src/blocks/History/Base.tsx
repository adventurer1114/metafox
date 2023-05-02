import { APP_ACTIVITY } from '@metafox/activity-point';
import {
  BlockViewProps,
  useGlobal,
  useIsMobile,
  useResourceAction,
  useResourceForm
} from '@metafox/framework';
import { styled, Box, Typography, Grid } from '@mui/material';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import React from 'react';
import { FormBuilder } from '@metafox/form';
import { whenParamRules } from '@metafox/utils';
import qs from 'querystring';

export type Props = BlockViewProps;

const gridTitle = [
  {
    label: 'app',
    grid: 2
  },
  {
    label: 'point',
    grid: 1
  },
  {
    label: 'action',
    grid: 5
  },
  {
    label: 'type',
    grid: 1
  },
  {
    label: 'id',
    grid: 1
  },
  {
    label: 'date',
    grid: 2
  }
];

const TitleStyled = styled(Grid, { name: 'TitleStyled' })(({ theme }) => ({
  display: 'flex',
  padding: theme.spacing(3, 2, 2)
}));

const ContentWrapper = styled(Box, {
  name: 'ContentWrapper'
})(({ theme }) => ({
  padding: theme.spacing(3, 2, 2),
  [theme.breakpoints.down('md')]: {
    padding: theme.spacing(0)
  }
}));

export default function Base({ title, ...rest }: Props) {
  const { usePageParams, navigate, jsxBackend, i18n } = useGlobal();
  const pageParams = usePageParams();
  const isMobile = useIsMobile();

  const dataSource = useResourceAction(
    APP_ACTIVITY,
    'activitypoint_transaction',
    'viewAll'
  );

  const formSchema = useResourceForm(
    APP_ACTIVITY,
    'activitypoint_transaction',
    'search'
  );

  const ListView = jsxBackend.get('core.block.mainListing');

  const submitFilter = (values, form) => {
    const apiRules = dataSource.apiRules;

    const params = whenParamRules(values, apiRules);

    navigate(`?${qs.stringify(params)}`, { replace: true });
    form.setSubmitting(false);
  };

  return (
    <Block testid="activityPointBlock" {...rest}>
      <BlockHeader title={title}></BlockHeader>
      <BlockContent {...rest}>
        <ContentWrapper>
          <FormBuilder
            navigationConfirmWhenDirty={false}
            formSchema={formSchema}
            onSubmit={submitFilter}
          />
          {!isMobile ? (
            <TitleStyled container>
              {gridTitle.map((title, index) => (
                <Grid item key={index} xs={title.grid}>
                  <Typography variant="h5">
                    {i18n.formatMessage({ id: title.label })}
                  </Typography>
                </Grid>
              ))}
            </TitleStyled>
          ) : null}
          <Box sx={isMobile && { mt: 3 }}>
            {React.createElement(ListView, {
              itemView: 'activitypoint.itemView.transaction',
              dataSource,
              emptyPage: 'core.itemView.no_content_history_point',
              pageParams,
              blockLayout: 'App List - Record Table',
              itemLayout: 'Record Item - Table',
              gridLayout: 'Record Item - Table'
            })}
          </Box>
        </ContentWrapper>
      </BlockContent>
    </Block>
  );
}

Base.displayName = 'ActivityPoint_History';

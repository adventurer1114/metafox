import {
  BlockViewProps,
  useGlobal,
  useResourceAction,
  useResourceForm
} from '@metafox/framework';
import { styled, Box, Typography, Grid } from '@mui/material';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import React from 'react';
import { FormBuilder } from '@metafox/form';
import { whenParamRules } from '@metafox/utils';
import qs from 'querystring';
import { APP_NAME } from '@metafox/advertise/constants';

export type Props = BlockViewProps;

const gridTitle = [
  {
    label: 'title',
    grid: 3
  },
  {
    label: 'start_date',
    grid: 2
  },
  {
    label: 'status',
    grid: 1
  },
  {
    label: 'type',
    grid: 2
  },
  {
    label: 'views',
    grid: 1
  },
  {
    label: 'clicks',
    grid: 1
  },
  {
    label: 'active',
    grid: 2
  }
];

const TitleStyled = styled(Grid, { name: 'TitleStyled' })(({ theme }) => ({
  display: 'flex',
  paddingTop: theme.spacing(3),
  paddingBottom: theme.spacing(2),
  paddingLeft: theme.spacing(2),
  paddingRight: theme.spacing(2)
}));

export default function Base({ title, ...rest }: Props) {
  const { usePageParams, navigate, jsxBackend, i18n } = useGlobal();
  const pageParams = usePageParams();

  const dataSource = useResourceAction(APP_NAME, 'sponsorships', 'viewAll');

  const formSchema = useResourceForm(APP_NAME, 'sponsorships', 'search');

  const ListView = jsxBackend.get('core.block.mainListing');

  const submitFilter = (values, form) => {
    const apiRules = dataSource.apiRules;

    const params = whenParamRules(values, apiRules);

    navigate(`?${qs.stringify(params)}`, { replace: true });
    form.setSubmitting(false);
  };

  return (
    <Block testid="advertiseBlock" {...rest}>
      <BlockHeader title={title}></BlockHeader>
      <BlockContent {...rest}>
        <Box sx={{ p: 2 }}>
          <>
            <FormBuilder
              navigationConfirmWhenDirty={false}
              formSchema={formSchema}
              onSubmit={submitFilter}
            />
            <TitleStyled container>
              {gridTitle.map((title, index) => (
                <Grid item key={index} xs={title.grid}>
                  <Typography variant="h5">
                    {i18n.formatMessage({ id: title.label })}
                  </Typography>
                </Grid>
              ))}
            </TitleStyled>
            {React.createElement(ListView, {
              itemView: 'advertise.itemView.sponsorshipRecord',
              dataSource,
              emptyPage: 'advertise.itemView.no_content_record',
              blockLayout: 'Large Main Lists',
              pageParams,
              gridContainerProps: { spacing: 0 }
            })}
          </>
        </Box>
      </BlockContent>
    </Block>
  );
}

Base.displayName = 'Advertise';

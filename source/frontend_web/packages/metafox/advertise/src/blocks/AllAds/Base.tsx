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
import { RESOURCE_NAME, APP_NAME } from '@metafox/advertise/constants';

export type Props = BlockViewProps;

const gridTitle = [
  {
    label: 'title',
    grid: 3
  },
  {
    label: 'placement',
    grid: 2.5
  },
  {
    label: 'start_date',
    grid: 1.5
  },
  {
    label: 'status',
    grid: 1.5,
    center: true
  },
  {
    label: 'impressions',
    grid: 1,
    center: true
  },
  {
    label: 'clicks',
    grid: 1,
    center: true
  },
  {
    label: 'active',
    grid: 1,
    center: true
  },
  {
    label: null,
    grid: 0.5,
    center: true
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

  const dataSource = useResourceAction(APP_NAME, RESOURCE_NAME, 'viewAll');

  const formSchema = useResourceForm(APP_NAME, RESOURCE_NAME, 'search_form');

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
        <ContentWrapper>
          <FormBuilder
            navigationConfirmWhenDirty={false}
            formSchema={formSchema}
            onSubmit={submitFilter}
          />
          <TitleStyled container>
            {gridTitle.map((item, index) => (
              <Grid item key={index} xs={item.grid}>
                <Typography
                  variant="h5"
                  sx={item.center && { textAlign: 'center' }}
                >
                  {item.label && i18n.formatMessage({ id: item.label })}
                </Typography>
              </Grid>
            ))}
          </TitleStyled>
          {React.createElement(ListView, {
            itemView: 'advertise.itemView.addAdsRecord',
            dataSource,
            emptyPage: 'advertise.itemView.no_content_record',
            blockLayout: 'App List - Record Table',
            itemLayout: 'Record Item - Table',
            gridLayout: 'Record Item - Table',
            pageParams
          })}
        </ContentWrapper>
      </BlockContent>
    </Block>
  );
}

Base.displayName = 'Advertise';

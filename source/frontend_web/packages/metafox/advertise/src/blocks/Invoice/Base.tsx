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
import { APP_NAME } from '../../constants';

export type Props = BlockViewProps;

const gridTitle = [
  {
    label: 'title',
    grid: 3
  },
  {
    label: 'transaction_id',
    grid: 2
  },
  {
    label: 'start_date',
    grid: 2
  },
  {
    label: 'status',
    grid: 2
  },
  {
    label: 'price',
    grid: 2
  },
  {
    label: 'action',
    grid: 1
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

  const dataSource = useResourceAction(
    APP_NAME,
    'advertise_invoice',
    'viewAll'
  );

  const formSchema = useResourceForm(
    APP_NAME,
    'advertise_invoice',
    'search_form'
  );

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
            {gridTitle.map((title, index) => (
              <Grid item key={index} xs={title.grid}>
                <Typography variant="h5">
                  {i18n.formatMessage({ id: title.label })}
                </Typography>
              </Grid>
            ))}
          </TitleStyled>
          {React.createElement(ListView, {
            itemView: 'advertise.itemView.invoiceRecord',
            dataSource,
            emptyPage: 'advertise.itemView.no_content_record',
            pageParams,
            blockLayout: 'App List - Record Table',
            itemLayout: 'Record Item - Table',
            gridLayout: 'Record Item - Table'
          })}
        </ContentWrapper>
      </BlockContent>
    </Block>
  );
}

Base.displayName = 'Advertise';

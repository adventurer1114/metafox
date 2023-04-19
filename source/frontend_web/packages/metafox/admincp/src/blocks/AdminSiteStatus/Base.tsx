import { RouteLink, useGlobal } from '@metafox/framework';
import { Block, BlockContent, BlockHeader, BlockTitle } from '@metafox/layout';
import { InformationList, UIBlockViewProps } from '@metafox/ui';
import { Skeleton, styled } from '@mui/material';
import React from 'react';

const BlockContentWrapper = styled(BlockContent, {
  slot: 'BlockContentWrapper'
})(({ theme }) => ({
  '& span': {
    fontSize: 13
  }
}));

export interface Props extends UIBlockViewProps {}

export default function AdminItemStats({ blockProps, title }: Props) {
  const { useFetchDetail, i18n, moment } = useGlobal();

  const [data, loading] = useFetchDetail({
    dataSource: {
      apiUrl: 'admincp/dashboard/site-status'
    }
  });

  if (loading) {
    return (
      <Block>
        <BlockHeader title={i18n.formatMessage({ id: title })} />
        <BlockContent>
          <Skeleton variant="text" width={200} />
          <Skeleton variant="text" width={200} />
          <Skeleton variant="text" width={250} />
        </BlockContent>
      </Block>
    );
  }

  const {
    license_status,
    license_status_style,
    latest_version,
    license_expired_at,
    can_upgrade,
    version
  } = Object.assign({}, data);

  let description = `${i18n.formatMessage({
    id: 'latest_version'
  })}: ${latest_version}`;

  if (!can_upgrade) {
    description = i18n.formatMessage({ id: 'your_site_is_up_to_date' });
  } else {
    description = [
      description,
      ' · ',
      <RouteLink to="/admincp/app/upgrade">Upgrade</RouteLink>
    ];
  }

  const infoItems = [
    {
      icon: 'ico-info-circle-alt-o',
      info: `${i18n.formatMessage({
        id: 'license_status'
      })}: `,
      status: `${license_status}`,
      class_style: `${license_status_style}`
    },
    license_expired_at
      ? {
          icon: 'ico-sandclock-end-o',
          info: `${i18n.formatMessage(
            {
              id: 'support_expire_on_description'
            },
            { date: moment(license_expired_at).format('MMMM DD, yyyy') }
          )}`
        }
      : null,
    {
      icon: 'ico-file-text-alt-o',
      info: `${i18n.formatMessage({
        id: 'site_version'
      })}: ${version}`,
      description
    }
  ].filter(Boolean);

  return (
    <Block>
      <BlockHeader>
        <BlockTitle>{i18n.formatMessage({ id: title })}</BlockTitle>
      </BlockHeader>
      <BlockContentWrapper>
        {infoItems.length > 0 && <InformationList values={infoItems} />}
      </BlockContentWrapper>
    </Block>
  );
}

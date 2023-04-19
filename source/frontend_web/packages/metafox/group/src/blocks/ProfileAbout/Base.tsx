import ErrorBoundary from '@metafox/core/pages/ErrorPage/Page';
import {
  BlockViewProps,
  Link,
  useGlobal,
  useResourceAction
} from '@metafox/framework';
import { APP_GROUP, RESOURCE_GROUP } from '@metafox/group';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import { InformationList, TruncateText } from '@metafox/ui';
import { Box, Skeleton, styled } from '@mui/material';
import React from 'react';

export interface Props extends BlockViewProps {}

const StyledTextInfo = styled('div')(({ theme }) => ({
  marginBottom: theme.spacing(1.5),
  fontSize: theme.mixins.pxToRem(15),
  color: theme.palette.text.primary
}));

export default function UserProfileAboutBlock({ title }: Props) {
  const { useFetchDetail, usePageParams, jsxBackend, i18n } = useGlobal();
  const pageParams = usePageParams();
  const dataSource = useResourceAction(APP_GROUP, RESOURCE_GROUP, 'groupInfo');
  const [data, loading, error] = useFetchDetail({
    dataSource,
    pageParams,
    preventReload: true
  });

  const LoadingSkeleton = () => (
    <Box>
      <Skeleton width={'100%'} />
      <Skeleton width={'100%'} />
      <Skeleton width={'100%'} />
    </Box>
  );

  const NoContentBlock = jsxBackend.get('core.block.no_content');

  const { text, location, phone, total_member, external_link, category } =
    Object.assign({}, data);

  const textCategory = (
    <Link to={category?.link || category?.url} color="primary">
      {category?.name}
    </Link>
  );

  const infoItems = [
    {
      icon: 'ico-checkin-o',
      info: location,
      value: !!location
    },
    {
      icon: 'ico-layers-o',
      info: textCategory,
      value: !!textCategory
    },
    {
      icon: 'ico-phone-o',
      info: phone,
      value: !!phone
    },
    {
      icon: 'ico-user-two-men-o',
      info: i18n.formatMessage(
        {
          id: 'people_joined_group'
        },
        {
          value: total_member
        }
      ),
      value: !!total_member
    },
    {
      icon: 'ico-globe-alt-o',
      info: external_link,
      value: !!external_link
    }
  ].filter(item => item.value);

  return (
    <Block>
      <BlockHeader title={title} />
      <BlockContent>
        <ErrorBoundary
          loading={loading}
          error={error}
          loadingComponent={LoadingSkeleton}
          emptyComponent={data ? null : NoContentBlock}
        >
          <Box>
            {text && (
              <StyledTextInfo>
                <TruncateText lines={3}>
                  <HtmlViewer html={text} />
                </TruncateText>
              </StyledTextInfo>
            )}
            <div>
              <InformationList values={infoItems} />
            </div>
          </Box>
        </ErrorBoundary>
      </BlockContent>
    </Block>
  );
}

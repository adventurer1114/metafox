import { useGlobal } from '@metafox/framework';
import { Block, BlockContent, BlockHeader, BlockTitle } from '@metafox/layout';
import { Box, Dialog, Link, Skeleton, styled } from '@mui/material';
import { FormatNumber } from '@metafox/ui';
import React from 'react';

const Title = styled('h3', { slot: 'Title' })(({ theme }) => ({
  color: theme.palette.grey[600],
  marginTop: 0,
  fontSize: 16
}));

const ItemContent = styled(Box, { slot: 'ItemContent' })(({ theme }) => ({
  display: 'flex',
  justifyContent: 'space-between',
  alignItems: 'center',
  marginLeft: theme.spacing(1),
  padding: theme.spacing(1.5, 0),
  borderBottom: '1px solid #eaeaea',
  color: theme.palette.grey['A700'],
  fontSize: 15
}));

const ItemWrapper = styled(Box, { slot: 'ItemWrapper' })(({ theme }) => ({
  paddingTop: theme.spacing(2),
  '&:first-of-type': {
    paddingTop: 0
  }
}));

const Wrapper = styled(Box, { slot: 'Box' })(({ theme }) => ({
  overflow: 'auto'
}));

const ViewMore = styled(Link, { slot: 'ViewMore' })(({ theme }) => ({
  color: '#2681d5',
  paddingTop: theme.spacing(2),
  paddingLeft: theme.spacing(1),
  display: 'block'
}));

const DialogWrapper = styled(Dialog, { slot: 'DialogWrapper' })(
  ({ theme }) => ({
    '& .MuiBox-root': {
      overflow: 'auto',
      margin: 0
    }
  })
);

export interface Props extends UIBlockViewProps {}

export default function AdminItemStats({ blockProps, title }: Props) {
  const { useFetchDetail, i18n } = useGlobal();

  const [data, loading] = useFetchDetail({
    dataSource: {
      apiUrl: 'admincp/dashboard/deep-statistic'
    }
  });

  const [open, setOpen] = React.useState(false);

  const handleClickOpen = () => {
    setOpen(true);
  };

  const handleClose = (value: string) => {
    setOpen(false);
  };

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

  const sum = data?.site_stat.length + data?.today.length;

  const filtered = [];

  data.today.map((item, index) => {
    if (5 - data?.site_stat.length > index) {
      filtered.push(item);
    }

    return filtered;
  });

  function SimpleDialog(props: SimpleDialogProps) {
    const { onClose, selectedValue, open } = props;

    const handleClose = () => {
      onClose(selectedValue);
    };

    return (
      <DialogWrapper
        fullWidth="true"
        maxWidth="xs"
        onClose={handleClose}
        open={open}
      >
        <Block>
          <Wrapper>
            <BlockHeader>
              <BlockTitle>{i18n.formatMessage({ id: title })}</BlockTitle>
            </BlockHeader>
            <BlockContent>
              {!loading && data?.site_stat.length ? (
                <ItemWrapper>
                  <Title>
                    {' '}
                    {i18n.formatMessage({ id: 'site_statistics' })}{' '}
                  </Title>
                  {data.site_stat.map((item, index) => (
                    <ItemContent key={index}>
                      <Box>{item.label}</Box>
                      <Box>
                        <FormatNumber
                          value={item.value}
                          formatOptions={item?.format_options}
                        />
                      </Box>
                    </ItemContent>
                  ))}
                </ItemWrapper>
              ) : null}
              {!loading && data?.today.length ? (
                <ItemWrapper>
                  <Title>
                    {' '}
                    {i18n.formatMessage({ id: 'today_statistics' })}{' '}
                  </Title>
                  {data.today.map((item, index) => (
                    <ItemContent key={index}>
                      <Box>{item.label}</Box>
                      <Box>
                        <FormatNumber
                          value={item.value}
                          formatOptions={item?.format_options}
                        />
                      </Box>
                    </ItemContent>
                  ))}
                </ItemWrapper>
              ) : null}
            </BlockContent>
          </Wrapper>
        </Block>
      </DialogWrapper>
    );
  }

  return (
    <Block>
      <Wrapper>
        <BlockHeader>
          <BlockTitle>{i18n.formatMessage({ id: title })}</BlockTitle>
        </BlockHeader>
        <BlockContent>
          {!loading && data?.site_stat.length ? (
            <ItemWrapper>
              <Title> {i18n.formatMessage({ id: 'site_statistics' })} </Title>
              {data.site_stat.map((item, index) => (
                <ItemContent key={index}>
                  <Box>{item.label}</Box>
                  <Box>
                    <FormatNumber
                      value={item.value}
                      formatOptions={item?.format_options}
                    />
                  </Box>
                </ItemContent>
              ))}
            </ItemWrapper>
          ) : null}
          {!loading && filtered.length ? (
            <ItemWrapper>
              <Title> {i18n.formatMessage({ id: 'today_statistics' })} </Title>
              {filtered.map((item, index) => (
                <ItemContent key={index}>
                  <Box>{item.label}</Box>
                  <Box>
                    <FormatNumber
                      value={item.value}
                      formatOptions={item?.format_options}
                    />
                  </Box>
                </ItemContent>
              ))}
            </ItemWrapper>
          ) : null}
          {sum > 5 ? (
            <ViewMore onClick={handleClickOpen}>
              {i18n.formatMessage({ id: 'view_more' })}
            </ViewMore>
          ) : null}
          <SimpleDialog open={open} onClose={handleClose} />
        </BlockContent>
      </Wrapper>
    </Block>
  );
}

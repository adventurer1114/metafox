/* eslint-disable react/jsx-no-useless-fragment */
/* eslint-disable no-useless-concat */
/* eslint-disable @typescript-eslint/no-unused-vars */
/* eslint-disable react-hooks/rules-of-hooks */
import { Block, BlockContent } from '@metafox/layout';
import { UIBlockViewProps } from '@metafox/ui';
import React, { useEffect } from 'react';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js';
import { Line } from 'react-chartjs-2';
import {
  Box,
  FormControl,
  InputLabel,
  MenuItem,
  Select,
  SelectChangeEvent,
  styled
} from '@mui/material';
import { useGlobal, useSession } from '@metafox/framework';
import { isObject } from 'lodash';
import moment from 'moment';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
);

const BlockContentWrapper = styled(BlockContent, { slot: 'BlockContent' })(
  ({ theme }) => ({
    height: '100%',
    display: 'flex',
    flexDirection: 'column',
    justifyContent: 'space-between'
  })
);

const HeaderWrapper = styled('div', { slot: 'HeaderWrapper' })(({ theme }) => ({
  display: 'flex',
  justifyContent: 'space-between',
  alignItems: 'center',
  marginBottom: theme.spacing(3),
  [theme.breakpoints.down('lg')]: {
    flexDirection: 'column'
  }
}));

const HeaderLeftWrapper = styled('div', { slot: 'HeaderLeftWrapper' })(
  ({ theme }) => ({
    height: 'auto',
    [theme.breakpoints.down('lg')]: {
      marginBottom: theme.spacing(2)
    }
  })
);

const HeaderRightWrapper = styled('div', { slot: 'HeaderRightWrapper' })(
  ({ theme }) => ({
    height: 'auto',
    '& .MuiSelect-select': {
      paddingTop: theme.spacing(1),
      paddingBottom: theme.spacing(1)
    }
  })
);

const TitleHeader = styled('h3', { slot: 'TitleHeader' })(({ theme }) => ({
  fontSize: theme.spacing(2.5),
  margin: theme.spacing(0),
  fontWeight: 'normal'
}));

const Time = styled('span', { slot: 'Time' })(({ theme }) => ({
  color: theme.palette.grey[700],
  height: theme.spacing(2),
  display: 'block'
}));

const Chart = styled('div', { slot: 'Chart' })(({ theme }) => ({
  color: theme.palette.grey[700],
  position: 'relative',
  '&.loading canvas': {
    opacity: 0.1
  }
}));

const Loading = styled(Box, { slot: 'Loading' })(({ theme }) => ({
  height: '100%',
  width: '100%',
  justifyContent: 'center',
  display: 'flex',
  position: 'absolute'
}));

export interface Props extends UIBlockViewProps {}

export default function AdminItemStats({ blockProps, title }: Props) {
  const { useFetchDetail, apiClient, jsxBackend, i18n } = useGlobal();
  const [chartData, setChartData] = React.useState([]);
  const [selectedType, setSelectedType] = React.useState('');
  const [periodType, setPeriodType] = React.useState('');
  const [view, setView] = React.useState('1d');
  const [loading, setLoading] = React.useState(true);

  const session = useSession();

  const [typeList] = useFetchDetail<object>({
    dataSource: {
      apiUrl: 'admincp/dashboard/stat-type'
    }
  });

  const getData = React.useCallback(
    (name: string, period: string) => {
      setLoading(true);
      setView(period);
      apiClient
        .request({
          method: 'get',
          url: `admincp/dashboard/chart?name=${name}&period=${period}`
        })
        .then(rs => {
          setChartData(rs.data.data);
          setTimeout(() => {
            setLoading(false);
          }, 300);
        });
    },
    [apiClient]
  );

  const handleTypeChage = (event: SelectChangeEvent) => {
    setSelectedType(event.target.value);
    getData(event.target.value, periodType);
  };

  const handlePeriodChage = (event: SelectChangeEvent) => {
    setPeriodType(event.target.value);
    getData(selectedType, event.target.value);
  };

  useEffect(() => {
    if (!isObject(typeList)) return;

    setSelectedType('user');
    setPeriodType('1d');
    getData('user', '1d');
  }, [getData, typeList]);

  const labels = [];
  const values = [];

  chartData?.forEach(item => {
    values.push(item.data);

    if (view === '1d') {
      labels.push(moment(item?.date).format('ll'));

      return;
    }

    labels.push(item?.date);
  });

  const data = {
    labels,
    datasets: [
      {
        data: values,
        borderColor: '#2682d5',
        backgroundColor: '#2682d5'
      }
    ]
  };

  const filtered = chartData.filter(chartData => {
    return chartData.data > 0;
  });

  const options = {
    responsive: true,
    plugins: {
      legend: false,
      title: {
        display: false
      }
    }
  };

  return (
    <Block>
      <BlockContentWrapper>
        <HeaderWrapper>
          <HeaderLeftWrapper>
            <TitleHeader>
              {i18n.formatMessage(
                { id: 'welcome_back_username' },
                { username: session.user.full_name }
              )}
            </TitleHeader>
            <Time>
              {!loading ? (
                <>
                  {labels[0]} - {labels[labels.length - 1]}{' '}
                </>
              ) : null}
            </Time>
          </HeaderLeftWrapper>
          <HeaderRightWrapper>
            <FormControl sx={{ m: 1, minWidth: 200 }} size="small">
              <InputLabel>{i18n.formatMessage({ id: 'type' })}</InputLabel>
              <Select
                value={selectedType}
                label="Type"
                onChange={handleTypeChage}
              >
                {typeList?.types?.map((item, index) => (
                  <MenuItem key={index} value={item.value}>
                    {item.label}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
            <FormControl sx={{ m: 1, minWidth: 200 }} size="small">
              <InputLabel>{i18n.formatMessage({ id: 'view' })}</InputLabel>
              <Select
                value={periodType}
                label="View"
                onChange={handlePeriodChage}
              >
                {typeList?.period?.map((item, index) => (
                  <MenuItem key={index} value={item.value}>
                    {item.label}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </HeaderRightWrapper>
        </HeaderWrapper>
        <Chart className={loading ? 'loading' : ''}>
          <>
            {loading ? (
              <Loading>
                {jsxBackend.render({ component: 'form.DefaultLoading' })}
              </Loading>
            ) : null}
            {filtered.length !== 0 ? (
              <>
                {' '}
                <Line width={500} data={data} options={options} />{' '}
              </>
            ) : (
              <Line
                width={500}
                data={data}
                options={{
                  ...options,
                  scales: {
                    y: {
                      min: 0
                    }
                  }
                }}
              />
            )}
          </>
        </Chart>
      </BlockContentWrapper>
    </Block>
  );
}

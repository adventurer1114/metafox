import CheckIcon from '@mui/icons-material/Check';
import ErrorIcon from '@mui/icons-material/Error';
import { Box, Button, LinearProgress, Typography } from '@mui/material';
import { ProcessStepShape } from './types';
import React from 'react';
import { useGlobal } from '@metafox/framework';
import { get } from 'lodash';
import { getErrString } from '@metafox/utils';
import HtmlViewer from '@metafox/html-viewer';

export default function ProcessStep({
  step,
  active,
  onSuccess
}: {
  active: boolean;
  step: ProcessStepShape;
  onSuccess: () => void;
}) {
  const { apiClient, handleActionError } = useGlobal();
  const { dataSource, data, enableReport, params, dryRun } = step;
  const [error, setError] = React.useState();
  const [success, setSuccess] = React.useState<boolean>();
  const [report, setReport] = React.useState<string>();

  const method = dataSource?.apiMethod ?? 'GET';

  const fetchOrRetry = React.useCallback(() => {
    apiClient
      .request({
        timeout: step.disableUserAbort ? step.timeout ?? 10e3 : 0,
        url: dataSource.apiUrl,
        method,
        params,
        data
      })
      .then(res => {
        if (get(res, 'data.data.retry')) {
          setTimeout(() => fetchOrRetry(), 5e3);
        } else {
          setReport(get(res, 'data.data.message'));
          onSuccess();
          setSuccess(true);
        }
      })
      .catch(err => {
        if (dryRun) {
          setError(getErrString(err));
          setReport(getErrString(err));
          onSuccess();
        } else if (err.code === 'ECONNABORTED') {
          fetchOrRetry();
        } else {
          handleActionError(err);
          setError(getErrString(err));
        }
      });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  React.useEffect(() => {
    if (active) {
      fetchOrRetry();
    }
  }, [active, fetchOrRetry]);

  const handleRetry = () => {
    setError(undefined);
    fetchOrRetry();
  };

  return (
    <Box sx={{}}>
      <Box
        sx={{
          display: 'flex',
          flexDirection: 'row',
          alignItems: 'center',
          height: 40
        }}
      >
        <Box sx={{ display: 'flex', flex: 1 }}>
          <Typography>{step.title}</Typography>
        </Box>
        {active && !error ? (
          <Box sx={{ display: 'block', minWidth: 220, width: '30%' }}>
            <LinearProgress variant="indeterminate" color="primary" />
          </Box>
        ) : null}
        {error ? (
          <Box>
            <ErrorIcon color="error" />
          </Box>
        ) : null}
        {error && !step.disableRetry ? (
          <Button size="small" onClick={handleRetry} title="retry">
            Retry
          </Button>
        ) : null}
        {success ? (
          <Box>
            <CheckIcon color="success" />
          </Box>
        ) : null}
      </Box>
      {enableReport && report ? (
        <HtmlViewer disableNl2br html={report} />
      ) : null}
      <Box></Box>
      <Box sx={{ pl: 2 }}>
        {step.message ? (
          <Typography sx={{ whiteSpace: 'pre-wrap' }}>
            {step.message}
          </Typography>
        ) : null}
      </Box>
    </Box>
  );
}

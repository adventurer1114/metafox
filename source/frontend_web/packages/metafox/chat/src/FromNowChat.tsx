import { convertDateTime } from '@metafox/chat/utils';
import { useGlobal } from '@metafox/framework';
import { Tooltip } from '@mui/material';
import React from 'react';

export type FromNowProps = {
  value: string | number;
  component?: React.ElementType;
  className?: string;
  shorten?: boolean;
  tooltip?: boolean;
};

export default function FromNowChat({
  className,
  value,
  component: As = 'span',
  tooltip = false
}: FromNowProps) {
  const { moment } = useGlobal();

  if (!value) return null;

  const date = moment(value);
  const title = date.format('llll');

  const time = convertDateTime(date);

  return (
    <Tooltip title={title} open={tooltip}>
      <As role="link" className={className} aria-label={title}>
        {time}
      </As>
    </Tooltip>
  );
}

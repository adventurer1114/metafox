import { useGlobal } from '@metafox/framework';
import React from 'react';

export type FormatDateProps = {
  component?: any;
  value: string;
  format?: string;
  'data-testid': string;
  phrase?: string;
};

export default function FormatDate({
  component: As = 'time',
  value,
  format = 'llll',
  'data-testid': testid,
  phrase = ''
}) {
  const { moment, i18n } = useGlobal();
  const text = moment(value).format(format);

  return (
    <As data-testid={testid}>
      {phrase
        ? i18n.formatMessage(
            { id: phrase },
            {
              time: text
            }
          )
        : text}
    </As>
  );
}

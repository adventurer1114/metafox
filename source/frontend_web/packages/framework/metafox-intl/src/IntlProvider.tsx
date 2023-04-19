/**
 * @type: service
 * name: IntlProvider
 */
import { MFOX_LOCALE, useGlobal } from '@metafox/framework';
import { detectBrowserLanguage } from '@metafox/utils';
import React from 'react';
import { createIntl, createIntlCache, RawIntlProvider } from 'react-intl';
import defaultRichTextElements from './defaultRichTextElements';

const cache = createIntlCache();

export default function IntlProvider({ children }) {
  const {
    usePreference,
    manager,
    getSetting,
    useIntlMessages = () => ({}),
    moment
  } = useGlobal();
  const { userLanguage } = usePreference();
  const supports = getSetting<object>('localize.languages');
  // const messages = getConfig<Messages>('messages');
  const messages = useIntlMessages();
  const defaultLocale = MFOX_LOCALE;

  const locale = userLanguage || detectBrowserLanguage(supports) || MFOX_LOCALE;

  const intl = createIntl(
    {
      locale,
      defaultLocale,
      messages,
      defaultRichTextElements,
      onWarn: (warning: string) => {},
      onError: err => {
        // console.warn(err);
      }
    },
    cache
  );

  manager.use({ i18n: intl });

  if (moment) {
    moment.locale([locale, defaultLocale].filter(Boolean));

    // update relative time
    // @todo
    moment.updateLocale(locale, {
      relativeTime: {
        future: 'in %s',
        past: '%s',
        s: 'Now',
        ss: '%ds',
        m: '1m',
        mm: '%dm',
        h: '1h',
        hh: '%dh',
        d: '1d',
        dd: '%dd',
        M: 'a month',
        MM: '%d months',
        y: 'a year',
        yy: '%d years'
      }
    });
  }

  return <RawIntlProvider value={intl}>{children}</RawIntlProvider>;
}

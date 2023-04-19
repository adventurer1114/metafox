/**
 * @type: service
 * name: ThemeProvider
 */
import createCache from '@emotion/cache';
import { CacheProvider } from '@emotion/react';
import { MFOX_LOCALE, RTL_LOCALE, useGlobal } from '@metafox/framework';
import { CssBaseline, useMediaQuery } from '@mui/material';
import createTheme from '@mui/material/styles/createTheme';
import MuiThemeProvider from '@mui/system/ThemeProvider';
import { merge } from 'lodash';
import React from 'react';
import rtlPlugin from 'stylis-plugin-rtl';
import useGlobalStyles from '@metafox/theme-default/GlobalCss.styles';
import * as mixins from './mixins';
import { detectBrowserLanguage } from '@metafox/utils';

type ThemeProviderProps = {
  children: React.ReactNode;
  infoLoader?: React.FC<{ themeId: string }>;
};

const CssGlobal = () => {
  useGlobalStyles();

  return null;
};

// Create rtl cache
const rltCache = createCache({
  key: 'rtl',
  stylisPlugins: [rtlPlugin]
});

const ltrCache = createCache({
  key: 'ltr',
  stylisPlugins: []
});

export default function ThemeProvider(props: ThemeProviderProps) {
  const { children } = props;
  const {
    layoutBackend,
    themeProcessor,
    usePreference,
    eventCenter,
    useLoggedIn,
    getSetting,
    use
  } = useGlobal();
  const [themeConfig, setThemeConfig] = React.useState(
    layoutBackend.getThemeConfig()
  );
  const {
    userLanguage,
    userDirection,
    themeType: _themeType,
    themeId
  } = usePreference();

  const supports = getSetting<object>('localize.languages', { en: 1.0 });

  const langCode = (
    userLanguage ||
    detectBrowserLanguage(supports) ||
    MFOX_LOCALE
  ).toLowerCase();

  const rtl = !!(userDirection === 'rtl'
    ? true
    : RTL_LOCALE.find(x => langCode.startsWith(x)));

  const isLoggedIn = useLoggedIn();
  const prefersDarkMode = useMediaQuery('(prefers-color-scheme: dark)');

  let themeType =
    _themeType === 'auto' && prefersDarkMode ? 'dark' : _themeType;

  if (process.env.MFOX_BUILD_TYPE === 'admincp' || !isLoggedIn) {
    themeType = 'light';
  }

  if (document.body) {
    document.body.dir = rtl ? 'rtl' : 'ltr';
  }

  themeType = themeType === 'dark' ? 'dark' : 'light';

  // / handle theme on changed.
  React.useEffect(() => {
    if (eventCenter) {
      const id = eventCenter.on('onThemeChanged', setThemeConfig);

      return () => eventCenter.off('onThemeChanged', id);
    }
  }, [eventCenter]);

  const theme = React.useMemo(() => {
    const options = JSON.parse(JSON.stringify(themeConfig));

    const themeOptions = merge(
      {
        themeId,
        direction: rtl ? 'rtl' : 'ltr',
        palette: { mode: themeType }
      },
      options.default,
      themeType === 'dark' ? options.dark : {}
    );

    // process theme options

    const theme = createTheme(themeOptions);

    Object.keys(mixins).forEach(name => {
      theme.mixins[name] = mixins[name](theme);
    });

    themeProcessor.process(theme);

    // theme processor
    // collect all theme processor.

    return theme;
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [themeConfig, themeId, themeType, rtl]);

  // root theme ui.
  use({ theme });

  return (
    <CacheProvider value={rtl ? rltCache : ltrCache}>
      <MuiThemeProvider theme={theme}>
        <CssBaseline />
        <CssGlobal />
        {children}
      </MuiThemeProvider>
    </CacheProvider>
  );
}

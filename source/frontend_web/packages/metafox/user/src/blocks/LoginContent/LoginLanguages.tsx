import { MFOX_LOCALE, useGlobal } from '@metafox/framework';
import { detectBrowserLanguage } from '@metafox/utils';
import { MenuItem, Select, styled } from '@mui/material';
import FormControl from '@mui/material/FormControl';
import React from 'react';

const name = 'LoginContent';

const RootStyled = styled('div', { name, slot: 'root' })(({ theme }) => ({
  paddingTop: theme.spacing(2.5),
  paddingBottom: theme.spacing(2),
  display: 'flex',
  justifyContent: 'flex-end',
  '& .MuiSvgIcon-root': {
    color: theme.palette.default.contrastText
  },
  '& .MuiInput-input': {
    color: theme.palette.default.contrastText
  }
}));

const MenuItemStyled = styled(MenuItem, { name })(({ theme }) => ({
  color: theme.palette.text.primary
}));

export default function LoginLanguages() {
  const { usePreference, getSetting, preferenceBackend, navigate } =
    useGlobal();

  const supports = getSetting<object>('localize.languages');

  const { userLanguage } = usePreference();

  if (!supports) return null;

  const value = userLanguage || detectBrowserLanguage(supports) || MFOX_LOCALE;

  const onChange = (evt: any) => {
    preferenceBackend.setAndRemember('userLanguage', evt.target.value);
    navigate(0);
  };

  return (
    <RootStyled>
      <FormControl>
        <Select
          labelId="demo-simple-select-autowidth-label"
          id="demo-simple-select-autowidth"
          value={value}
          onChange={onChange}
          autoWidth
          variant="standard"
          disableUnderline
        >
          {Object.keys(supports).map(langCode => (
            <MenuItemStyled key={langCode} value={langCode}>
              {supports[langCode]}
            </MenuItemStyled>
          ))}
        </Select>
      </FormControl>
    </RootStyled>
  );
}

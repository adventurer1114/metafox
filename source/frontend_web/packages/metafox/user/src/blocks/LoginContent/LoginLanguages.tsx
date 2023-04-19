import { MFOX_LOCALE, useGlobal } from '@metafox/framework';
import { detectBrowserLanguage } from '@metafox/utils';
import { Theme } from '@mui/material';
import FormControl from '@mui/material/FormControl';
import NativeSelect from '@mui/material/NativeSelect';
import { createStyles, makeStyles, withStyles } from '@mui/styles';
import React from 'react';

const useStyles = makeStyles(
  theme => ({
    languages: {
      paddingTop: theme.spacing(2.5),
      paddingBottom: theme.spacing(2.5),
      display: 'flex',
      justifyContent: 'flex-end',
      '& .MuiInput-root': {
        color: theme.palette.default.contrastText
      },
      '& .MuiSvgIcon-root': {
        color: theme.palette.default.contrastText
      }
    }
  }),
  { name: 'LoginContent' }
);

const StyledSelect = withStyles((theme: Theme) =>
  createStyles({
    root: {
      color: '#fff',
      height: 'initial',
      '& option': {
        color: theme.palette.text.primary
      }
    },
    icon: {
      color: '#fff'
    }
  })
)(NativeSelect);

export default function LoginLanguages() {
  const classes = useStyles();
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
    <div className={classes.languages}>
      <FormControl>
        <StyledSelect
          value={value}
          inputProps={{
            name: 'languages',
            id: 'languages'
          }}
          onChange={onChange}
          disableUnderline
        >
          {Object.keys(supports).map(langCode => (
            <option key={langCode} value={langCode}>
              {supports[langCode]}
            </option>
          ))}
        </StyledSelect>
      </FormControl>
    </div>
  );
}

/**
 * @type: block
 * name: user.block.userLoginContent
 * chunkName: boot
 */
import {
  AuthUserShape,
  BlockViewProps,
  createBlock,
  useGlobal,
  useSession
} from '@metafox/framework';
import { Container, LineIcon, Image } from '@metafox/ui';
import { Box, Grid, IconButton, Tooltip, Typography } from '@mui/material';
import clsx from 'clsx';
import React from 'react';
import useStyles from './LoginContent.styles';
import LoginForm from './LoginForm';
import LoginLanguages from './LoginLanguages';
import UserAccessed from './UserAccessed';

export interface LoginContentProps extends BlockViewProps {
  multipleAccess: boolean;
  title?: string;
  subTitle?: string;
  subTitle1?: string;
  subtitle2?: string;
  logo?: string;
  className?: string;
  limit: number;
}

type TestAccount = { email: string; password: string; enabled: boolean };

function LoginContent({ limit = 4 }: LoginContentProps) {
  const classes = useStyles();
  const { dispatch, i18n, getSetting, assetUrl, useTheme, jsxBackend } =
    useGlobal();

  const { accounts } = useSession();
  const [user, setUser] = React.useState<AuthUserShape>();
  const testAccount = getSetting<TestAccount>('testAccount');
  const FooterMenu = jsxBackend.get('core.block.footer');

  const theme = useTheme();
  const logo =
    theme.palette.mode === 'dark'
      ? assetUrl('layout.image_logo_dark')
      : assetUrl('layout.image_logo');

  const isIpad = window.outerWidth < theme.breakpoints.values.md;

  const multipleAccess = accounts && Array.isArray(accounts) && accounts.length;

  const backgroundImage = multipleAccess
    ? assetUrl('layout.image_sign_in_multi_access')
    : assetUrl('layout.image_welcome');

  const addMoreAccount = () => dispatch({ type: 'user/addMoreAccount' });

  if (!accounts || !Array.isArray(accounts)) return null;

  const onSelectUser = (user: AuthUserShape) => setUser(user);

  const removeAccount = (user: AuthUserShape) => {
    dispatch({ type: 'user/removeAccount', payload: user.id });
  };

  return (
    <div className={classes.root}>
      <Container className={classes.container} maxWidth="md">
        <LoginLanguages />
        <Grid
          container
          className={clsx(
            classes.containerGrid,
            multipleAccess && classes.multipleAccess
          )}
        >
          <Grid className={classes.gridLeft} item xs={12} md={6}>
            <div className={classes.welcomeContent}>
              <div className={classes.contentHeader}>
                <img className={classes.logo} src={logo} alt="logo" />
                {!isIpad ? (
                  <Typography
                    className={classes.subTitle}
                    fontWeight={400}
                    variant="subtitle1"
                    color="textSecondary"
                  >
                    {i18n.formatMessage({ id: 'login_slogan_message' })}
                  </Typography>
                ) : null}
                <Image
                  aspectRatio={'11'}
                  src={backgroundImage}
                  backgroundImage
                />
              </div>
              {multipleAccess && !isIpad ? (
                <div className={classes.signedIn}>
                  <Typography
                    variant="subtitle1"
                    paragraph
                    fontWeight={400}
                    color="textSecondary"
                  >
                    {i18n.formatMessage({ id: 'login_previously' })}
                  </Typography>
                  <Box display="flex" flexDirection="row">
                    {accounts.slice(0, limit).map((user, index) => (
                      <Box
                        key={index.toString()}
                        sx={{
                          marginRight: 3,
                          position: 'relative',
                          ':hover .closeBtn': {
                            visibility: 'visible'
                          }
                        }}
                      >
                        <UserAccessed
                          onSelectUser={onSelectUser}
                          user={user as any}
                          size={64}
                        />
                        <Tooltip
                          title={i18n.formatMessage({
                            id: 'remove_account_from_this_page'
                          })}
                        >
                          <IconButton
                            size="smallest"
                            onClick={() => removeAccount(user)}
                            className="closeBtn"
                            sx={{
                              visibility: 'hidden',
                              position: 'absolute',
                              right: -4,
                              transform: 'scale(0.8)',
                              top: 0,
                              backgroundColor: `${theme.palette.background.paper} !important`,
                              color: theme.palette.text.secondary,
                              padding: '0 !important'
                            }}
                          >
                            <LineIcon icon="ico-close" />
                          </IconButton>
                        </Tooltip>
                      </Box>
                    ))}
                    <Tooltip
                      title={i18n.formatMessage({ id: 'add_new_account' })}
                    >
                      <IconButton
                        color="primary"
                        aria-label="add more"
                        onClick={addMoreAccount}
                        sx={{ width: 64, height: 64, border: '1px solid' }}
                      >
                        <LineIcon icon="ico-plus" />
                      </IconButton>
                    </Tooltip>
                  </Box>
                </div>
              ) : null}
            </div>
          </Grid>
          <Grid className={classes.formContent} item xs={12} md={6}>
            <Box sx={{ display: 'block', width: '100%' }}>
              <Typography variant="h2" align="center" className={classes.title}>
                {i18n.formatMessage({ id: 'login_welcome_back' })}
              </Typography>
              {testAccount?.enabled && testAccount.email ? (
                <Typography variant="body2" paragraph align="center">
                  Test Account: <b>{testAccount.email}</b>
                  <b>/{testAccount.password}</b>
                </Typography>
              ) : null}
              <LoginForm user={user} />
            </Box>
          </Grid>
        </Grid>
        {React.createElement(FooterMenu, { color: 'inherit' })}
      </Container>
    </div>
  );
}

export default createBlock({
  extendBlock: LoginContent,
  defaults: {
    title: 'Login Content'
  }
});

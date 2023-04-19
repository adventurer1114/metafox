/**
 * @type: route
 * name: user.logout
 * path: /logout
 * bundle: web
 */
import { useGlobal } from '@metafox/framework';

export default function LogoutPage() {
  const { redirectTo, cookieBackend, getSetting } = useGlobal();
  const redirect_after_logout = getSetting('user.redirect_after_logout');
  const token = cookieBackend.get('token');

  if (token) {
    cookieBackend.remove('token');
    cookieBackend.remove('refreshToken');
    cookieBackend.remove('dateExpiredToken');
    cookieBackend.remove('fcm-notification');
    // unregister all service worker when logout
    try {
      navigator.serviceWorker.getRegistrations().then(registrations => {
        for (const registration of registrations) {
          registration.unregister();
        }
      });
    } catch (err) {}
  }

  const admincp = process.env.MFOX_BUILD_TYPE === 'admincp';
  const baseUrl = process.env.PUBLIC_URL;

  const redirectUrl = admincp ? `${baseUrl}/admincp` : baseUrl;

  redirectTo(redirect_after_logout || redirectUrl || '/');

  return null;
}

/**
 * @type: reducer
 * name: user
 */

import {
  combineReducers,
  createEntityReducer,
  createUIReducer
} from '@metafox/framework';
import accountSettings from './accountSettings';
import emailNotificationSettings from './emailNotificationSettings';
import invisibleSettings from './invisibleSettings';
import profileMenu from './profileMenu';
import profilePrivacy from './profilePrivacy';
import sharingItemPrivacy from './sharingItemPrivacy';
import uiConfig from './uiConfig';
import verifyEmail from './verifyEmail';
import paymentSettings from './payment';
import notificationSettings from './notificationSetting';
import multiFactorAuthSettings from './multiFactorAuth';

const appName = 'user';

export default combineReducers({
  entities: createEntityReducer(appName),
  uiConfig: createUIReducer(appName, uiConfig),
  accountSettings,
  paymentSettings,
  multiFactorAuthSettings,
  invisibleSettings,
  emailNotificationSettings,
  sharingItemPrivacy,
  profilePrivacy,
  profileMenu,
  verifyEmail,
  notificationSettings
});

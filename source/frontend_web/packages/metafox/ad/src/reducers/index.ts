/**
 * @type: reducer
 * name: ad
 */

import {
  combineReducers,
  createEntityReducer,
  createUIReducer
} from '@metafox/framework';

const appName = 'user';

export default combineReducers({
  entities: createEntityReducer(appName),
  uiConfig: createUIReducer(appName, {})
});

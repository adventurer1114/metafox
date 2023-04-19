export const FORM_SUBMIT = '@form/SUBMIT';

export const MFOX_LOCALE = process.env.MFOX_LOCALE;

export const MFOX_BUILD_TYPE = process.env.MFOX_BUILD_TYPE || 'web';

export const IS_ADMINCP = MFOX_BUILD_TYPE === 'admincp' ? 1 : 0;

export const RTL_LOCALE = [
  'ar',
  'arc',
  'arz',
  'dv',
  'fa',
  'ha',
  'he',
  'khw',
  'ks',
  'ku',
  'ps',
  'sd',
  'ur',
  'uz_AF',
  'yi'
];

export const BUNDLE_DIR = `bundle-${MFOX_BUILD_TYPE}`;

export const FORM_SEARCH_SUBMIT = '@form/search/SUBMIT';

export const THEME_KEY = IS_ADMINCP ? 'themeId1' : 'themeId';

export const DEFAULT_THEME = IS_ADMINCP ? 'admincp:admincp' : 'a0:a0';

export const FORM_ADMIN_SEARCH_SUBMIT = '@formAdmin/search/SUBMIT';

export const ABORT_CONTROL_CANCEL = '@abortControl/cancel';

export const ABORT_CONTROL_START = '@abortControl/start';

export const FETCH_DETAIL = '@fetchDetail';

export const ENTITY_REFRESH = '@entity/REFRESH';

export const ENTITY_DELETE = '@entity/DELETE';

export const ENTITY_PATCH = '@entity/PATCH';

export const ENTITY_PUT = '@entity/PUT';

export const ENTITY_FULFILL = '@entity/FULFILL';

export const PAGINATION = '@pagination';

export const PAGINATION_UPDATE_LAST_READ: string =
  '@pagination/UPDATE_LAST_READ';

export const PAGINATION_MODIFIED = '@pagination/modified';

export const PAGINATION_INIT: string = '@pagination/INIT';

export const PAGINATION_SUCCESS = '@pagination/SUCCESS';

export const PAGINATION_UNSHIFT = '@pagination/UNSHIFT';

export const PAGINATION_PUSH = '@pagination/PUSH';

export const PAGINATION_PUSH_INDEX = '@pagination/PUSH_INDEX';

export const PAGINATION_START = '@pagination/START';

export const PAGINATION_DELETE = '@pagination/DELETE';

export const PAGINATION_REFRESH = '@pagination/REFRESH';

export const PAGINATION_FULFILL_PAGE = '@pagination/FULFILL/PAGE';

export const PAGINATION_FAILED = '@pagination/FAILED';

export const PAGINATION_CLEAR = '@pagination/CLEAR';

export const PAGINATION_UN_LIST = '@pagination/UN_LIST';

export const PAGINATION_RESET_ALL = '@pagination/resetAll';

export const LOGGED_OUT = 'session/logout';

export const REFRESH_TOKEN = 'session/refresh_token';

export const STRATEGY_REFRESH_TOKEN = 'session/strategy_refresh_token';

export const ACTION_UPDATE_TOKEN = 'session/updateToken';

export const CLOSE_MENU = 'closeMenu';

export const APP_BOOTSTRAP = '@bootstrap';

export const APP_BOOTSTRAP_DONE = '@bootstrap/DONE';

export const RELOAD_USER = '@reloadUser';

export const LAYOUT_EDITOR_TOGGLE = '@layout/toggleEditor';

export const LS_AUTH_NAME = 'authUser';

export const LS_GUEST_NAME = 'authGuest';

export const LS_ACCOUNT_NAME = 'accounts';

/**
 * load page meta data action name
 */
export const LOAD_PAGE_META = '@core/loadPageMeta';

/**
 * Default locale group to load from frontend
 * etc: /api/v1/core/translation/{LOCALE_GROUP}/en
 */
export const LOCALE_GROUP = 'web';

export const APP_SERVICE_CONTEXT = 'useGlobal';

export const CACHE_SETTING_KEY = IS_ADMINCP ? 'settings.admin' : 'settings';

/**
 * Load data from prefer remote server when bootrap.
 */
export const USE_BOOTSTRAP_CACHE = true;

export const GET_STATICS = '@getStatics';

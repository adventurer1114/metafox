/**
 * @type: service
 * name: preferenceBackend
 * priority: -9
 */
import {
  DEFAULT_THEME,
  Manager,
  THEME_KEY,
  UserPreferenceConfig
} from '@metafox/framework';
import { ThemeType } from '@metafox/layout';
import { get, set } from 'lodash';

export class UserPreferenceBackend {
  private data: UserPreferenceConfig;

  private manager: Manager;

  public static readonly configKey: string = 'settings';

  constructor(data: UserPreferenceConfig) {
    this.data = data ?? ({} as any);
  }
  [key: string]: any;

  bootstrap(manager: Manager) {
    this.manager = manager;

    const isLayoutPreviewWindow = manager.constants?.isLayoutPreviewWindow;
    const topWindow = window?.top as any;
    const selfWindow = window as any;
    // mix data to this services.

    if (isLayoutPreviewWindow && topWindow.preferenceBackend) {
      return (selfWindow.preferenceBackend = topWindow.preferenceBackend);
    }

    const cookieBackend = manager.cookieBackend;

    const themeType = cookieBackend.get('themeType') as unknown as ThemeType;

    this.data.previewDevice = cookieBackend.get('previewDevice') || '';
    this.data.userLanguage = cookieBackend.get('userLanguage');
    this.data.themeType = themeType ?? 'auto';

    const themeId = cookieBackend.get(THEME_KEY) || '';
    this.data[THEME_KEY] = /(.+):(.+)/i.test(themeId) ? themeId : DEFAULT_THEME;

    this.notifyChanged = this.notifyChanged.bind(this);

    return this;
  }

  public get(name: string, value: any = undefined) {
    return get(this.data, name, value);
  }

  public set(key: string, value: any): void {
    if ('function' === typeof value) {
      const prev = this.get(key);
      value = value(prev);
    }

    set(this.data, key, value);

    this.notifyChanged(Object.assign({}, this.data));
  }

  public getAll(): UserPreferenceConfig {
    return this.data;
  }

  private notifyChanged(obj: UserPreferenceConfig) {
    if (this.manager?.eventCenter)
      this.manager.eventCenter.dispatch('onUserPreferenceChanged', obj);
  }

  public toggleDarkMode = () => {
    this.setAndRemember('themeType', prev => {
      return prev === 'dark' ? 'light' : 'dark';
    });
  };

  public setThemeType = (themeType: 'light' | 'dark' | 'auto') => {
    this.setAndRemember('themeType', themeType);
  };

  private remember(key: string, value: any) {
    if (this.manager?.cookieBackend)
      this.manager.cookieBackend.set(key.toString(), value.toString());
  }

  public setAndRemember(key: string, value: any) {
    if ('function' === typeof value) {
      const prev = this.get(key);
      value = value(prev);
    }

    this.remember(key, value);
    this.set(key, value);
  }

  public getTheme(): string {
    return get(this.data, THEME_KEY);
  }

  public setTheme(value: string): void {
    this.setAndRemember(THEME_KEY, value);
  }

  public leavePreviewMode() {}
}

export default UserPreferenceBackend;

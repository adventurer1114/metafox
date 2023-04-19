import uniqMetaName from './uniqMetaName';

type Typed = string | null | false | undefined;

export default function normalizePageName(
  appName: Typed,
  resourceName: Typed,
  action: Typed,
  category?: Typed,
  suffix?: Typed
): string {
  const prefix = process.env.MFOX_BUILD_TYPE === 'admincp' ? 'admin.' : '';

  const name = [action, category, resourceName ? resourceName : appName, suffix]
    .filter(Boolean)
    .join('_');

  return uniqMetaName(`${prefix}${appName}.${name}`);
}

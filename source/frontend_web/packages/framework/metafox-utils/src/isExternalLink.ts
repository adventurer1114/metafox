/**
 *
 * @param {String} url - Url
 * @returns
 */
export default function isExternalLink(url: string): boolean {
  const base = new URL(`${window.location.protocol}//${window.location.host}`);

  return new URL(url, base).hostname !== base.hostname;
}
